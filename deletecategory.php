<?php
session_start();
require_once 'db.php'; // Adaptez le chemin
require_once 'check_admin.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $category_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($category_id === false) {
        $_SESSION['message'] = "ID de catégorie invalide.";
        $_SESSION['message_type'] = "danger";
        header("Location: categories.php");
        exit();
    }

    try {
        // Vérifier si des produits sont liés à cette catégorie (ON DELETE RESTRICT)
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM produits WHERE id_categorie = :id_categorie");
        $stmtCheck->bindParam(':id_categorie', $category_id);
        $stmtCheck->execute();
        $productCount = $stmtCheck->fetchColumn();

        if ($productCount > 0) {
            $_SESSION['message'] = "Impossible de supprimer la catégorie : elle est encore utilisée par ".$productCount." produit(s).";
            $_SESSION['message_type'] = "danger";
        } else {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id_categorie = :id_categorie");
            $stmt->bindParam(':id_categorie', $category_id);

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $_SESSION['message'] = "Catégorie supprimée avec succès !";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Aucune catégorie trouvée avec cet ID.";
                    $_SESSION['message_type'] = "warning";
                }
            } else {
                $_SESSION['message'] = "Erreur lors de la suppression de la catégorie.";
                $_SESSION['message_type'] = "danger";
            }
        }
    } catch (PDOException $e) {
        // Gérer les erreurs de contrainte de clé étrangère si ON DELETE RESTRICT est contourné ou échoue
        if ($e->getCode() == '23000') { // Code d'erreur pour violation de contrainte d'intégrité
             $_SESSION['message'] = "Impossible de supprimer la catégorie : elle est encore référencée ailleurs (probablement par des produits).";
        } else {
            $_SESSION['message'] = "Erreur de base de données : " . $e->getMessage();
        }
        $_SESSION['message_type'] = "danger";
    }
    header("Location: categories.php");
    exit();
} else {
    $_SESSION['message'] = "Aucun ID de catégorie fourni pour la suppression.";
    $_SESSION['message_type'] = "danger";
    header("Location: categories.php");
    exit();
}
?>