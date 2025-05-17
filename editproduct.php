<?php
session_start();
require_once 'db.php'; // Adaptez le chemin
require_once 'check_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
    $product_name = trim($_POST['product_name'] ?? '');
    $category_id = filter_var($_POST['category_id'] ?? null, FILTER_VALIDATE_INT);
    $supplier_id = filter_var($_POST['supplier_id'] ?? null, FILTER_VALIDATE_INT);
    $packaging = trim($_POST['packaging'] ?? '');
    $max_stock = filter_var($_POST['max_stock'] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]]);
    $critical_threshold = filter_var($_POST['critical_threshold'] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]]);

    if ($product_id === false || empty($product_name) || $category_id === false || $supplier_id === false || $max_stock === false || $critical_threshold === false) {
        $_SESSION['message'] = "Données manquantes ou invalides pour la mise à jour.";
        $_SESSION['message_type'] = "danger";
        header("Location: produits.php");
        exit();
    }
    if ($critical_threshold > $max_stock) {
        $_SESSION['message'] = "Le seuil critique ne peut pas être supérieur au stock maximum.";
        $_SESSION['message_type'] = "danger";
        header("Location: produits.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE produits SET 
                                nom_produit = :nom_produit, 
                                id_categorie = :id_categorie, 
                                id_fournisseur = :id_fournisseur, 
                                conditionnement = :conditionnement, 
                                stock_maximum = :stock_maximum, 
                                seuil_critique = :seuil_critique
                               WHERE id_produit = :id_produit");

        $stmt->bindParam(':nom_produit', $product_name);
        $stmt->bindParam(':id_categorie', $category_id);
        $stmt->bindParam(':id_fournisseur', $supplier_id);
        $stmt->bindParam(':conditionnement', $packaging);
        $stmt->bindParam(':stock_maximum', $max_stock);
        $stmt->bindParam(':seuil_critique', $critical_threshold);
        $stmt->bindParam(':id_produit', $product_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Produit mis à jour avec succès !";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erreur lors de la mise à jour du produit.";
            $_SESSION['message_type'] = "danger";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur de base de données : " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
    header("Location: produits.php");
    exit();
} else {
    header("Location: produits.php");
    exit();
}
?>