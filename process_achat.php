<?php
session_start(); // Démarrage de la session PHP
require 'fonctions.php'; // Inclusion du fichier de fonctions

// Connexion à la base de données MySQL
$conn = mysqli_connect("localhost", "root", "12345678", "projet_final_web");

// Vérifier si la connexion à la base de données a échoué
if (!$conn) {
    die("Erreur de connexion à la base de données: " . mysqli_connect_error()); // Arrête l'exécution et affiche l'erreur
}

$profil = []; // Initialisation du tableau de profil
$estConnecte = false; // Initialisation de la variable indiquant l'état de connexion de l'utilisateur

// Vérifier l'authentification de l'utilisateur
if(isset($_SESSION["username"])) {
    $estConnecte = true; // L'utilisateur est connecté
    $profil = [$_SESSION["prenom"], $_SESSION["nom"]]; // Profil de l'utilisateur à partir des données de session
} else {
    $estConnecte = false; // L'utilisateur n'est pas connecté
    $profil = []; // Initialisation d'un profil vide
    header("Location: connexion.php"); // Redirection vers la page de connexion
    exit; // Arrêter l'exécution du script
}

// Récupérer l'ID de l'utilisateur connecté
$user_id = $_SESSION["user_id"];

// Traitement du formulaire si POST est soumis avec quantité et ID d'article
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["quantite"]) && isset($_POST["id_article"])) {
    $article_id = $_POST["id_article"]; // Récupération de l'ID de l'article à acheter
    $quantite = $_POST["quantite"]; // Récupération de la quantité à acheter

    // Récupération des détails de l'article à acheter
    $query_article = "SELECT * FROM articles WHERE id = $article_id";
    $result_article = mysqli_query($conn, $query_article);

    if ($result_article) {
        $row_article = mysqli_fetch_assoc($result_article); // Récupération des résultats de la requête

        // Récupération des détails de l'utilisateur pour vérifier le solde
        $query_utilisateur = "SELECT solde FROM utilisateurs WHERE id = $user_id";
        $result_utilisateur = mysqli_query($conn, $query_utilisateur);

        if ($result_utilisateur) {
            $row_utilisateur = mysqli_fetch_assoc($result_utilisateur); // Récupération des résultats de la requête

            // Vérification si l'article est en stock et si l'utilisateur a assez d'argent
            if ($row_article['stock'] >= $quantite && $row_article['prix'] * $quantite <= $row_utilisateur['solde']) {
                // Calcul du nouveau solde de l'utilisateur après l'achat
                $nouveau_solde = $row_utilisateur['solde'] - ($row_article['prix'] * $quantite);

                // Mettre à jour le solde de l'utilisateur dans la base de données
                $update_solde = "UPDATE utilisateurs SET solde = $nouveau_solde WHERE id = $user_id";
                $result_solde = mysqli_query($conn, $update_solde);

                if ($result_solde) {
                    // Mettre à jour le stock de l'article après l'achat
                    $nouveau_stock = $row_article['stock'] - $quantite;
                    $update_stock = "UPDATE articles SET stock = $nouveau_stock WHERE id = $article_id";
                    $result_stock = mysqli_query($conn, $update_stock);

                    if ($result_stock) {
                        // Insérer un enregistrement dans la table "ventes" pour indiquer la vente
                        $insert_vente = "INSERT INTO ventes (vendeur_id, article_id, quantite) VALUES ({$row_article['vendeur_id']}, $article_id, $quantite)";
                        $result_vente = mysqli_query($conn, $insert_vente);

                        if ($result_vente) {
                            // Insérer un enregistrement dans la table "achats" pour enregistrer l'achat de l'utilisateur
                            $insert_achat = "INSERT INTO achats (utilisateur_id, article_id, quantite) VALUES ($user_id, $article_id, $quantite)";
                            $result_achat = mysqli_query($conn, $insert_achat);

                            if ($result_achat) {
                                // Redirection vers la page de confirmation d'achat si tout est réussi
                                $_SESSION['flash_message'] = "Votre achat a été effectué avec succès !";
                                header("Location: confirmation_achat.php");
                                exit; // Arrêter l'exécution du script après la redirection
                            } else {
                                echo "Erreur lors de l'insertion de l'achat : " . mysqli_error($conn); // Affichage de l'erreur d'insertion
                            }
                        } else {
                            echo "Erreur lors de l'insertion de la vente : " . mysqli_error($conn); // Affichage de l'erreur d'insertion
                        }
                    } else {
                        echo "Erreur lors de la mise à jour du stock : " . mysqli_error($conn); // Affichage de l'erreur de mise à jour
                    }
                } else {
                    echo "Erreur lors de la mise à jour du solde : " . mysqli_error($conn); // Affichage de l'erreur de mise à jour
                }
            } else {
                echo "Solde insuffisant."; // Message si le solde de l'utilisateur est insuffisant pour l'achat
            }
        } else {
            echo "Erreur lors de la récupération des détails de l'utilisateur : " . mysqli_error($conn); // Affichage de l'erreur de récupération
        }
    } else {
        echo "Erreur lors de la récupération des détails de l'article : " . mysqli_error($conn); // Affichage de l'erreur de récupération
    }
} else {
    echo "Paramètres manquants."; // Message si les paramètres nécessaires ne sont pas présents dans la requête POST
}
?>
