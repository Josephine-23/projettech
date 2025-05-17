<?php
session_start();
require_once '../config/db.php'; // Adaptez le chemin
require_once 'check_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['category_id'], $_POST['category_name']) && !empty(trim($_POST['category_name'])) && !empty($_POST['category_id'])) {
        $category_id = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
        $category_name = trim($_POST['category_name']);

        if ($category_id === false) {
            $_SESSION['message'] = "ID de catégorie invalide.";
            $_SESSION['message_type'] = "danger";
            header("Location: categories.php");
            exit();
        }

        try {
            // Vérifier si une AUTRE catégorie avec ce nom existe déjà
            $stmtCheck = $pdo->prepare("SELECT id_categorie FROM categories WHERE nom_categorie = :nom_categorie AND id_categorie != :id_categorie");
            $stmtCheck->bindParam(':nom_categorie', $category_name);
            $stmtCheck->bindParam(':id_categorie', $category_id);
            $stmtCheck->execute();

            if ($stmtCheck->rowCount() > 0) {
                $_SESSION['message'] = "Une autre catégorie avec ce nom existe déjà.";
                $_SESSION['message_type'] = "danger";
            } else {
                $stmt = $pdo->prepare("UPDATE categories SET nom_categorie = :nom_categorie WHERE id_categorie = :id_categorie");
                $stmt->bindParam(':nom_categorie', $category_name);
                $stmt->bindParam(':id_categorie', $category_id);

                if ($stmt->execute()) {
                    $_SESSION['message'] = "Catégorie mise à jour avec succès !";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Erreur lors de la mise à jour de la catégorie.";
                    $_SESSION['message_type'] = "danger";
                }
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erreur de base de données : " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Données manquantes ou invalides pour la mise à jour.";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: categories.php");
    exit();
} else {
    header("Location: categories.php");
    exit();
}
?>