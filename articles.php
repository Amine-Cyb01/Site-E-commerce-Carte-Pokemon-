<?php
session_start(); // Démarre une nouvelle session ou reprend une session existante

require 'fonctions.php'; // Inclut le fichier 'fonctions.php' qui contient des fonctions utilitaires

// Connexion à la base de données
$conn = mysqli_connect("localhost", "root", "12345678", "projet_final_web"); // Établit une connexion à la base de données MySQL avec les paramètres donnés

// Vérifier si la connexion a réussi
if (!$conn) { // Vérifie si la connexion a échoué
    die("Erreur de connexion à la base de données: " . mysqli_connect_error()); // Arrête l'exécution du script et affiche un message d'erreur
}

if (isset($_SESSION["username"])) { // Vérifie si l'utilisateur est connecté en vérifiant l'existence de la variable de session 'username'
    $estConnecte = true; // Définit une variable indiquant que l'utilisateur est connecté
    $profil = [$_SESSION["prenom"], $_SESSION["nom"]]; // Récupère le prénom et le nom de l'utilisateur dans la session
} else { // Si l'utilisateur n'est pas connecté
    $estConnecte = false; // Définit une variable indiquant que l'utilisateur n'est pas connecté
    $profil = []; // Initialise un tableau vide pour le profil
}

afficherBarreNavigation($profil); // Appelle une fonction pour afficher la barre de navigation en passant le profil de l'utilisateur

// Traitement de la recherche et du tri
$recherche = isset($_GET['recherche']) ? $_GET['recherche'] : ''; // Récupère la valeur de recherche depuis les paramètres GET
$tri = isset($_GET['tri']) ? $_GET['tri'] : ''; // Récupère la valeur de tri depuis les paramètres GET

$query = "SELECT * FROM articles"; // Initialise la requête SQL pour sélectionner tous les articles
$conditions = []; // Initialise un tableau pour stocker les conditions de la requête

if (!empty($recherche)) { // Si une recherche a été effectuée
    $conditions[] = "nom LIKE '%" . mysqli_real_escape_string($conn, $recherche) . "%'"; // Ajoute une condition pour la recherche par nom de l'article
}

if (count($conditions) > 0) { // Si des conditions ont été ajoutées
    $query .= " WHERE " . implode(' AND ', $conditions); // Ajoute les conditions à la requête SQL
}

// On ajoute un tri ascendant et descendant par prix
if (!empty($tri)) { // Si un tri a été sélectionné
    if ($tri == "prix_asc") { // Si le tri est par prix croissant
        $query .= " ORDER BY prix ASC"; // Ajoute une clause ORDER BY pour trier par prix croissant
    } elseif ($tri == "prix_desc") { // Si le tri est par prix décroissant
        $query .= " ORDER BY prix DESC"; // Ajoute une clause ORDER BY pour trier par prix décroissant
    }
}

$result = mysqli_query($conn, $query); // Exécute la requête SQL et stocke le résultat
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS pour le style de la page -->
    <title>Toutes les cartes</title> <!-- Titre de la page -->
</head>
<body>

    <div class="container">
        <h1>Toutes les cartes en vente sur notre site</h1> <!-- Titre principal de la page -->

        <form action="articles.php" method="GET" class="search-container"> <!-- Formulaire pour la recherche et le tri -->
            <input type="text" name="recherche" id="recherche" placeholder="Nom du Pokémon" class="search-input"> <!-- Champ de saisie pour la recherche -->
            <select name="tri" class="sort-select"> <!-- Sélecteur pour le tri -->
                <option value="">Trier par</option>
                <option value="prix_asc">Prix croissant</option>
                <option value="prix_desc">Prix décroissant</option>
            </select>
            <button type="submit" class="search-button">🔍</button> <!-- Bouton pour soumettre le formulaire -->
        </form>

        <div class="all_products">
            <?php
                if ($result && mysqli_num_rows($result) > 0) { // Vérifie si la requête a retourné des résultats
                    while ($row = mysqli_fetch_assoc($result)) { // Boucle sur chaque ligne de résultat
                        echo "<div class=\"product\">"; // Div pour chaque produit
                        echo "<a href=\"details_article.php?id={$row['id']}\">"; // Lien vers la page de détails de l'article
                        echo "<img src=\"{$row['lien_photo']}\" alt=\"{$row['nom']}\">"; // Image du produit
                        echo "<h3>{$row['nom']}</h3>"; // Nom du produit
                        echo "<span class=\"price\">{$row['prix']} €</span>"; // Prix du produit
                        echo "</a>";
                        echo "</div>";
                    }
                } else { // Si aucun résultat n'a été trouvé
                    echo "Aucun résultat trouvé pour la recherche '$recherche'."; // Affiche un message indiquant qu'aucun résultat n'a été trouvé
                }
            ?>
        </div>
    </div>

</body>
</html>
