<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS pour le style de la page -->
    <title>Mes achats</title> <!-- Titre de la page -->
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

        afficherBarreNavigation($profil); // Appelle une fonction pour afficher la barre de navigation en passant le profil de l'utilisateur
    ?>

    <?php
        // Récupérer les achats de l'utilisateur connecté
        $user_id = $_SESSION["user_id"]; // Récupère l'ID de l'utilisateur connecté depuis la session
        $query = "SELECT articles.nom, achats.quantite, achats.date_achat
            FROM achats INNER JOIN articles ON achats.article_id = articles.id
            WHERE achats.utilisateur_id = $user_id"; // Requête SQL pour récupérer les achats de l'utilisateur
        $result = mysqli_query($conn, $query); // Exécute la requête SQL et stocke le résultat
    ?>

<body>

    <div class="articles-achetes"> <!-- Div contenant la liste des articles achetés -->
        <h1>Mes cartes achetées</h1> <!-- Titre de la section -->
        <?php
            // Afficher la liste des articles achetés
            if (mysqli_num_rows($result) > 0) { // Vérifie s'il y a des résultats
                while ($row = mysqli_fetch_assoc($result)) { // Boucle sur chaque ligne de résultat
                    $nom_article = $row["nom"]; // Récupère le nom de l'article
                    $quantite = $row["quantite"]; // Récupère la quantité achetée
                    $date_achat = $row["date_achat"]; // Récupère la date d'achat

                    echo "<div class='barre-horizontale'>"; // Div pour chaque article acheté
                    echo "<span class='nom-article'>$nom_article</span>"; // Affiche le nom de l'article
                    echo "<span class='quantite'>$quantite</span>"; // Affiche la quantité achetée
                    echo "<span class='date-achat'>$date_achat</span>"; // Affiche la date d'achat
                    echo "</div>"; // Fermeture de la div de l'article acheté
                }
            } else { // S'il n'y a aucun résultat
                echo "Vous n'avez pas encore acheté d'articles."; // Affiche un message indiquant qu'aucun article n'a été acheté
            }
        ?>
    </div>

</body>
</html>
