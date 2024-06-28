<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS pour le style de la page -->
    <title>Mes cartes en vente</title> <!-- Titre de la page -->
</head>

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
    header("Location: connexion.php"); // Redirige l'utilisateur vers la page de connexion
    exit; // Arrête l'exécution du script
}

if(!$_SESSION["estVendeur"] == 1) { // Vérifie si l'utilisateur est un vendeur en vérifiant la variable de session 'estVendeur'
    header("Location: profil.php"); // Redirige l'utilisateur vers sa page de profil
    exit; // Arrête l'exécution du script
}

afficherBarreNavigation($profil); // Appelle une fonction pour afficher la barre de navigation en passant le profil de l'utilisateur
?>

<body>

<h1>Mes cartes en vente</h1> <!-- Titre principal de la page -->
<div class="articles_en_vente"> <!-- Div contenant les articles en vente -->
    <?php
        // On récupère les articles en vente de l'utilisateur
        $user_id = $_SESSION["user_id"]; // Récupère l'ID de l'utilisateur connecté depuis la session
        $query = "SELECT * FROM articles
            WHERE vendeur_id = $user_id AND stock > 0"; // Requête SQL pour récupérer les articles en vente de l'utilisateur
        $result = mysqli_query($conn, $query); // Exécute la requête SQL et stocke le résultat

        // On vérifie si la requête a réussi
        if ($result) { // Si la requête a réussi
            // On affiche la liste des articles en vente
            while ($row = mysqli_fetch_assoc($result)) { // Boucle sur chaque ligne de résultat
                echo "<div class=\"un_article_en_vente\">"; // Div pour un article en vente
                echo "<div class=\"image_et_info\">"; // Div pour l'image et les informations de l'article
                echo "<img src=\"{$row['lien_photo']}\" alt=\"{$row['nom']}\">"; // Image de l'article
                echo "<div class=\"info_un_article_en_vente\">"; // Div pour les informations détaillées de l'article
                echo "<h2>{$row['nom']}</h2>"; // Nom de l'article
                echo "<p>{$row['description']}</p>"; // Description de l'article
                echo "<p>Prix : {$row['prix']} €</p>"; // Prix de l'article
                echo "<p>État : {$row['etat']}</p>"; // État de l'article
                echo "<p>Stock : {$row['stock']}</p>"; // Stock disponible de l'article
                echo "</div>"; // Fermeture de div pour les informations détaillées
                echo "</div>"; // Fermeture de div pour l'image et les informations
                echo "<div class=\"boutons\">"; // Div pour les boutons d'action (modifier et supprimer)
                echo "<button>Modifier</button>"; // Bouton pour modifier l'article
                echo "<button class=\"supprimer\">Supprimer</button>"; // Bouton pour supprimer l'article
                echo "</div>"; // Fermeture de div pour les boutons
                echo "</div>"; // Fermeture de div pour l'article en vente
            }
        } else { // Si la requête a échoué
            echo "Erreur lors de la récupération des articles en vente."; // Affiche un message d'erreur
        }
    ?>
</div>

</body>
</html>
