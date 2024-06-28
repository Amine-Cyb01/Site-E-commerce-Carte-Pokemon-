<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css"> <!-- Inclusion de la feuille de style -->
    <title>Mes ventes</title>
</head>

<?php
session_start(); // Démarrage de la session PHP

require 'fonctions.php'; // Inclusion du fichier de fonctions

// Connexion à la base de données MySQL
$conn = mysqli_connect("localhost", "root", "12345678", "projet_final_web");

// Vérifier si la connexion à la base de données a échoué
if (!$conn) {
    die("Erreur de connexion à la base de données: " . mysqli_connect_error()); // Arrête l'exécution et affiche l'erreur
}

// Vérifier si l'utilisateur est connecté
if(isset($_SESSION["username"])) {
    $estConnecte = true; // L'utilisateur est connecté
    $profil = [$_SESSION["prenom"], $_SESSION["nom"]]; // Profil de l'utilisateur à partir des données de session
} else {
    $estConnecte = false; // L'utilisateur n'est pas connecté
    $profil = [];
    header("Location: connexion.php"); // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
    exit; // Arrêter l'exécution du script
}

// Vérifier si l'utilisateur est vendeur (pour accéder à cette page)
if(!$_SESSION["estVendeur"] == 1) {
    header("Location: profil.php"); // Redirection vers la page de profil si l'utilisateur n'est pas vendeur
    exit; // Arrêter l'exécution du script
}

afficherBarreNavigation($profil); // Appel d'une fonction pour afficher la barre de navigation
?>

<?php
// Récupérer les ventes de l'utilisateur connecté
$user_id = $_SESSION["user_id"]; // Récupération de l'ID de l'utilisateur depuis la session
$query = "SELECT articles.nom, ventes.quantite, ventes.date_vente
    FROM ventes INNER JOIN articles ON ventes.article_id = articles.id
    WHERE ventes.vendeur_id = $user_id"; // Requête SQL pour récupérer les ventes de l'utilisateur
$result = mysqli_query($conn, $query); // Exécution de la requête SQL
?>

<body>
    <div class="articles-vendus">
        <h1>Mes cartes vendues</h1>
        <?php
            // Afficher la liste des articles vendus
            if (mysqli_num_rows($result) > 0) { // Vérification s'il y a des résultats
                while ($row = mysqli_fetch_assoc($result)) { // Parcours des résultats
                    $nomArticle = $row["nom"]; // Nom de l'article vendu
                    $quantite = $row["quantite"]; // Quantité vendue
                    echo "<div class='barre-horizontale'>"; // Début d'une ligne pour chaque article vendu
                    echo "<span class='nom-article'>$nomArticle</span>"; // Affichage du nom de l'article
                    echo "<div class='barre' style='width: $quantite%;'></div>"; // Barre de progression basée sur la quantité vendue
                    echo "<span class='quantite'>$quantite</span>"; // Affichage de la quantité vendue
                    echo "</div>"; // Fin de la ligne pour chaque article vendu
                }
            } else {
                echo "Vous n'avez pas encore vendu d'articles."; // Message affiché si l'utilisateur n'a pas encore vendu d'articles
            }
        ?>
    </div>

</body>
</html>
