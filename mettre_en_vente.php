<?php
    session_start(); // Démarrage de la session PHP

    // Connexion à la base de données
    $conn = mysqli_connect("localhost", "root", "12345678", "projet_final_web");

    // Vérifier si la connexion a réussi
    if (!$conn) {
        die("Erreur de connexion à la base de données: " . mysqli_connect_error());
    }

    // Vérifier si l'utilisateur est connecté
    if(isset($_SESSION["username"])) {
        $estConnecte = true;
        $profil = [$_SESSION["prenom"], $_SESSION["nom"]];
    } else {
        // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
        $estConnecte = false;
        $profil = [];
        header("Location: connexion.php");
        exit; // Arrêter le script pour éviter toute exécution supplémentaire
    }

    // Vérifier si l'utilisateur est un vendeur (estVendeur doit être défini à 1)
    if(!$_SESSION["estVendeur"] == 1) {
        header("Location: devenir_vendeur.php");
        exit; // Redirection vers la page devenir_vendeur.php si l'utilisateur n'est pas vendeur
    }

    // Vérifier si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupérer les valeurs saisies dans le formulaire
        $nom = $_POST["nom"];
        $description = $_POST["description"];
        $prix = $_POST["prix"];
        $etat = $_POST["etat"];
        $stock = $_POST["stock"];

        // Tableau pour stocker les erreurs de validation
        $erreurs = array();

        // Validation des champs du formulaire
        if (!preg_match("/^[a-zA-Z-'\s]+$/", $nom)) {
            $erreurs["nom"] = "Le nom n'est pas valide.";
        }

        if (empty($description)) {
            $erreurs["description"] = "La description est requise.";
        }

        if (!is_numeric($prix) || $prix <= 0) {
            $erreurs["prix"] = "Le prix doit être un nombre positif.";
        }

        if (!in_array($etat, ['neuf', 'comme neuf', 'bon état', 'état moyen', 'mauvais état'])) {
            $erreurs["etat"] = "L'état n'est pas valide.";
        }

        if (!is_numeric($stock) || $stock < 1) {
            $erreurs["stock"] = "Le stock doit être un nombre positif.";
        }

        // Validation du téléchargement de la photo de l'article
        if (isset($_FILES["lien_photo"])) {
            $nom_photo_original = basename($_FILES["lien_photo"]["name"]); // Nom du fichier original
            $type_photo = strtolower(pathinfo($nom_photo_original, PATHINFO_EXTENSION)); // Extension du fichier

            $nouveau_nom_photo = uniqid('photo_article_', true) . '.' . $type_photo; // Génère un nom unique pour la photo
            $dossier_stockage = "images/annonces/".$nouveau_nom_photo; // Chemin où la photo sera stockée

            // Vérifier la taille du fichier (limite à 5MB)
            if ($_FILES["lien_photo"]["size"] > 5000000) {
                $erreurs["lien_photo"] = "Le fichier est trop volumineux (il ne doit pas dépasser 5 MB).";
            }

            // Vérifier le format de la photo (seulement jpg, jpeg, ou png)
            $type_autorise = array("jpg", "jpeg", "png");
            if (!in_array($type_photo, $type_autorise)) {
                $erreurs["lien_photo"] = "Le type du fichier n'est pas valide (jpg, jpeg, ou png seulement).";
            }

            // Déplacer et enregistrer le fichier téléchargé dans le dossier de stockage
            if (!move_uploaded_file($_FILES["lien_photo"]["tmp_name"], $dossier_stockage)) {
                $erreurs["lien_photo"] = "Erreur lors du téléchargement de la photo.";
            }
        } else {
            $erreurs["lien_photo"] = "Aucune photo n'a été téléchargée.";
        }

        // Si aucune erreur de validation n'est trouvée, procéder à l'insertion des données dans la base de données
        if (count($erreurs) == 0) {
            $user_id = $_SESSION["user_id"];

            // Préparation de la requête SQL pour insérer les données dans la table articles
            $sql = "INSERT INTO articles (nom, description, prix, etat, stock, lien_photo, vendeur_id) VALUES ('$nom', '$description', $prix, '$etat', $stock, '$dossier_stockage', $user_id)";

            // Exécution de la requête SQL d'insertion
            if ($conn->query($sql) === TRUE) {
                $_SESSION['flash_message'] = "Félicitations, votre carte a bien été ajoutée."; // Message de succès stocké dans une variable de session
                header("Location: profil.php"); // Redirection vers la page du profil après l'ajout de l'article
                exit(); // Arrêter le script pour éviter toute exécution supplémentaire
            } else {
                echo "Erreur lors de l'ajout de l'article : " . $conn->error; // Affichage d'une erreur si l'insertion échoue
            }
        }
    }

    // Fermer la connexion à la base de données
    $conn->close();
?>
