<?php
session_start(); // Démarre une nouvelle session ou reprend une session existante

require 'fonctions.php'; // Inclut le fichier 'fonctions.php' qui contient des fonctions utilitaires

// Connexion à la base de données
$conn = mysqli_connect("localhost", "root", "12345678", "projet_final_web"); // Établit une connexion à la base de données MySQL avec les paramètres donnés

// Vérifier si la connexion a réussi
if (!$conn) { // Vérifie si la connexion a échoué
    die("Erreur de connexion à la base de données: " . mysqli_connect_error()); // Arrête l'exécution du script et affiche un message d'erreur
}

if(isset($_SESSION["username"])) { // Vérifie si l'utilisateur est connecté en vérifiant l'existence de la variable de session 'username'
    $estConnecte = true; // Définit une variable indiquant que l'utilisateur est connecté
    $profil = [$_SESSION["prenom"], $_SESSION["nom"]]; // Récupère le prénom et le nom de l'utilisateur dans la session
} else { // Si l'utilisateur n'est pas connecté
    $estConnecte = false; // Définit une variable indiquant que l'utilisateur n'est pas connecté
    $profil = []; // Initialise un tableau vide pour le profil
}

afficherBarreNavigation($profil); // Appelle une fonction pour afficher la barre de navigation en passant le profil de l'utilisateur

$featuredProducts = getFeaturedProducts($conn); // Récupère les produits en vedette depuis la base de données en utilisant une fonction personnalisée
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS pour le style de la page -->
    <title>Page d'accueil</title> <!-- Titre de la page -->
</head>
<body>

    <div class="container-accueil"> <!-- Div contenant l'ensemble de la page d'accueil -->
        <div class="hero-container"> <!-- Div contenant la bannière principale -->
            <img src="images/site/banner.png" alt="bannière pokémon"> <!-- Image de la bannière -->
            <div class="hero-banner"> <!-- Div pour le contenu de la bannière -->
                <img src="images/site/logo.png" alt="logo PokéShop"> <!-- Logo du site -->
                <h1>Bienvenue chez PokéShop, la boutique des passionés de cartes Pokémon</h1> <!-- Titre de bienvenue -->
                <a href="articles.php"><button class="bouton-accueil">Voir toutes les cartes</button></a> <!-- Bouton pour voir tous les articles -->
            </div>
        </div>

        <h2>Nos produits en vedette</h2> <!-- Titre pour la section des produits en vedette -->
        <div class="featured-products"> <!-- Div contenant les produits en vedette -->
            <?php
                foreach ($featuredProducts as $product) { // Boucle sur chaque produit en vedette
                    echo "<div class=\"product_accueil\">"; // Div pour un produit en vedette
                    echo "<a href=\"details_article.php?id={$product['id']}\">"; // Lien vers la page de détails du produit
                    echo "<img src=\"{$product['lien_photo']}\" alt=\"{$product['nom']}\">"; // Image du produit
                    echo "<h3>{$product['nom']}</h3>"; // Nom du produit
                    echo "<p>{$product['description']}</p>"; // Description du produit
                    echo "<span class=\"price\">{$product['prix']} €</span>"; // Prix du produit
                    echo "</a>"; // Fermeture du lien
                    echo "</div>"; // Fermeture de la div du produit
                }
            ?>
        </div>
    </div>

</body>
</html>
