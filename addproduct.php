<?php
session_start();
require_once 'db.php'; // Adaptez le chemin
require_once 'check_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et validation basique des données
    $product_name = trim($_POST['nom_produit'] ?? '');
    $category_id = filter_var($_POST['id_categorie'] ?? null, FILTER_VALIDATE_INT);
    $supplier_id = filter_var($_POST['id_fournisseur'] ?? null, FILTER_VALIDATE_INT);
    $packaging = trim($_POST['conditionnement'] ?? '');
    $max_stock = filter_var($_POST['stock_maximum'] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]]);
    $critical_threshold = filter_var($_POST['seuil_critique'] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]]);

    if (empty($product_name) || $category_id === false || $supplier_id === false || $max_stock === false || $critical_threshold === false) {
        $_SESSION['message'] = "Veuillez remplir tous les champs obligatoires correctement.";
        $_SESSION['message_type'] = "danger";
        header("Location: addproduct.php");
        exit();
    }
     if ($critical_threshold > $max_stock) {
        $_SESSION['message'] = "Le seuil critique ne peut pas être supérieur au stock maximum.";
        $_SESSION['message_type'] = "danger";
        header("Location: addproduct.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO produits (nom_produit, id_categorie, id_fournisseur, conditionnement, stock_maximum, seuil_critique, stock_actuel) 
                               VALUES (:nom_produit, :id_categorie, :id_fournisseur, :conditionnement, :stock_maximum, :seuil_critique, 0)"); // Stock actuel à 0 par défaut

        $stmt->bindParam(':nom_produit', $product_name);
        $stmt->bindParam(':id_categorie', $category_id);
        $stmt->bindParam(':id_fournisseur', $supplier_id);
        $stmt->bindParam(':conditionnement', $packaging);
        $stmt->bindParam(':stock_maximum', $max_stock);
        $stmt->bindParam(':seuil_critique', $critical_threshold);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Produit ajouté avec succès !";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erreur lors de l'ajout du produit.";
            $_SESSION['message_type'] = "danger";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur de base de données : " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
    header("Location: addproduct.php");
    exit();
} else {
    header("Location: produits.php");
    exit();
}


?>