<?php
session_start();

// Supprimer toutes les variables de session
$_SESSION = array();

// Détruire la session
session_destroy();

// Rediriger l'utilisateur vers l'accueil
header("Location: accueil.php");
exit;
?>