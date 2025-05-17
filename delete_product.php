<?php
session_start();
require_once 'db.php';
require_once 'check_admin.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($product_id === false) {
        $_SESSION['message'] = "ID de produit invalide.";
        $_SESSION['message_type'] = "danger";
        header("Location: produits.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM produits WHERE id_produit = :id_produit");
        $stmt->bindParam(':id_produit', $product_id);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $_SESSION['message'] = "Produit supprimé avec succès !";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Aucun produit trouvé avec cet ID.";
                $_SESSION['message_type'] = "warning";
            }
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression du produit.";
            $_SESSION['message_type'] = "danger";
        }
    } catch (PDOException $e) {
         // Gérer les erreurs de contrainte si par exemple il est utilisé dans une table qui n'a pas ON DELETE CASCADE
        if ($e->getCode() == '23000') {
             $_SESSION['message'] = "Impossible de supprimer le produit : il est encore référencé (ex: dans des détails d'inventaire non supprimés).";
        } else {
            $_SESSION['message'] = "Erreur de base de données : " . $e->getMessage();
        }
        $_SESSION['message_type'] = "danger";
    }
    header("Location: produits.php");
    exit();
} else {
    $_SESSION['message'] = "Aucun ID de produit fourni pour la suppression.";
    $_SESSION['message_type'] = "danger";
    header("Location: produits.php");
    exit();
}
?>