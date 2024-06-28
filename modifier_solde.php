<?php
// Établir une connexion à la base de données
$servername = "localhost"; // Nom du serveur MySQL
$username = "root"; // Nom d'utilisateur MySQL
$password = "12345678"; // Mot de passe MySQL
$dbname = "projet_final_web"; // Nom de la base de données

session_start(); // Démarrage de la session PHP

$conn = new mysqli($servername, $username, $password, $dbname); // Connexion à la base de données MySQL
if ($conn->connect_error) {
    die("Échec de la connexion à la base de données : " . $conn->connect_error); // Arrête l'exécution si la connexion échoue
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

// Récupérer le solde actuel de l'utilisateur connecté
if ($estConnecte) {
    $user_id = $_SESSION["user_id"]; // Récupérer l'ID de l'utilisateur à partir de la session
    $sql = "SELECT solde FROM utilisateurs WHERE id = $user_id"; // Requête SQL pour récupérer le solde
    $result = $conn->query($sql); // Exécution de la requête SQL
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Récupérer la ligne de résultat
        $solde = $row["solde"]; // Récupérer le solde de l'utilisateur
    }
}

// Vérifier si le formulaire de recharge de solde a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les valeurs saisies dans le formulaire
    $montant_a_ajouter = $_POST["montant_a_ajouter"]; // Montant à ajouter au solde

    $erreurs = array(); // Initialisation du tableau d'erreurs de validation

    // Validation du montant à ajouter
    if (!is_numeric($montant_a_ajouter)) {
        $erreurs["montant_a_ajouter"] = "Le solde saisi n'est pas valide."; // Erreur si le montant n'est pas numérique
    } else {
        // Vérification supplémentaire du montant à ajouter
        $montant_a_ajouter = floatval($montant_a_ajouter); // Conversion en nombre à virgule flottante
        if ($montant_a_ajouter <= 0) {
            $erreurs["montant_a_ajouter"] = "Le montant à ajouter doit être supérieur à zéro."; // Erreur si le montant est inférieur ou égal à zéro
        } elseif ($solde + $montant_a_ajouter > 9999999999.99) {
            $erreurs["montant_a_ajouter"] = "Le solde ne peut pas dépasser 10 décimales."; // Erreur si le solde dépasse 10 décimales
        }
    }

    // Si aucune erreur de validation n'est trouvée, procéder à la mise à jour dans la base de données
    if (count($erreurs) == 0) {
        // Ajouter le montant au solde actuel de l'utilisateur
        $solde += $montant_a_ajouter;
        $sql = "UPDATE utilisateurs SET solde = '$solde' WHERE id = $user_id"; // Requête SQL pour mettre à jour le solde

        if ($conn->query($sql) === TRUE) {
            $_SESSION['flash_message'] = "Félicitations, le rechargement de votre compte a réussi. Votre nouveau solde est de $solde € !"; // Message de succès stocké dans la session
            header("Location: profil.php"); // Redirection vers la page de profil après la mise à jour
            exit(); // Arrêter l'exécution du script
        } else {
            echo "Erreur lors du rechargement du compte : " . $conn->error; // Affichage d'une erreur si la mise à jour échoue
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Recharger votre solde</title>
</head>

<body>
    <div class="container">
        <h1>Recharger le solde de votre compte</h1>
        <?php
        require 'fonctions.php'; // Inclure les fonctions PHP
        afficherBarreNavigation($profil); // Afficher la barre de navigation avec le profil de l'utilisateur
        ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="container-inscription-externe">
                <div class="container-inscription-interne">
                    <!-- Champ de saisie pour le montant à ajouter -->
                    <input type="number" step="0.01" name="montant_a_ajouter" id="montant_a_ajouter" required placeholder="Montant à ajouter" min="0" class="input-field"><br>
                    <?php if (isset($erreurs["montant_a_ajouter"])) { echo "<span class='erreur'>" . $erreurs["montant_a_ajouter"] . "</span><br>"; } ?>
                </div>
            </div>

            <!-- Bouton pour soumettre le formulaire de recharge de solde -->
            <br><input type="submit" value="Ajouter le montant au solde" class="submit-button">
        </form>
        
        <!-- Bouton d'annulation pour revenir à la page de profil -->
        <button onclick="window.location.href='profil.php'" class="submit-button annuler">Annuler</button>
    </div>
    
</body>
</html>
