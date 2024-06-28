<?php
// Établir une connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "projet_final_web";

session_start(); // Démarrage de la session PHP

// Connexion à la base de données via MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    // Vérifier si la connexion à la base de données a échoué
    die("Échec de la connexion à la base de données : " . $conn->connect_error);
}

// Vérifier si l'utilisateur est connecté
if(isset($_SESSION["username"])) {
    $estConnecte = true;
    // Initialiser le profil de l'utilisateur avec son prénom et nom depuis la session
    $profil = [$_SESSION["prenom"], $_SESSION["nom"]];
} else {
    $estConnecte = false;
    $profil = [];
    // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: connexion.php");
    exit; // Arrêter le script pour éviter toute exécution supplémentaire
}

// Récupérer l'adresse de l'utilisateur connecté depuis la base de données
if ($estConnecte) {
    $user_id = $_SESSION["user_id"];
    // Requête SQL pour récupérer l'adresse de l'utilisateur à partir de son ID
    $sql = "SELECT adresse, code_postal, ville, pays FROM utilisateurs WHERE id = $user_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // Récupérer les données de l'utilisateur
        $row = $result->fetch_assoc();
        $adresse = $row["adresse"];
        $code_postal = $row["code_postal"];
        $ville = $row["ville"];
        $pays = $row["pays"];
    }
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les valeurs saisies dans le formulaire
    $new_adresse = $_POST["new_adresse"];
    $new_code_postal = $_POST["new_code_postal"];
    $new_ville = $_POST["new_ville"];
    $new_pays = $_POST["new_pays"];

    $erreurs = array(); // Tableau pour stocker les erreurs de validation

    // Validation des champs du formulaire
    if (!preg_match("/^[a-zA-ZÀ-ÿ0-9-'\s]+$/", $new_adresse)) {
        $erreurs["new_adresse"] = "L'adresse n'est pas valide.";
    }
    if (!preg_match("/^[0-9]{5}$/", $new_code_postal)) {
        $erreurs["new_code_postal"] = "Le code postal n'est pas valide.";
    }
    if (!preg_match("/^[a-zA-ZÀ-ÿ'\s-]+$/", $new_ville)) {
        $erreurs["new_ville"] = "La ville n'est pas valide.";
    }
    if (!preg_match("/^[a-zA-ZÀ-ÿ'\s-]+$/", $new_pays)) {
        $erreurs["new_pays"] = "Le pays n'est pas valide.";
    }

    // Si aucune erreur de validation n'est trouvée, procéder à la mise à jour dans la base de données
    if (count($erreurs) == 0) {
        // Requête SQL pour mettre à jour les données de l'utilisateur dans la base de données
        $sql = "UPDATE utilisateurs SET adresse = '$new_adresse', code_postal = '$new_code_postal', ville = '$new_ville', pays = '$new_pays' WHERE id = $user_id";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['flash_message'] = "Votre adresse a bien été modifiée."; // Stockage du message de succès dans une variable de session
            header("Location: profil.php"); // Redirection vers la page de profil après la modification
            exit(); // Terminer le script pour éviter toute exécution supplémentaire
        } else {
            echo "Erreur lors de la modification de l'adresse : " . $conn->error; // Affichage d'une erreur si la mise à jour échoue
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Mettre à jour mon adresse</title>
</head>

<?php
require 'fonctions.php'; // Inclusion du fichier de fonctions pour la barre de navigation
afficherBarreNavigation($profil); // Appel de la fonction pour afficher la barre de navigation avec le profil de l'utilisateur
?>

<body>
    <div class="container">
        <h1>Mettre à jour mon adresse</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="container-inscription-externe">
                <div class="container-inscription-interne">
                    <h2>Saisissez votre nouvelle adresse</h2>

                    <!-- Champs de saisie pour l'adresse, code postal, ville et pays -->
                    <input type="text" name="new_adresse" id="new_adresse" required placeholder="Adresse" value="<?php echo $adresse?>" class="input-field"><br>
                    <?php if (isset($erreurs["new_adresse"])) { echo "<span class='erreur'>" . $erreurs["new_adresse"] . "</span><br>"; } ?>

                    <input type="text" name="new_code_postal" id="new_code_postal" required placeholder="Code postal" value="<?php echo $code_postal?>" class="input-field"><br>
                    <?php if (isset($erreurs["new_code_postal"])) { echo "<span class='erreur'>" . $erreurs["new_code_postal"] . "</span><br>"; } ?>

                    <input type="text" name="new_ville" id="new_ville" required placeholder="Ville" value="<?php echo $ville?>" class="input-field"><br>
                    <?php if (isset($erreurs["new_ville"])) { echo "<span class='erreur'>" . $erreurs["new_ville"] . "</span><br>"; } ?>

                    <input type="text" name="new_pays" id="new_pays" required placeholder="Pays" value="<?php echo $pays?>" class="input-field"><br>
                    <?php if (isset($erreurs["new_pays"])) { echo "<span class='erreur'>" . $erreurs["new_pays"] . "</span><br>"; } ?>
                </div>
            </div>

            <!-- Bouton pour soumettre le formulaire de mise à jour -->
            <br><input type="submit" value="Modifier mon adresse" class="submit-button">
        </form>
        
        <!-- Bouton d'annulation pour revenir à la page de profil -->
        <button onclick="window.location.href='profil.php'" class="submit-button annuler">Annuler</button>
    </div>
</body>
</html>
