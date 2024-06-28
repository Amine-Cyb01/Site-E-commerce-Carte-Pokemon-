<?php
session_start(); // Démarre une nouvelle session ou reprend une session existante
require 'fonctions.php'; // Inclut le fichier 'fonctions.php' qui contient des fonctions utilitaires

if(isset($_SESSION["username"])) { // Vérifie si l'utilisateur est connecté en vérifiant l'existence de la variable de session 'username'
    $estConnecte = true; // Définit une variable indiquant que l'utilisateur est connecté
    $profil = [$_SESSION["prenom"], $_SESSION["nom"]]; // Récupère le prénom et le nom de l'utilisateur dans la session
} else { // Si l'utilisateur n'est pas connecté
    $estConnecte = false; // Définit une variable indiquant que l'utilisateur n'est pas connecté
    $profil = []; // Initialise un tableau vide pour le profil
}

// On vérifie si un ID d'article est passé dans l'URL
if (isset($_GET['id'])) {
    // Connexion à la base de données
    $conn = mysqli_connect("localhost", "root", "12345678", "projet_final_web"); // Établit une connexion à la base de données MySQL avec les paramètres donnés
    if (!$conn) { // Vérifie si la connexion a échoué
        die("Erreur de connexion à la base de données: " . mysqli_connect_error()); // Arrête l'exécution du script et affiche un message d'erreur
    }

    $idArticle = $_GET['id']; // On récupère l'ID de l'article depuis l'URL

    $sql = "SELECT * FROM articles WHERE id = $idArticle"; // Requête SQL pour récupérer les détails de l'article
    $result = mysqli_query($conn, $sql); // Exécute la requête SQL et stocke le résultat
    if (!$result) { // Vérifie si la requête a échoué
        die("Erreur lors de la récupération des détails de l'article: " . mysqli_error($conn)); // Arrête l'exécution du script et affiche un message d'erreur
    }

    // On vérifie s'il y a un seul résultat
    if (mysqli_num_rows($result) == 1) { // Vérifie s'il y a exactement un résultat
        $row = mysqli_fetch_assoc($result); // Récupère la ligne de résultat sous forme de tableau associatif
        $nomArticle = $row["nom"]; // Récupère le nom de l'article
        $prix = $row["prix"]; // Récupère le prix de l'article
        $stock = $row["stock"]; // Récupère le stock disponible de l'article
        $lienPhoto = $row["lien_photo"]; // Récupère le lien de la photo de l'article
    } else { // Si l'article n'est pas trouvé
        echo "Article non trouvé."; // Affiche un message indiquant que l'article n'est pas trouvé
    }
} else { // Si aucun ID d'article n'est spécifié dans l'URL
    echo "ID de l'article non spécifié."; // Affiche un message indiquant que l'ID de l'article n'est pas spécifié
}

afficherBarreNavigation($profil); // Appelle une fonction pour afficher la barre de navigation en passant le profil de l'utilisateur
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> <!-- Spécifie le jeu de caractères pour le document -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Définit la fenêtre d'affichage pour rendre la page responsive -->
    <title>Acheter</title> <!-- Titre de la page -->
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS pour le style de la page -->
</head>
<body>
    <?php
        afficherBarreNavigation($profil); // Affiche à nouveau la barre de navigation
    ?>

    <div class="container"> <!-- Div contenant les détails de l'article et le formulaire d'achat -->
        <h2>Acheter <?php echo $nomArticle; ?></h2> <!-- Titre avec le nom de l'article -->
        <img src="<?php echo $lienPhoto; ?>" alt="<?php echo $nomArticle; ?>" class="product-image"> <!-- Image de l'article -->
        <p>Prix: <?php echo $prix; ?> €</p> <!-- Affiche le prix de l'article -->
        <p>Stock disponible: <?php echo $stock; ?></p> <!-- Affiche le stock disponible de l'article -->

        <form action="process_achat.php" method="post"> <!-- Formulaire pour traiter l'achat -->
            <label for="quantite">Quantité:</label> <!-- Étiquette pour le champ de quantité -->
            <input type="number" id="quantite" name="quantite" min="1" max="<?php echo $stock; ?>" required> <!-- Champ pour entrer la quantité à acheter -->
            <input type="hidden" name="id_article" value="<?php echo $idArticle; ?>"> <!-- Champ caché contenant l'ID de l'article -->
            <input type="submit" value="Acheter" class="custom-button"> <!-- Bouton pour soumettre le formulaire -->
        </form>
    </div>
</body>
</html>

