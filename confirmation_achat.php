<?php
session_start(); // Démarre une nouvelle session ou reprend une session existante
require 'fonctions.php'; // Inclut le fichier 'fonctions.php' qui contient des fonctions utilitaires

if (isset($_SESSION["username"])) { // Vérifie si l'utilisateur est connecté en vérifiant l'existence de la variable de session 'username'
    $profil = [$_SESSION["prenom"], $_SESSION["nom"]]; // Récupère le prénom et le nom de l'utilisateur dans la session et les stocke dans le tableau $profil
} else { // Si l'utilisateur n'est pas connecté
    $profil = []; // Initialise un tableau vide pour le profil
}

afficherBarreNavigation($profil); // Appelle une fonction pour afficher la barre de navigation en passant le profil de l'utilisateur

if (!isset($_SESSION["username"])) { // Vérifie à nouveau si l'utilisateur est connecté
    header("Location: connexion.php"); // Redirige l'utilisateur vers la page de connexion s'il n'est pas connecté
    exit; // Arrête l'exécution du script
}

$flash_message = isset($_SESSION['flash_message']) ? $_SESSION['flash_message'] : "Votre achat a été effectué avec succès !"; 
// Récupère le message flash de la session s'il existe, sinon utilise un message par défaut

unset($_SESSION['flash_message']); // Supprime le message flash de la session
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> <!-- Définit l'encodage des caractères pour le document -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Assure la compatibilité avec les appareils mobiles -->
    <title>Confirmation d'achat</title> <!-- Titre de la page -->
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS pour le style de la page -->
</head>
<body>
    <div class="container"> <!-- Conteneur principal de la page -->
        <h1>Confirmation d'achat</h1> <!-- Titre principal de la page -->
        <p><?php echo $flash_message; ?></p> <!-- Affiche le message de confirmation -->
        <a href="articles.php" class="button">Retourner à la liste des articles</a> <!-- Lien pour retourner à la liste des articles -->
    </div>
</body>
</html>

