<?php
session_start();
// Rediriger si pas connecté (à adapter selon votre logique de session)
if (!isset($_SESSION['id_utilisateur']) /* || $_SESSION['role'] !== 'admin' */) { // Ajoutez une vérification de rôle si nécessaire
    header("Location: connexion.php"); // Adaptez le chemin vers votre page de connexion
    exit();
}
include_once 'db.php'; // Chemin vers votre fichier de connexion DB

$current_user_name = $_SESSION['nom_utilisateur'] ?? 'Admin';
// $page_title est une variable que vous définirez dans chaque page spécifique
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Stock Management'; ?> - INVADER</title>
    <link rel="stylesheet" href="styles.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="admin-top-header">
        <div class="admin-top-header-container">
            <div class="admin-logo">
                <a href="index.php">INVADER <span class="logo-subtext">Stock Management</span></a>
            </div>
            <div class="admin-user-info">
                <span>Bonjour, <?php echo htmlspecialchars($current_user_name); ?></span>
                <a href="deconnexion.php" class="btn btn-sm btn-outline-light">Déconnexion</a>
            </div>
        </div>
    </header>

    <main class="admin-main-wrapper">
        <?php // Le contenu spécifique de la page viendra ici ?>