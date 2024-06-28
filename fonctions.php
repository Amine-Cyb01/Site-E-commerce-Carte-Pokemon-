<?php
    // Définition de la fonction afficherBarreNavigation qui prend en paramètre un tableau $profil
    function afficherBarreNavigation($profil) {
        // Vérifie si les éléments prénom et nom sont définis dans le tableau $profil
        if(isset($profil[0]) && isset($profil[1])) {
            $prenom = $profil[0]; // Assigne le prénom
            $nom = $profil[1]; // Assigne le nom

            // Génère la barre de navigation pour un utilisateur connecté
            echo "<header>";
            echo "<ul class=\"navigation\">";
            echo "<li class=\"left\"><a href=\"accueil.php\"><img src=\"images/site/logo.png\"/></a></li>";
            echo "<li class=\"left\"><a href=\"articles.php\">Toutes les cartes</a></li>";
            echo "<li class=\"right\"><a href=\"profil.php\">$prenom $nom</a></li>";
            echo "<li class=\"right\"><a href=\"deconnexion.php\">Déconnexion</a></li>";
        } else {
            // Génère la barre de navigation pour un utilisateur non connecté
            echo "<header>";
            echo "<ul class=\"navigation\">";
            echo "<li class=\"left\"><a href=\"accueil.php\"><img src=\"images/site/logo.png\"/></a></li>";
            echo "<li class=\"left\"><a href=\"articles.php\">Toutes les cartes</a></li>";
            echo "<li class=\"right\"><a href=\"connexion.php\">Connexion</a></li>";
        }
        echo "</ul>";
        echo "</header>";
    }

    // Définition de la fonction getFeaturedProducts qui prend en paramètre une connexion à la base de données $conn
    function getFeaturedProducts($conn) {
        $sql = "SELECT * FROM articles WHERE articles.en_vedette = 1"; // Requête SQL pour récupérer les articles en vedette
        $result = $conn->query($sql); // Exécute la requête

        if ($result->num_rows > 0) { // Vérifie si des résultats ont été trouvés
            $products = $result->fetch_all(MYSQLI_ASSOC); // Récupère tous les résultats sous forme de tableau associatif
            // echo "<pre>";
            // print_r($products);
            // echo "</pre>";
            return $products; // Retourne le tableau des produits en vedette
        } else {
            return []; // Retourne un tableau vide si aucun produit en vedette n'est trouvé
        }
    }

    // Définition de la fonction afficherTousLesArticles qui prend en paramètre une connexion à la base de données $conn
    function afficherTousLesArticles($conn) {
        $sql = "SELECT * FROM articles"; // Requête SQL pour récupérer tous les articles
        $result = $conn->query($sql); // Exécute la requête

        if ($result->num_rows > 0) { // Vérifie si des résultats ont été trouvés
            // Afficher chaque article
            while($row = $result->fetch_assoc()) { // Parcourt chaque ligne de résultat
                echo "<div class=\"product\">";
                echo "<h3>{$row['nom']}</h3>"; // Affiche le nom de l'article
                echo "<p>{$row['description']}</p>"; // Affiche la description de l'article
                echo "<span class=\"price\">{$row['prix']} €</span>"; // Affiche le prix de l'article
                echo "</div>";
            }
        } else {
            echo "Aucun article trouvé."; // Message affiché si aucun article n'est trouvé
        }
    }
?>
