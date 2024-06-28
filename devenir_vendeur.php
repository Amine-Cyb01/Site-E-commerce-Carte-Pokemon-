<?php
// Établir une connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "projet_final_web";

session_start(); // Démarre une nouvelle session ou reprend une session existante

$conn = new mysqli($servername, $username, $password, $dbname); // Crée une connexion à la base de données
if ($conn->connect_error) { // Vérifie si la connexion a échoué
    die("Échec de la connexion à la base de données : " . $conn->connect_error); // Affiche un message d'erreur et arrête le script si la connexion échoue
}

if(isset($_SESSION["username"])) { // Vérifie si l'utilisateur est connecté
    $estConnecte = true; // Indique que l'utilisateur est connecté
    $profil = [$_SESSION["prenom"], $_SESSION["nom"]]; // Récupère le prénom et le nom de l'utilisateur pour le profil
} else {
    $estConnecte = false; // Indique que l'utilisateur n'est pas connecté
    $profil = []; // Initialise un tableau vide pour le profil
    header("Location: connexion.php"); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit; // Arrête l'exécution du script
}

if($_SESSION["estVendeur"] == 1) { // Vérifie si l'utilisateur est déjà vendeur
    header("Location: profil.php"); // Redirige vers la page de profil si l'utilisateur est déjà vendeur
    exit; // Arrête l'exécution du script
}

// Récupérer les informations personnelles de l'utilisateur connecté
if ($estConnecte) { // Si l'utilisateur est connecté
    $user_id = $_SESSION["user_id"]; // Récupère l'ID de l'utilisateur connecté
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") { // Si le formulaire a été soumis en méthode POST
    // Récupérer les valeurs saisies dans le formulaire
    $IBAN = $_POST["IBAN"]; // Récupère l'IBAN saisi par l'utilisateur

    $erreurs = array(); // Initialise un tableau pour les erreurs
    if (!preg_match("/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4}[0-9]{7}([A-Z0-9]?){0,16}$/u", $IBAN)) { // Vérifie si l'IBAN est valide
        $erreurs["IBAN"] = "Le numéro IBAN saisi n'est pas valide."; // Ajoute une erreur si l'IBAN n'est pas valide
    }

    if (isset($_FILES["lien_photo_piece_identite"])) { // Vérifie si une photo d'identité a été téléchargée
        $nom_photo_original = basename($_FILES["lien_photo_piece_identite"]["name"]); // Nom du fichier original
        $type_photo = strtolower(pathinfo($nom_photo_original, PATHINFO_EXTENSION)); // Extension du fichier

        $nouveau_nom_photo = uniqid('photo_carte_identite_', true) . '.' . $type_photo; // Génère un nom unique pour la photo
        $dossier_stockage = "images/cartes_identite/".$nouveau_nom_photo; // Chemin où la photo sera stockée

        // Vérifie si la taille du fichier dépasse la limite de 5MB
        if ($_FILES["lien_photo_piece_identite"]["size"] > 5000000) {
            $erreurs["lien_photo_piece_identite"] = "Le fichier est trop volumineux (il ne doit pas dépasser 5 mb)."; // Ajoute une erreur si le fichier est trop volumineux
        }

        // Restriction des formats autorisés
        $type_autorise = array("jpg", "jpeg", "png"); // Formats autorisés
        if (!in_array($type_photo, $type_autorise)) { // Vérifie si le format du fichier est autorisé
            $erreurs["lien_photo_piece_identite"] = "Le type du fichier n'est pas valide (jpg, jpeg, ou png seulement)."; // Ajoute une erreur si le format du fichier n'est pas valide
        }

        // Vérifie si le téléchargement du fichier a échoué
        if (!move_uploaded_file($_FILES["lien_photo_piece_identite"]["tmp_name"], $dossier_stockage)) {
            $erreurs["lien_photo_piece_identite"] = "Erreur lors du téléchargement de la photo."; // Ajoute une erreur si le téléchargement échoue
        }
    } else {
        $erreurs["lien_photo_piece_identite"] = "Aucune photo n'a été téléchargée."; // Ajoute une erreur si aucune photo n'a été téléchargée
    }

    if (count($erreurs) == 0) { // Si aucune erreur n'a été détectée
        // Ajouter les données nécessaires dans la base de données
        $sql = "UPDATE utilisateurs SET estVendeur = 1, IBAN = '$IBAN', lien_photo_piece_identite = '$dossier_stockage' WHERE id = $user_id"; // Requête SQL pour mettre à jour les informations de l'utilisateur

        if ($conn->query($sql) === TRUE) { // Exécute la requête et vérifie si elle a réussi
            $_SESSION["estVendeur"] = 1; // Met à jour la session pour indiquer que l'utilisateur est maintenant vendeur

            $_SESSION['flash_message'] = "Félicitations, vous pouvez désormais vendre des cartes !"; // Stocke un message de succès dans une variable de session
            header("Location: profil.php"); // Redirige vers la page de profil
            exit(); // Termine le script pour éviter toute exécution supplémentaire
        } else {
            echo "Erreur lors de l'ajout des informations pour devenir vendeur : " . $conn->error; // Affiche un message d'erreur si la requête échoue
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS pour le style -->
    <title>Devenir vendeur</title> <!-- Titre de la page -->
</head>

    <?php
        require 'fonctions.php'; // Inclut le fichier 'fonctions.php' qui contient des fonctions utilitaires
        afficherBarreNavigation($profil); // Appelle une fonction pour afficher la barre de navigation avec le profil de l'utilisateur
    ?>

<body>
    <div class="container">
        <h1>Ajouter les informations nécessaires pour devenir vendeur</h1> <!-- Titre de la section -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data"> <!-- Formulaire pour soumettre les informations -->
            <div class="container-inscription-externe">
                <div class="container-inscription-interne">
                    <input type="text" name="IBAN" id="IBAN" required placeholder="IBAN" class="input-field"><br> <!-- Champ pour saisir l'IBAN -->
                    <?php if (isset($erreurs["IBAN"])) { echo "<span class='erreur'>" . $erreurs["IBAN"] . "</span><br>"; } ?> <!-- Affiche une erreur si l'IBAN n'est pas valide -->

                    <h3>Téléchargez une photo de votre pièce d'identité</h3> <!-- Indication pour télécharger une photo de la pièce d'identité -->
                    <input type="file" name="lien_photo_piece_identite" id="lien_photo_piece_identite" accept="image/*" required class="input-field"><br> <!-- Champ pour télécharger la photo -->
                    <?php if (isset($erreurs["lien_photo_piece_identite"])) { echo "<span class='erreur'>" . $erreurs["lien_photo_piece_identite"] . "</span><br>"; } ?> <!-- Affiche une erreur si la photo n'est pas valide -->
                </div>
            </div>

            <br><input type="submit" value="Modifier mes informations" class="submit-button"> <!-- Bouton pour soumettre le formulaire -->
        </form>
        <button onclick="window.location.href='profil.php'" class="submit-button annuler">Annuler</button> <!-- Bouton pour annuler et retourner au profil -->
    </div>
    
</body>
</html>
