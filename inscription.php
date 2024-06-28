<?php
// Définition des paramètres de connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "projet_final_web";

// Démarrage de la session PHP
session_start();

// Connexion à la base de données MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    // Arrêt du script si la connexion échoue
    die("Échec de la connexion à la base de données : " . $conn->connect_error);
}

// Vérification si le formulaire d'inscription a été soumis (méthode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des valeurs saisies dans le formulaire
    $prenom = $_POST["prenom"];
    $nom = $_POST["nom"];
    $email = $_POST["email"];
    $mot_de_passe = $_POST["mot_de_passe"];
    $adresse = $_POST["adresse"];
    $code_postal = $_POST["code_postal"];
    $ville = $_POST["ville"];
    $pays = $_POST["pays"];

    // Tableau pour stocker les erreurs de validation
    $erreurs = array();

    // Validation des champs du formulaire
    if (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/u", $nom)) {
        $erreurs["nom"] = "Le nom n'est pas valide.";
    }
    if (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/u", $prenom)) {
        $erreurs["prenom"] = "Le prénom n'est pas valide.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs["email"] = "L'email n'est pas valide.";
    }
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{8,}$/", $mot_de_passe)) {
        $erreurs["mot_de_passe"] = "Le mot de passe doit contenir au moins 8 caractères, dont au moins une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial.";
    }
    if (!preg_match("/^[a-zA-ZÀ-ÿ0-9-'\s]+$/", $adresse)) {
        $erreurs["adresse"] = "L'adresse n'est pas valide.";
    }
    if (!preg_match("/^[0-9]{5}$/", $code_postal)) {
        $erreurs["code_postal"] = "Le code postal n'est pas valide.";
    }
    if (!preg_match("/^[a-zA-ZÀ-ÿ'\s-]+$/", $ville)) {
        $erreurs["ville"] = "La ville n'est pas valide.";
    }
    if (!preg_match("/^[a-zA-ZÀ-ÿ'\s-]+$/", $pays)) {
        $erreurs["pays"] = "Le pays n'est pas valide.";
    }

    // Vérification si l'email existe déjà dans la base de données
    $sql = "SELECT id FROM utilisateurs WHERE email='$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $erreurs["email"] = "Cet email est déjà utilisé.";
    }

    // Si aucune erreur de validation n'est trouvée, procéder à l'insertion des données dans la base de données
    if (count($erreurs) == 0) {
        // Hachage du mot de passe avant de l'insérer dans la base de données
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // Préparation de la requête SQL pour l'insertion des données dans la table utilisateurs
        $sql = "INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe, adresse, code_postal, ville, pays) VALUES ('$prenom', '$nom', '$email', '$mot_de_passe_hash', '$adresse', '$code_postal', '$ville', '$pays')";

        // Exécution de la requête SQL d'insertion
        if ($conn->query($sql) === TRUE) {
            $_SESSION['flash_message'] = "Félicitations, votre inscription est terminée ! Vous pouvez maintenant vous connecter."; // Message de réussite stocké dans une variable de session
            header("Location: connexion.php"); // Redirection vers la page de connexion après l'inscription
            exit(); // Arrêt du script pour éviter toute exécution supplémentaire
        } else {
            echo "Erreur lors de l'inscription : " . $conn->error; // Affichage d'une erreur si l'insertion échoue
        }
    }
}

// Fermeture de la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Page d'inscription</title>
</head>

    <?php
        // Inclusion du fichier de fonctions pour afficher la barre de navigation
        require 'fonctions.php';

        // Vérification si l'utilisateur est connecté pour afficher la barre de navigation appropriée
        if(isset($_SESSION["username"])) {
            $estConnecte = true;
            $profil = [$_SESSION["prenom"], $_SESSION["nom"]];
        } else {
            $estConnecte = false;
            $profil = [];
        }
        afficherBarreNavigation($profil); // Appel de la fonction pour afficher la barre de navigation
    ?>

<body>
    <div class="container">
        <h1>Inscription</h1>
        <!-- Formulaire d'inscription -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="container-inscription-externe">
                <div class="container-inscription-interne">
                    <h2>Informations personnelles</h2>

                    <input type="text" name="prenom" id="prenom" required placeholder="Prénom" class="input-field"><br>
                    <?php if (isset($erreurs["prenom"])) { echo "<span class='erreur'>" . $erreurs["prenom"] . "</span><br>"; } ?>

                    <input type="text" name="nom" id="nom" required placeholder="Nom" class="input-field"><br>
                    <?php if (isset($erreurs["nom"])) { echo "<span class='erreur'>" . $erreurs["nom"] . "</span><br>"; } ?>

                    <input type="email" name="email" id="email" required placeholder="Email" class="input-field"><br>
                    <?php if (isset($erreurs["email"])) { echo "<span class='erreur'>" . $erreurs["email"] . "</span><br>"; } ?>

                    <input type="password" name="mot_de_passe" id="mot_de_passe" required placeholder="Mot de passe" class="input-field"><br>
                    <?php if (isset($erreurs["mot_de_passe"])) { echo "<span class='erreur'>" . $erreurs["mot_de_passe"] . "</span><br>"; } ?>
                </div>

                <div class="container-inscription-interne">
                    <h2>Adresse</h2>

                    <input type="text" name="adresse" id="adresse" required placeholder="Adresse" class="input-field"><br>
                    <?php if (isset($erreurs["adresse"])) { echo "<span class='erreur'>" . $erreurs["adresse"] . "</span><br>"; } ?>

                    <input type="text" name="code_postal" id="code_postal" required placeholder="Code postal" class="input-field"><br>
                    <?php if (isset($erreurs["code_postal"])) { echo "<span class='erreur'>" . $erreurs["code_postal"] . "</span><br>"; } ?>

                    <input type="text" name="ville" id="ville" required placeholder="Ville" class="input-field"><br>
                    <?php if (isset($erreurs["ville"])) { echo "<span class='erreur'>" . $erreurs["ville"] . "</span><br>"; } ?>

                    <input type="text" name="pays" id="pays" required placeholder="Pays" class="input-field"><br>
                    <?php if (isset($erreurs["pays"])) { echo "<span class='erreur'>" . $erreurs["pays"] . "</span><br>"; } ?>
                </div>
            </div>

            <br><input type="submit" value="S'inscrire" class="submit-button">
        </form>
    </div>
    
</body>
</html>
