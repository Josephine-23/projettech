<?php
session_start();
require_once 'db.php'; // Adaptez le chemin vers votre connexion PDO
require_once 'check_admin.php';   // Script pour vérifier si l'utilisateur est admin et connecté

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage basique des données
    $supplier_name = trim($_POST['supplier_name'] ?? '');
    $supplier_contact = trim($_POST['supplier_contact'] ?? '');
    $supplier_email = trim($_POST['supplier_email'] ?? '');
    $supplier_phone = trim($_POST['supplier_phone'] ?? '');
    $supplier_address = trim($_POST['supplier_address'] ?? '');

    // Validation (le nom du fournisseur est le seul champ requis ici, adaptez selon vos besoins)
    if (empty($supplier_name)) {
        $_SESSION['message'] = "Le nom du fournisseur est obligatoire.";
        $_SESSION['message_type'] = "danger";
        header("Location: fournisseurs.php");
        exit();
    }

    // Optionnel : Valider le format de l'email si fourni
    if (!empty($supplier_email) && !filter_var($supplier_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Le format de l'adresse e-mail du fournisseur est invalide.";
        $_SESSION['message_type'] = "danger";
        header("Location: fournisseurs.php");
        exit();
    }

    try {
        // Vérifier si un fournisseur avec ce nom existe déjà
        $stmtCheck = $pdo->prepare("SELECT id_fournisseur FROM fournisseurs WHERE nom_fournisseur = :nom_fournisseur");
        $stmtCheck->bindParam(':nom_fournisseur', $supplier_name);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            $_SESSION['message'] = "Un fournisseur avec ce nom existe déjà.";
            $_SESSION['message_type'] = "danger";
        } else {
            // Préparer la requête d'insertion
            $stmt = $pdo->prepare("INSERT INTO fournisseurs (nom_fournisseur, contact_fournisseur, email_fournisseur, telephone_fournisseur, adresse_fournisseur) 
                                   VALUES (:nom_fournisseur, :contact_fournisseur, :email_fournisseur, :telephone_fournisseur, :adresse_fournisseur)");

            // Lier les paramètres
            $stmt->bindParam(':nom_fournisseur', $supplier_name);
            $stmt->bindParam(':contact_fournisseur', $supplier_contact);
            $stmt->bindParam(':email_fournisseur', $supplier_email);
            $stmt->bindParam(':telephone_fournisseur', $supplier_phone);
            $stmt->bindParam(':adresse_fournisseur', $supplier_address);

            // Exécuter la requête
            if ($stmt->execute()) {
                $_SESSION['message'] = "Fournisseur ajouté avec succès !";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Erreur lors de l'ajout du fournisseur.";
                $_SESSION['message_type'] = "danger";
            }
        }
    } catch (PDOException $e) {
        // Gérer les erreurs de base de données
        error_log("Erreur PDO lors de l'ajout du fournisseur : " . $e->getMessage()); // Log l'erreur serveur
        $_SESSION['message'] = "Erreur de base de données. Veuillez réessayer. Détail : " . $e->getMessage(); // Message pour l'utilisateur
        $_SESSION['message_type'] = "danger";
    }

    // Rediriger vers la page des fournisseurs
    header("Location: fournisseurs.php");
    exit();

} else {
    // Si la page est accédée directement sans POST, rediriger
    $_SESSION['message'] = "Accès non autorisé.";
    $_SESSION['message_type'] = "warning";
    header("Location: fournisseurs.php");
    exit();
}
?>