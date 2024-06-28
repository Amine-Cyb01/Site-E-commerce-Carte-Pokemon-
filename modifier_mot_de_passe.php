<?php
// Établir une connexion à la base de données
$servername = "localhost"; // Nom du serveur MySQL
$username = "root"; // Nom d'utilisateur MySQL
$password = "12345678"; // Mot de passe MySQL
$dbname = "projet_final_web"; // Nom de la base de données

session_start(); // Démarrage de la session PHP

$conn = new mysqli($servername, $username, $password, $dbname); // Connexion à la base de données MySQL
if ($conn->connect_error) {
    die("Échec de la connexion à la base de données : " . $conn->connect_error); // Arrête l'exécution si la connexion échoue
}

$profil = []; // Initialisation du tableau de profil
$estConnecte = false; // Initialisation de la variable indiquant l'état de connexion de l'utilisateur

// Vérifier l'authentification de l'utilisateur
if(isset($_SESSION["username"])) {
    $estConnecte = true; // L'utilisateur est connecté
    $profil = [$_SESSION["prenom"], $_SESSION["nom"]]; // Profil de l'utilisateur à partir des données de session
}

// Vérifier si le formulaire de mise à jour de mot de passe est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification des champs soumis
    $mot_de_passe = $_POST["mot_de_passe"]; // Ancien mot de passe saisi
    $new_mot_de_passe = $_POST["new_mot_de_passe"]; // Nouveau mot de passe saisi

    $mot_de_passe = mysqli_real_escape_string($conn, $mot_de_passe); // Échapper les caractères spéciaux pour éviter les injections SQL
    $new_mot_de_passe = mysqli_real_escape_string($conn, $new_mot_de_passe); // Échapper les caractères spéciaux pour éviter les injections SQL

    // Récupérer le mot de passe haché de la base de données
    if ($estConnecte) {
        $user_id = $_SESSION["user_id"]; // ID de l'utilisateur à partir de la session
        $query = "SELECT mot_de_passe FROM utilisateurs WHERE id = '$user_id'"; // Requête pour récupérer le mot de passe haché
        $result = mysqli_query($conn, $query); // Exécution de la requête SQL
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result); // Récupération de la ligne de résultat
            $mot_de_passe_hash = $row["mot_de_passe"]; // Récupération du mot de passe haché de la base de données
        }
    }

    // Vérifier l'ancien mot de passe
    if (isset($mot_de_passe_hash) && password_verify($mot_de_passe, $mot_de_passe_hash)) {
        $erreurs = array(); // Initialisation du tableau d'erreurs de validation

        // Validation du nouveau mot de passe avec une expression régulière
        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{8,}$/", $new_mot_de_passe)) {
            $erreurs["new_mot_de_passe"] = "Le mot de passe doit contenir au moins 8 caractères, dont au moins une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial.";
        }

        // Si aucune erreur de validation n'est trouvée, procéder à la mise à jour dans la base de données
        if (count($erreurs) == 0) {
            // Hacher le mot de passe avant de l'insérer dans la base de données
            $mot_de_passe_hash = password_hash($new_mot_de_passe, PASSWORD_DEFAULT); // Hachage du nouveau mot de passe

            // Mettre à jour les données valides dans la base de données
            $sql = "UPDATE utilisateurs SET mot_de_passe = '$mot_de_passe_hash' WHERE id = '$user_id'"; // Requête SQL pour mettre à jour le mot de passe

            if ($conn->query($sql) === TRUE) {
                $_SESSION['flash_message'] = "Votre mot de passe a bien été mis à jour."; // Message de succès stocké dans la session
                header("Location: profil.php"); // Redirection vers la page de profil après la modification
                exit(); // Arrête l'exécution du script
            } else {
                echo "Erreur lors de la modification du mot de passe : " . $conn->error; // Affichage d'une erreur si la mise à jour échoue
            }
        }
    } else {
        // Afficher un message d'erreur si l'ancien mot de passe est incorrect
        $erreurs["mot_de_passe"] = "Mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Mettre à jour mon mot de passe</title>
</head>

<body>
    <div class="container">
        <h1>Mettre à jour mon mot de passe</h1>
        <?php
        require 'fonctions.php'; // Inclure les fonctions PHP
        afficherBarreNavigation($profil); // Afficher la barre de navigation avec le profil de l'utilisateur
        ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="container-inscription-externe">
                <div class="container-inscription-interne">
                    <h2>Saisissez votre ancien mot de passe</h2>

                    <!-- Champ de saisie pour l'ancien mot de passe -->
                    <input type="password" name="mot_de_passe" id="mot_de_passe" required placeholder="Ancien mot de passe" class="input-field"><br>
                    <?php if (isset($erreurs["mot_de_passe"])) { echo "<span class='erreur'>" . $erreurs["mot_de_passe"] . "</span><br>"; } ?>

                    <h2>Saisissez votre nouveau mot de passe</h2>

                    <!-- Champ de saisie pour le nouveau mot de passe -->
                    <input type="password" name="new_mot_de_passe" id="new_mot_de_passe" required placeholder="Nouveau mot de passe" class="input-field"><br>
                    <?php if (isset($erreurs["new_mot_de_passe"])) { echo "<span class='erreur'>" . $erreurs["new_mot_de_passe"] . "</span><br>"; } ?>
                </div>
            </div>

            <!-- Bouton pour soumettre le formulaire de mise à jour du mot de passe -->
            <br><input type="submit" value="Modifier mon mot de passe" class="submit-button">
        </form>
        
        <!-- Bouton d'annulation pour revenir à la page de profil -->
        <button onclick="window.location.href='profil.php'" class="submit-button annuler">Annuler</button>
    </div>
</body>
</html>
