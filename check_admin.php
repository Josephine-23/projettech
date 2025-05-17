<?php
if (!isset($_SESSION['id_utilisateur'])) {
    // Si vous voulez être plus strict et rediriger vers une page de connexion spécifique pour l'admin
    // header("Location: ../admin_login.php"); 
    $_SESSION['message'] = "Vous devez être connecté pour accéder à cette page.";
    $_SESSION['message_type'] = "danger";
    header("Location: connexion.php"); // Ou votre page de connexion générale
    exit();
}
?>