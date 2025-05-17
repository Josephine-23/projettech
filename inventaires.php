<?php
session_start();
include_once 'db.php'; // Ou '../config/db.php' si db.php est dans le dossier parent
include_once 'header.php'; // Votre header commun pour l'admin
include_once 'check_admin.php';
?>
<link rel="stylesheet" href="admin_style.css">
<?php  
$page_title = "Nouvel Inventaire"; // Défini pour être utilisé dans header.php si besoin

// --- Récupération des produits pour l'inventaire ---
$products_for_inventory = [];
$products_by_category = [];

try {
    // Récupérer tous les produits actifs, leurs catégories, et les infos de stock
    // Assurez-vous que les noms de colonnes correspondent à votre base de données
    $sql = "SELECT 
                p.id_produit, 
                p.nom_produit, 
                p.conditionnement, 
                p.stock_actuel, 
                p.seuil_critique, 
                c.nom_categorie 
            FROM produits p
            JOIN categories c ON p.id_categorie = c.id_categorie
            ORDER BY c.nom_categorie, p.nom_produit"; // Trier par catégorie puis par nom de produit

    $stmt = $pdo->query($sql);
    $products_for_inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Regrouper les produits par catégorie pour l'affichage
    if (!empty($products_for_inventory)) {
        foreach ($products_for_inventory as $product) {
            $products_by_category[$product['nom_categorie']][] = $product;
        }
    }

} catch (PDOException $e) {
    // Gérer l'erreur de récupération des produits
    // Vous pouvez afficher un message d'erreur ou logger l'erreur
    // Pour l'instant, on laisse $products_for_inventory vide, le message "Aucun produit" s'affichera
    error_log("Erreur PDO lors de la récupération des produits pour l'inventaire : " . $e->getMessage());
    // Optionnel : Définir un message d'erreur pour l'utilisateur
    // $_SESSION['message'] = "Impossible de charger la liste des produits pour l'inventaire.";
    // $_SESSION['message_type'] = "danger";
}
?>

<!-- Le lien CSS est déjà dans votre header.php, sinon ajoutez-le ici -->
<!-- <link rel="stylesheet" href="admin_style.css"> -->

<div class="page-content-wrapper"> <!-- S'assurer que cette classe est stylée dans admin_style.css -->
    <nav class="content-tabs">
        <!-- Adaptez les liens et la classe 'active' en fonction de la page actuelle -->
        <a href="produits.php" class="tab-item <?php echo (basename($_SERVER['PHP_SELF']) == 'produits.php') ? 'active' : ''; ?>">Produits</a>
        <a href="categories.php" class="tab-item <?php echo (basename($_SERVER['PHP_SELF']) == 'categories.php') ? 'active' : ''; ?>">Catégories</a>
        <a href="inventaires.php" class="tab-item <?php echo (strpos(basename($_SERVER['PHP_SELF']), 'inventory') !== false || basename($_SERVER['PHP_SELF']) == 'inventaires.php') ? 'active' : ''; ?>">Inventaires</a>
        <a href="fournisseurs.php" class="tab-item <?php echo (basename($_SERVER['PHP_SELF']) == 'fournisseurs.php') ? 'active' : ''; ?>">Fournisseurs</a>
    </nav>

    <div class="page-title-bar">
         <a href="inventaires.php" class="back-link">← Retour aux Inventaires</a>
        <h1>Nouvel Inventaire</h1>
    </div>

    <?php if (isset($_SESSION['message'])): // Afficher les messages de session (par exemple, si redirection depuis process_new_inventory.php avec une erreur) ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type']); ?>">
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        </div>
    <?php endif; ?>

    <p class="form-description">
        Entrez les niveaux de stock actuels pour chaque produit. <br>
        Note: Les valeurs "Stock Actuel" sont celles enregistrées avant cet inventaire.
    </p>

    <!-- L'action pointe vers le script qui traitera les données de l'inventaire -->
    <form action="newinventory.php" method="POST" class="inventory-form">
        <div class="form-group inline-form-group">
            <label for="inventory_name">Réalisé par (Votre Nom) :</label>
            <input type="text" id="inventory_name" name="inventory_realisateur_name" class="form-control" placeholder="Ex: <?php echo htmlspecialchars($_SESSION['nom_utilisateur'] ?? 'Admin'); ?>" value="<?php echo htmlspecialchars($_SESSION['nom_utilisateur'] ?? ''); ?>" required>
        </div>
         <div class="form-group">
            <label for="inventory_notes">Notes (optionnel) :</label>
            <textarea id="inventory_notes" name="inventory_notes" class="form-control" rows="3" placeholder="Ex: Comptage fin de semaine, vérification livraisons..."></textarea>
        </div>

        <?php if (!empty($products_by_category)): ?>
            <?php foreach ($products_by_category as $category_name => $products_in_cat): // Renommé pour clarté ?>
                <div class="inventory-category-group">
                    <h2><?php echo htmlspecialchars($category_name); ?></h2>
                    <div class="table-responsive">
                        <table class="data-table inventory-table">
                            <thead>
                                <tr>
                                    <th>PRODUIT</th>
                                    <th>CONDITIONNEMENT</th>
                                    <th>STOCK ACTUEL (THÉORIQUE)</th>
                                    <th>NOUVEAU COMPTAGE (PHYSIQUE)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products_in_cat as $product): ?>
                                    <?php
                                        // Assurer que les clés existent pour éviter les erreurs si des données sont manquantes
                                        $stock_actuel_val = $product['stock_actuel'] ?? 0;
                                        $seuil_critique_val = $product['seuil_critique'] ?? 0; // Assurez-vous que cette colonne est sélectionnée
                                        $conditionnement_val = $product['conditionnement'] ?? 'N/A';
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['nom_produit']); ?></td>
                                        <td><?php echo htmlspecialchars($conditionnement_val); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($stock_actuel_val); ?>
                                            <?php if ($stock_actuel_val <= $seuil_critique_val && $seuil_critique_val > 0): // Ajouter une condition pour que le seuil soit > 0 pour être pertinent ?>
                                                <span class="status-badge status-critical">Critique</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <input type="number" name="new_count[<?php echo $product['id_produit']; ?>]" class="form-control form-control-sm new-count-input" min="0" placeholder="0" value="" required>
                                            <input type="hidden" name="previous_stock[<?php echo $product['id_produit']; ?>]" value="<?php echo htmlspecialchars($stock_actuel_val); ?>">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; // Fin de if (!empty($products_by_category)) ?>

        <?php if (empty($products_for_inventory)): ?>
            <div class="alert alert-warning">Aucun produit n'est configuré pour l'inventaire. Veuillez d'abord ajouter des produits dans la section "Produits".</div>
        <?php else: ?>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">Enregistrer l'Inventaire</button>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php include_once 'footer.php'; // Votre footer commun pour l'admin ?>