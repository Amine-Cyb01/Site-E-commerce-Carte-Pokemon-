<?php
session_start(); // Démarre une nouvelle session ou reprend une session existante

require 'fonctions.php'; // Inclut le fichier 'fonctions.php' qui contient des fonctions utilitaires

if(isset($_SESSION["username"])) { // Vérifie si l'utilisateur est connecté
    $estConnecte = true; // Indique que l'utilisateur est connecté
    $profil = [$_SESSION["prenom"], $_SESSION["nom"]]; // Récupère le prénom et le nom de l'utilisateur pour le profil
} else {
    $estConnecte = false; // Indique que l'utilisateur n'est pas connecté
    $profil = []; // Initialise un tableau vide pour le profil
}

// Vérifier si un ID d'article est passé dans l'URL
if (isset($_GET['id'])) {
    // Connexion à la base de données
    $conn = mysqli_connect("localhost", "root", "12345678", "projet_final_web"); // Connexion à la base de données
    if (!$conn) {
        die("Erreur de connexion à la base de données: " . mysqli_connect_error()); // Si la connexion échoue, afficher un message d'erreur et arrêter le script
    }

    $idArticle = $_GET['id']; // Récupère l'ID de l'article depuis l'URL

    $sql = "SELECT * FROM articles WHERE id = $idArticle"; // Requête SQL pour obtenir les détails de l'article

    $result = mysqli_query($conn, $sql); // Exécute la requête
    if (!$result) {
        die("Erreur lors de la récupération des détails de l'article: " . mysqli_error($conn)); // Si la requête échoue, afficher un message d'erreur et arrêter le script
    }

    if (mysqli_num_rows($result) == 1) { // Vérifie si un article a été trouvé
        $row = mysqli_fetch_assoc($result); // Récupère les informations de l'article
        $nomArticle = $row["nom"]; // Nom de l'article
        $description = $row["description"]; // Description de l'article
        $prix = $row["prix"]; // Prix de l'article
        $etat = $row["etat"]; // État de l'article
        $stock = $row["stock"]; // Stock de l'article
        $lienPhoto = $row["lien_photo"]; // Lien de la photo de l'article
    } else {
        echo "Article non trouvé."; // Si aucun article n'a été trouvé, afficher un message
    }
} else {
    echo "ID de l'article non spécifié."; // Si aucun ID d'article n'a été passé dans l'URL, afficher un message
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> <!-- Définition de l'encodage des caractères -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Adaptation de la mise en page pour les petits écrans -->
    <title>Carte de produit</title> <!-- Titre de la page -->
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS pour le style -->
</head>
<body>
    <?php
        afficherBarreNavigation($profil); // Appelle une fonction pour afficher la barre de navigation avec le profil de l'utilisateur
    ?>
    
    <div class="centrer"> <!-- Conteneur pour centrer le contenu -->
        <div class="un_article_en_vente"> <!-- Conteneur pour un article en vente -->
            <div class="image_et_info"> <!-- Conteneur pour l'image et les informations de l'article -->
                <img src="<?php echo $lienPhoto; ?>" alt="<?php echo $nomArticle; ?>"> <!-- Affiche l'image de l'article -->
                <div class="info_un_article_en_vente"> <!-- Conteneur pour les informations de l'article -->
                    <h2><?php echo $nomArticle; ?></h2> <!-- Affiche le nom de l'article -->
                    <p>Description : <?php echo $description; ?></p> <!-- Affiche la description de l'article -->
                    <p>Prix : <?php echo $prix; ?> €</p> <!-- Affiche le prix de l'article -->
                    <p>État : <?php echo $etat; ?></p> <!-- Affiche l'état de l'article -->
                    <p>Stock : <?php echo $stock; ?></p> <!-- Affiche le stock de l'article -->
                </div>
            </div>
            <div class="boutons"> <!-- Conteneur pour les boutons -->
                <button class="button" onclick="location.href='acheter.php?id=<?php echo $idArticle; ?>'">Acheter</button> <!-- Bouton pour acheter l'article -->
                <button class="button" onclick="location.href='accueil.php'">Retour à l'accueil</button> <!-- Bouton pour retourner à l'accueil -->
            </div>
        </div>
    </div>
</body>
</html>

