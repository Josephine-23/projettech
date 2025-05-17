<?php
session_start();
require_once 'db.php'; // Adaptez le chemin
require_once 'check_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $realisateur_name = trim($_POST['inventory_realisateur_name'] ?? ($_SESSION['nom_utilisateur'] ?? 'Inconnu'));
    $inventory_notes = trim($_POST['inventory_notes'] ?? '');
    $new_counts = $_POST['new_count'] ?? []; // Tableau des [id_produit => quantite_comptee]
    $previous_stocks = $_POST['previous_stock'] ?? []; // Tableau des [id_produit => stock_precedent]

    if (empty($realisateur_name) || empty($new_counts)) {
        $_SESSION['message'] = "Le nom du réalisateur et au moins un comptage sont requis.";
        $_SESSION['message_type'] = "danger";
        header("Location: 'inventaires.php");
        exit();
    }

    try {
        $pdo->beginTransaction(); // Démarrer une transaction

        // 1. Insérer l'enregistrement principal de l'inventaire
        $stmtInv = $pdo->prepare("INSERT INTO inventaires (id_utilisateur, nom_realisateur, notes_inventaire) VALUES (:id_utilisateur, :nom_realisateur, :notes_inventaire)");
        $stmtInv->bindParam(':id_utilisateur', $_SESSION['id_utilisateur']);
        $stmtInv->bindParam(':nom_realisateur', $realisateur_name);
        $stmtInv->bindParam(':notes_inventaire', $inventory_notes);
        $stmtInv->execute();
        $id_inventaire = $pdo->lastInsertId(); // Récupérer l'ID du nouvel inventaire

        // 2. Insérer les détails de l'inventaire et mettre à jour le stock des produits
        $stmtDetail = $pdo->prepare("INSERT INTO details_inventaire (id_inventaire, id_produit, stock_precedent, quantite_comptee) VALUES (:id_inventaire, :id_produit, :stock_precedent, :quantite_comptee)");
        $stmtUpdateProductStock = $pdo->prepare("UPDATE produits SET stock_actuel = :nouveau_stock WHERE id_produit = :id_produit");

        foreach ($new_counts as $id_produit_str => $quantite_comptee_str) {
            $id_produit = filter_var($id_produit_str, FILTER_VALIDATE_INT);
            // On s'assure que la quantité est un nombre, même si vide on met 0
            $quantite_comptee = filter_var($quantite_comptee_str, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0, "default" => 0]]);
             if ($quantite_comptee_str === '' && $quantite_comptee === 0) { // Si le champ était vide, on le considère comme 0
                // ok
            } else if ($quantite_comptee === false && $quantite_comptee_str !== '0') { // Si c'est pas un nombre valide et pas '0'
                 $_SESSION['message'] = "Quantité invalide pour le produit ID " . htmlspecialchars($id_produit_str) . ". L'inventaire n'a pas été enregistré.";
                 $_SESSION['message_type'] = "danger";
                 $pdo->rollBack();
                 header("Location: inventaries.php");
                 exit();
            }


            $stock_precedent = filter_var($previous_stocks[$id_produit_str] ?? 0, FILTER_VALIDATE_INT, ["options" => ["default" => 0]]);

            if ($id_produit) {
                // Insérer le détail
                $stmtDetail->bindParam(':id_inventaire', $id_inventaire);
                $stmtDetail->bindParam(':id_produit', $id_produit);
                $stmtDetail->bindParam(':stock_precedent', $stock_precedent);
                $stmtDetail->bindParam(':quantite_comptee', $quantite_comptee);
                $stmtDetail->execute();

                // Mettre à jour le stock du produit
                $stmtUpdateProductStock->bindParam(':nouveau_stock', $quantite_comptee);
                $stmtUpdateProductStock->bindParam(':id_produit', $id_produit);
                $stmtUpdateProductStock->execute();
            }
        }

        $pdo->commit(); // Valider la transaction
        $_SESSION['message'] = "Inventaire enregistré avec succès ! Les stocks des produits ont été mis à jour.";
        $_SESSION['message_type'] = "success";
        header("Location: inventaires.php"); // Rediriger vers la liste des inventaires
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack(); // Annuler la transaction en cas d'erreur
        $_SESSION['message'] = "Erreur de base de données lors de l'enregistrement de l'inventaire : " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
        // Rediriger vers le formulaire pour que l'utilisateur puisse réessayer ou voir les erreurs
        header("Location: newinventory.php");
        exit();
    }
} else {
    header("Location: newinventory.php");
    exit();
}
?>