<?php
session_start(); 

include_once 'db.php';     // Votre connexion PDO $pdo
include_once 'header.php'; // Votre header HTML commun pour l'admin (qui devrait inclure le <head> et le début du <body>)
include_once 'check_admin.php'; // Vérifie si l'utilisateur est admin et connecté
?>
<link rel="stylesheet" href="admin_style.css">
<?php   
$page_title = "Gestion des Fournisseurs"; // Pour le <title> dans header.php

// --- Récupération des fournisseurs depuis la base de données ---
$suppliers = []; // Initialiser en cas d'erreur
try {
    $sql = "SELECT 
                id_fournisseur, 
                nom_fournisseur, 
                contact_fournisseur, 
                email_fournisseur, 
                telephone_fournisseur,
                adresse_fournisseur  -- Assurez-vous que cette colonne existe si vous l'utilisez dans data-address
            FROM fournisseurs 
            ORDER BY nom_fournisseur ASC";

    $stmt = $pdo->query($sql);
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Gérer l'erreur de récupération des fournisseurs
    error_log("Erreur PDO lors de la récupération des fournisseurs : " . $e->getMessage());
    $_SESSION['message'] = "Impossible de charger la liste des fournisseurs. Erreur: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    // $suppliers restera un tableau vide, le message "Aucun fournisseur" s'affichera
}
?>

<div class="page-content-wrapper">
    <nav class="content-tabs">
        <!-- Logique PHP pour la classe 'active' sur les onglets -->
        <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
        <a href="produits.php" class="tab-item <?php echo ($currentPage == 'produits.php') ? 'active' : ''; ?>">Produits</a>
        <a href="categories.php" class="tab-item <?php echo ($currentPage == 'categories.php') ? 'active' : ''; ?>">Catégories</a>
        <a href="inventaires.php" class="tab-item <?php echo (strpos($currentPage, 'inventory') !== false || $currentPage == 'inventaires.php') ? 'active' : ''; ?>">Inventaires</a>
        <a href="fournisseurs.php" class="tab-item <?php echo ($currentPage == 'fournisseurs.php') ? 'active' : ''; ?>">Fournisseurs</a>
    </nav>

    <div class="page-title-bar">
        <h1>Fournisseurs</h1>
        <div class="actions">
            <input type="search" id="supplierSearch" placeholder="🔍 Rechercher des fournisseurs..." class="search-input">
            <!-- Le data-modal-target doit correspondre à l'ID du modal -->
            <button class="btn btn-primary open-modal" data-modal-target="addSupplierModal">+ Ajouter un Fournisseur</button>
        </div>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type']); ?>">
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="data-table" id="suppliersTable">
            <thead>
                <tr>
                    <th>NOM DU FOURNISSEUR</th>
                    <th>CONTACT</th>
                    <th>EMAIL</th>
                    <th>TÉLÉPHONE</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($suppliers)): ?>
                    <?php foreach ($suppliers as $supplier): ?>
                        <?php
                            // Assurer que les clés existent pour éviter les erreurs
                            $contact = $supplier['contact_fournisseur'] ?? 'N/A';
                            $email = $supplier['email_fournisseur'] ?? 'N/A';
                            $phone = $supplier['telephone_fournisseur'] ?? 'N/A';
                            $address = $supplier['adresse_fournisseur'] ?? ''; // Pour le data-attribute
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($supplier['nom_fournisseur']); ?></td>
                            <td><?php echo htmlspecialchars($contact); ?></td>
                            <td><?php echo htmlspecialchars($email); ?></td>
                            <td><?php echo htmlspecialchars($phone); ?></td>
                            <td>
                                <button class="btn btn-sm btn-edit open-modal"
                                        data-modal-target="editSupplierModal"
                                        data-id="<?php echo $supplier['id_fournisseur']; ?>"
                                        data-name="<?php echo htmlspecialchars($supplier['nom_fournisseur']); ?>"
                                        data-contact="<?php echo htmlspecialchars($contact === 'N/A' ? '' : $contact); ?>"
                                        data-email="<?php echo htmlspecialchars($email === 'N/A' ? '' : $email); ?>"
                                        data-phone="<?php echo htmlspecialchars($phone === 'N/A' ? '' : $phone); ?>"
                                        data-address="<?php echo htmlspecialchars($address); ?>">
                                    Edit
                                </button>
                                <!-- Assurez-vous que deletefournisseur.php est le bon nom de fichier pour le script de suppression -->
                                <a href="delete_supplier.php?id=<?php echo $supplier['id_fournisseur']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ? Cela pourrait affecter les produits associés.');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">Aucun fournisseur trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajouter Fournisseur -->
<div id="addSupplierModal" class="modal"> <!-- ID correspond à data-modal-target -->
    <div class="modal-content">
        <span class="close-modal" data-modal-target="addSupplierModal">×</span> <!-- data-modal-target pour JS -->
        <h2>Ajouter un Nouveau Fournisseur</h2>
        <!-- L'action doit pointer vers votre script de traitement PHP -->
        <form action="addsupplier.php" method="POST">
            <div class="form-group">
                <label for="add_supplier_name">Nom du Fournisseur :</label>
                <input type="text" id="add_supplier_name" name="supplier_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="add_supplier_contact">Nom du Contact (optionnel) :</label>
                <input type="text" id="add_supplier_contact" name="supplier_contact" class="form-control">
            </div>
            <div class="form-group">
                <label for="add_supplier_email">Email (optionnel) :</label>
                <input type="email" id="add_supplier_email" name="supplier_email" class="form-control">
            </div>
            <div class="form-group">
                <label for="add_supplier_phone">Téléphone (optionnel) :</label>
                <input type="tel" id="add_supplier_phone" name="supplier_phone" class="form-control">
            </div>
            <div class="form-group">
                <label for="add_supplier_address">Adresse (optionnel) :</label>
                <textarea id="add_supplier_address" name="supplier_address" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer Fournisseur</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Modifier Fournisseur -->
<div id="editSupplierModal" class="modal"> <!-- ID correspond à data-modal-target -->
    <div class="modal-content">
        <span class="close-modal" data-modal-target="editSupplierModal">×</span> <!-- data-modal-target pour JS -->
        <h2>Modifier le Fournisseur</h2>
        <!-- L'action doit pointer vers votre script de traitement PHP -->
        <form action="editsupplier.php" method="POST">
            <input type="hidden" id="edit_supplier_id" name="supplier_id">
            <div class="form-group">
                <label for="edit_supplier_name">Nom du Fournisseur :</label>
                <input type="text" id="edit_supplier_name" name="supplier_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="edit_supplier_contact">Nom du Contact :</label>
                <input type="text" id="edit_supplier_contact" name="supplier_contact" class="form-control">
            </div>
            <div class="form-group">
                <label for="edit_supplier_email">Email :</label>
                <input type="email" id="edit_supplier_email" name="supplier_email" class="form-control">
            </div>
            <div class="form-group">
                <label for="edit_supplier_phone">Téléphone :</label>
                <input type="tel" id="edit_supplier_phone" name="supplier_phone" class="form-control">
            </div>
             <div class="form-group">
                <label for="edit_supplier_address">Adresse :</label>
                <textarea id="edit_supplier_address" name="supplier_address" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Mettre à Jour</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Supprimer Fournisseur -->
<div id="deleteSupplierModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" data-modal-target="deleteSupplierModal">×</span>
        <h2>Supprimer le Fournisseur</h2>
        <p>Êtes-vous sûr de vouloir supprimer ce fournisseur ? Cela pourrait affecter les produits associés.</p>
        <div class="form-actions">
            <button type="button" class="btn btn-secondary close-modal" data-modal-target="deleteSupplierModal">Annuler</button>
            <a href="#" id="confirmDeleteButton" class="btn btn-danger">Supprimer</a>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; // Votre footer commun pour l'admin ?>