<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css"> <!-- Inclusion de la feuille de style -->
    <title>Mon compte</title>
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
    $profil = []; // Initialisation d'un profil vide
    header("Location: connexion.php"); // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
    exit; // Arrêter l'exécution du script
}

// Récupérer le solde de l'utilisateur connecté
if ($estConnecte) {
    $user_id = $_SESSION["user_id"]; // Récupération de l'ID de l'utilisateur depuis la session
    $sql = "SELECT solde FROM utilisateurs WHERE id = $user_id"; // Requête SQL pour récupérer le solde
    $result = $conn->query($sql); // Exécution de la requête SQL
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Récupération du résultat de la requête
        $solde = $row["solde"]; // Stockage du solde dans la variable $solde
    }
}

afficherBarreNavigation($profil); // Appel d'une fonction pour afficher la barre de navigation
echo "<h1>Bonjour, $profil[0] $profil[1] !</h1>"; // Affichage d'un message de bienvenue avec le prénom et le nom de l'utilisateur
?>

<body>
    <?php
        if (isset($_SESSION['flash_message'])) {
            echo "<h3 class=\"flash_message\">" .$_SESSION['flash_message'] . "</h3>"; // Affichage d'un message flash s'il est présent
            unset($_SESSION['flash_message']); // Suppression du message flash de la session après l'affichage
        }
    ?>

    <div class="profil">
        <div class="profil_compartiment">
            <h2>Mes informations personnelles</h2>
            <ul>
                <a class="petite_box" href="modifier_informations.php"><li>Modifier mes informations personnelles</li></a> <!-- Liens vers les pages de modification -->
                <a class="petite_box" href="modifier_adresse.php"><li>Modifier mon adresse</li></a>
                <a class="petite_box" href="modifier_mot_de_passe.php"><li>Modifier mon mot de passe</li></a>
                <a class="petite_box" href="modifier_solde.php"><li>Recharger le solde de votre compte</li></a>
                <?php if($_SESSION['estVendeur'] == 0) { echo "<a class=\"petite_box\" href=\"devenir_vendeur.php\"><li>Devenir vendeur</li></a>";} ?> <!-- Lien pour devenir vendeur si l'utilisateur ne l'est pas -->
                <li class="gras">Mon solde actuel : <?php echo $solde . " €"?></li> <!-- Affichage du solde actuel de l'utilisateur -->
            </ul>
        </div>
                
        <div class="profil_compartiment">
            <h2>Mes articles</h2>
            <ul>
                <?php if($_SESSION['estVendeur'] == 1) { echo "<a class=\"petite_box\" href=\"mettre_en_vente.php\"><li>Mettre une carte en vente</li></a>";} ?> <!-- Lien pour mettre un article en vente si l'utilisateur est vendeur -->
                <?php if($_SESSION['estVendeur'] == 1) { echo "<a class=\"petite_box\" href=\"article_en_vente.php\"><li>Mes cartes en vente</li></a>";} ?> <!-- Lien pour voir les articles en vente si l'utilisateur est vendeur -->
                <?php if($_SESSION['estVendeur'] == 1) { echo "<a class=\"petite_box\" href=\"ventes.php\"><li>Mes ventes</li></a>";} ?> <!-- Lien pour voir les ventes si l'utilisateur est vendeur -->
                <a class="petite_box" href="achats.php"><li>Mes achats</li></a> <!-- Lien pour voir les achats de l'utilisateur -->
            </ul>
        </div>
    </div>
    <div class="bouton-profil">
        <a href="articles.php"><button class="bouton-accueil">Découvrir les cartes</button></a> <!-- Bouton pour découvrir les articles disponibles -->
    </div>

</body>
</html>
