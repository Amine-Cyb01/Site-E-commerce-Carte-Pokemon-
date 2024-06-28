<?php
session_start(); // Démarre une nouvelle session ou reprend une session existante

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Connexion à la base de données
    $conn = mysqli_connect("localhost", "root", "12345678", "projet_final_web");

    // Vérifier si la connexion a réussi
    if (!$conn) {
        die("Erreur de connexion à la base de données: " . mysqli_connect_error());
    }

    // Échapper les caractères spéciaux pour éviter les attaques de type injections SQL
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Requête pour obtenir l'utilisateur par son email
    $query = "SELECT * FROM utilisateurs WHERE email = '$username'";
    $result = mysqli_query($conn, $query);

    // Vérifier si la requête a renvoyé un résultat
    if (mysqli_num_rows($result) == 1) {
        // Récupérer les informations de l'utilisateur
        $row = mysqli_fetch_assoc($result);
        $user_id = $row["id"];
        $prenom = $row["prenom"];
        $nom = $row["nom"];
        $estVendeur = $row["estVendeur"];
        $mot_de_passe_hash = $row["mot_de_passe"];

        // Vérifier le mot de passe
        if (password_verify($password, $mot_de_passe_hash)) {
            // Stocker les informations de l'utilisateur dans la session
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $username;
            $_SESSION["prenom"] = $prenom;
            $_SESSION["nom"] = $nom;
            $_SESSION["estVendeur"] = $estVendeur;

            // Rediriger vers la page de succès
            header("Location: profil.php");
            exit;
        } else {
            // Afficher un message d'erreur
            $error = "Identifiant ou mot de passe incorrect";
        }
    } else {
        // Afficher un message d'erreur
        $error = "Identifiant ou mot de passe incorrect";
    }

    // Fermer la connexion à la base de données
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS pour le style -->
    <title>Page de connexion</title> <!-- Titre de la page -->
</head>

    <?php
        require 'fonctions.php'; // Inclut le fichier 'fonctions.php' qui contient des fonctions utilitaires

        if(isset($_SESSION["username"])) { // Vérifie si l'utilisateur est connecté
            $estConnecte = true; // Indique que l'utilisateur est connecté
            $profil = [$_SESSION["prenom"], $_SESSION["nom"]]; // Récupère le prénom et le nom de l'utilisateur pour le profil
        } else {
            $estConnecte = false; // Indique que l'utilisateur n'est pas connecté
            $profil = []; // Initialise un tableau vide pour le profil
        }
        afficherBarreNavigation($profil); // Appelle une fonction pour afficher la barre de navigation avec le profil de l'utilisateur
    ?>

<body>
    <div class="container">
        <?php
            if (isset($_SESSION['flash_message'])) { // Vérifie s'il y a un message flash dans la session
                echo "<h3>" .$_SESSION['flash_message'] . "</h3>"; // Affiche le message flash
                unset($_SESSION['flash_message']); // Supprime le message flash de la session
            }
        ?>

        <h1>Connexion</h1> <!-- Titre principal de la page -->

        <?php if (isset($error)) { ?>
            <p><?php echo $error; ?></p> <!-- Affiche un message d'erreur s'il y en a un -->
        <?php } ?>

        <form method="POST" action="">
            <input type="email" id="username" name="username" placeholder="Adresse email" required class="input-field"><br> <!-- Champ pour l'adresse email -->
            <input type="password" id="password" name="password" placeholder="Mot de passe" required class="input-field"><br> <!-- Champ pour le mot de passe -->

            <br><input type="submit" value="Se connecter" class="submit-button"> <!-- Bouton pour soumettre le formulaire -->
        </form>
        <br>
        
        <p>Vous n'avez pas de compte ? <a href="inscription.php">Inscrivez-vous ici !</a></p> <!-- Lien pour s'inscrire -->
    </div>
    
</body>
</html>
