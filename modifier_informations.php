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

// Récupérer les informations personnelles de l'utilisateur connecté
if ($estConnecte) {
    $user_id = $_SESSION["user_id"];
    // Requête SQL pour récupérer le prénom et le nom de l'utilisateur à partir de son ID
    $sql = "SELECT prenom, nom FROM utilisateurs WHERE id = $user_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // Récupérer les données de l'utilisateur
        $row = $result->fetch_assoc();
        $prenom = $row["prenom"];
        $nom = $row["nom"];
    }
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les valeurs saisies dans le formulaire
    $new_prenom = $_POST["new_prenom"];
    $new_nom = $_POST["new_nom"];

    $erreurs = array(); // Tableau pour stocker les erreurs de validation

    // Validation des champs du formulaire
    if (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/u", $new_nom)) {
        $erreurs["new_nom"] = "Le nom n'est pas valide.";
    }
    if (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/u", $new_prenom)) {
        $erreurs["new_prenom"] = "Le prénom n'est pas valide.";
    }

    // Si aucune erreur de validation n'est trouvée, procéder à la mise à jour dans la base de données
    if (count($erreurs) == 0) {
        // Requête SQL pour mettre à jour les données de l'utilisateur dans la base de données
        $sql = "UPDATE utilisateurs SET prenom = '$new_prenom', nom = '$new_nom' WHERE id = $user_id";
        $_SESSION["prenom"] = $new_prenom; // Mettre à jour le prénom dans la session
        $_SESSION["nom"] = $new_nom; // Mettre à jour le nom dans la session

        if ($conn->query($sql) === TRUE) {
            $_SESSION['flash_message'] = "Vos informations ont bien été mises à jour."; // Stockage du message de succès dans une variable de session
            header("Location: profil.php"); // Redirection vers la page de profil après la modification
            exit(); // Terminer le script pour éviter toute exécution supplémentaire
        } else {
            echo "Erreur lors de la modification des informations personnelles : " . $conn->error; // Affichage d'une erreur si la mise à jour échoue
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Mettre à jour mes informations personnelles</title>
</head>

<?php
require 'fonctions.php'; // Inclusion du fichier de fonctions pour la barre de navigation
afficherBarreNavigation($profil); // Appel de la fonction pour afficher la barre de navigation avec le profil de l'utilisateur
?>

<body>
    <div class="container">
        <h1>Mettre à jour mes informations personnelles</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="container-inscription-externe">
                <div class="container-inscription-interne">
                    <h2>Saisissez vos nouvelles informations personnelles</h2>

                    <!-- Champs de saisie pour le prénom et le nom -->
                    <input type="text" name="new_prenom" id="new_prenom" required placeholder="Prénom" value="<?php echo $prenom?>" class="input-field"><br>
                    <?php if (isset($erreurs["new_prenom"])) { echo "<span class='erreur'>" . $erreurs["new_prenom"] . "</span><br>"; } ?>

                    <input type="text" name="new_nom" id="new_nom" required placeholder="Nom" value="<?php echo $nom?>" class="input-field"><br>
                    <?php if (isset($erreurs["new_nom"])) { echo "<span class='erreur'>" . $erreurs["new_nom"] . "</span><br>"; } ?>
                </div>
            </div>

            <!-- Bouton pour soumettre le formulaire de mise à jour -->
            <br><input type="submit" value="Modifier mes informations" class="submit-button">
        </form>
        
        <!-- Bouton d'annulation pour revenir à la page de profil -->
        <button onclick="window.location.href='profil.php'" class="submit-button annuler">Annuler</button>
    </div>
</body>
</html>
