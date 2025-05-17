<?php
session_start();
require_once 'db.php'; // Adaptez le chemin
require_once 'check_admin.php'; // Script pour vérifier si l'utilisateur est admin et connecté

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['category_name']) && !empty(trim($_POST['category_name']))) {
        $category_name = trim($_POST['category_name']);

        try {
            // Vérifier si la catégorie existe déjà
            $stmtCheck = $pdo->prepare("SELECT id_categorie FROM categories WHERE nom_categorie = :nom_categorie");
            $stmtCheck->bindParam(':nom_categorie', $category_name);
            $stmtCheck->execute();

            if ($stmtCheck->rowCount() > 0) {
                $_SESSION['message'] = "Une catégorie avec ce nom existe déjà.";
                $_SESSION['message_type'] = "danger";
            } else {
                $stmt = $pdo->prepare("INSERT INTO categories (nom_categorie) VALUES (:nom_categorie)");
                $stmt->bindParam(':nom_categorie', $category_name);

                if ($stmt->execute()) {
                    $_SESSION['message'] = "Catégorie ajoutée avec succès !";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Erreur lors de l'ajout de la catégorie.";
                    $_SESSION['message_type'] = "danger";
                }
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erreur de base de données : " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Le nom de la catégorie ne peut pas être vide.";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: categories.php");
    exit();
} else {
    // Rediriger si accès direct non autorisé
    header("Location: categories.php");
    exit();
}
?>