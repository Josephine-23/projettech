<?php
session_start();
require_once 'db.php'; // Adaptez le chemin
$page_title = "Gestion des Produits";
include_once 'header.php'; // Inclut le header HTML et la nav admin
?>
<link rel="stylesheet" href="admin_style.css">
<?php   
// --- Gestion du tri ---
$sortable_columns = ['name' => 'p.nom_produit', 'category' => 'c.nom_categorie', 'stock' => 'p.stock_actuel', 'status' => 'status_order']; // 'status_order' sera un champ calculé
$sort_column = $_GET['sort'] ?? 'name'; // Colonne de tri par défaut
$sort_direction = strtoupper($_GET['dir'] ?? 'ASC'); // Direction par défaut

// Valider la colonne de tri et la direction
if (!array_key_exists($sort_column, $sortable_columns)) {
    $sort_column = 'name'; // Retour à la colonne par défaut si invalide
}
if (!in_array($sort_direction, ['ASC', 'DESC'])) {
    $sort_direction = 'ASC'; // Retour à la direction par défaut si invalide
}

$orderByClause = $sortable_columns[$sort_column] . " " . $sort_direction;

// Si on trie par statut, il faut une logique un peu plus complexe
// On peut créer un champ calculé pour l'ordre des statuts
$status_order_case = "CASE 
                        WHEN p.stock_actuel <= p.seuil_critique THEN 1 /* Critical */
                        WHEN p.stock_actuel <= p.seuil_critique * 2 THEN 2 /* Warning */
                        ELSE 3 /* OK */
                      END AS status_order";

// --- Récupération des produits ---
try {
    // Notez la jointure avec `categories` et l'alias pour les tables (p et c)
    // et l'ajout du CASE pour le tri par statut
    $sql = "SELECT p.id_produit, p.nom_produit, p.id_categorie, p.id_fournisseur, 
                   p.conditionnement, p.stock_actuel, p.stock_maximum, p.seuil_critique,
                   c.nom_categorie,
                   $status_order_case 
            FROM produits p
            JOIN categories c ON p.id_categorie = c.id_categorie
            ORDER BY $orderByClause";

    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll();

} catch (PDOException $e) {
    // Gérer l'erreur, par exemple :
    $_SESSION['message'] = "Erreur lors de la récupération des produits : " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    $products = []; // Tableau vide pour éviter les erreurs plus loin
}


// Logique pour les catégories et fournisseurs (pour les modals, comme avant)
try {
    $categories = $pdo->query("SELECT id_categorie, nom_categorie FROM categories ORDER BY nom_categorie")->fetchAll();
    $suppliers = $pdo->query("SELECT id_fournisseur, nom_fournisseur FROM fournisseurs ORDER BY nom_fournisseur")->fetchAll();
} catch (PDOException $e) {
    $categories = [];
    $suppliers = [];
    // Gérer l'erreur si nécessaire
}


// Fonctions pour générer les liens de tri (pour rendre le HTML plus propre)
function getSortLink($column_name, $display_text, $current_sort_column, $current_sort_direction) {
    $link_direction = ($current_sort_column === $column_name && $current_sort_direction === 'ASC') ? 'DESC' : 'ASC';
    $arrow = '';
    if ($current_sort_column === $column_name) {
        $arrow = ($current_sort_direction === 'ASC') ? ' ▲' : ' ▼';
    }
    return "<a href=\"?sort=$column_name&dir=$link_direction\">" . htmlspecialchars($display_text) . $arrow . "</a>";
}

?>

<div class="page-content-wrapper">
    <nav class="content-tabs">
        <a href="produits.php" class="tab-item active">Produits</a>
        <a href="categories.php" class="tab-item">Catégories</a>
        <a href="inventaires.php" class="tab-item">Inventaires</a>
        <a href="fournisseurs.php" class="tab-item">Fournisseurs</a>
    </nav>

    <div class="page-title-bar">
        <h1>Produits</h1>
        <div class="actions">
            <input type="search" id="productSearch" placeholder="🔍 Rechercher des produits..." class="search-input">
            <button class="btn btn-primary open-modal" data-modal-target="addProductModal">+ Ajouter un Produit</button>
        </div>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type']); ?>">
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="data-table" id="productsTable">
            <thead>
                <tr>
                    <th><?php echo getSortLink('name', 'NOM DU PRODUIT', $sort_column, $sort_direction); ?></th>
                    <th><?php echo getSortLink('category', 'CATÉGORIE', $sort_column, $sort_direction); ?></th>
                    <th><?php echo getSortLink('stock', 'STOCK ACTUEL', $sort_column, $sort_direction); ?></th>
                    <th><?php echo getSortLink('status', 'STATUT', $sort_column, $sort_direction); ?></th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                            // Calcul du statut (comme avant)
                            $stock_status = '';
                            $stock_status_class = '';
                            // Assurez-vous que seuil_critique n'est pas null pour éviter les erreurs
                            $seuil_critique_val = $product['seuil_critique'] ?? 0; 
                            $stock_actuel_val = $product['stock_actuel'] ?? 0;

                            if ($stock_actuel_val <= $seuil_critique_val) {
                                $stock_status = 'Reorder Needed';
                                $stock_status_class = 'status-critical';
                            } elseif ($stock_actuel_val <= $seuil_critique_val * 2) {
                                $stock_status = 'Running Low';
                                $stock_status_class = 'status-warning';
                            } else {
                                $stock_status = 'Sufficient';
                                $stock_status_class = 'status-ok';
                            }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['nom_produit']); ?></td>
                            <td><?php echo htmlspecialchars($product['nom_categorie']); ?></td> <!-- Utilisation de nom_categorie de la jointure -->
                            <td><?php echo htmlspecialchars($stock_actuel_val); ?> / <?php echo htmlspecialchars($product['stock_maximum'] ?? 'N/A'); ?></td>
                            <td><span class="status-badge <?php echo $stock_status_class; ?>"><?php echo $stock_status; ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-edit open-modal"
                                        data-modal-target="editProductModal"
                                        data-id="<?php echo $product['id_produit']; ?>"
                                        data-name="<?php echo htmlspecialchars($product['nom_produit']); ?>"
                                        data-category_id="<?php echo $product['id_categorie']; ?>"
                                        data-supplier_id="<?php echo $product['id_fournisseur']; ?>"
                                        data-packaging="<?php echo htmlspecialchars($product['conditionnement'] ?? ''); ?>"
                                        data-max_stock="<?php echo htmlspecialchars($product['stock_maximum'] ?? ''); ?>"
                                        data-critical_threshold="<?php echo htmlspecialchars($product['seuil_critique'] ?? ''); ?>">
                                    Edit
                                </button>
                                <a href="delete_product.php?id=<?php echo $product['id_produit']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">Aucun produit trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modals (Ajouter Produit, Modifier Produit) comme vous les aviez déjà -->
<!-- Modal Ajouter Produit -->
<div id="addProductModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" data-modal-target="addProductModal">×</span>
        <h2>Ajouter un Nouveau Produit</h2>
        <form action="process_add_product.php" method="POST">
            <div class="form-group">
                <label for="add_product_name">Nom du Produit :</label>
                <input type="text" id="add_product_name" name="product_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="add_category_id">Catégorie :</label>
                <select id="add_category_id" name="category_id" class="form-control" required>
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id_categorie']; ?>"><?php echo htmlspecialchars($cat['nom_categorie']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="add_supplier_id">Fournisseur :</label>
                <select id="add_supplier_id" name="supplier_id" class="form-control" required>
                     <option value="">-- Sélectionner --</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?php echo $sup['id_fournisseur']; ?>"><?php echo htmlspecialchars($sup['nom_fournisseur']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
             <div class="form-group">
                <label for="add_packaging">Conditionnement (ex: Bouteille 75cl, Canette 33cl) :</label>
                <input type="text" id="add_packaging" name="packaging" class="form-control">
            </div>
            <div class="form-group">
                <label for="add_max_stock">Stock Maximum :</label>
                <input type="number" id="add_max_stock" name="max_stock" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label for="add_critical_threshold">Seuil de Stock Critique :</label>
                <input type="number" id="add_critical_threshold" name="critical_threshold" class="form-control" min="0" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer Produit</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Modifier Produit -->
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" data-modal-target="editProductModal">×</span>
        <h2>Modifier le Produit</h2>
        <form action="editproduct.php" method="POST">
            <input type="hidden" id="edit_product_id" name="product_id">
            <div class="form-group">
                <label for="edit_product_name">Nom du Produit :</label>
                <input type="text" id="edit_product_name" name="product_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="edit_category_id">Catégorie :</label>
                <select id="edit_category_id" name="category_id" class="form-control" required>
                     <option value="">-- Sélectionner --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id_categorie']; ?>"><?php echo htmlspecialchars($cat['nom_categorie']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_supplier_id">Fournisseur :</label>
                <select id="edit_supplier_id" name="supplier_id" class="form-control" required>
                     <option value="">-- Sélectionner --</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?php echo $sup['id_fournisseur']; ?>"><?php echo htmlspecialchars($sup['nom_fournisseur']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_packaging">Conditionnement :</label>
                <input type="text" id="edit_packaging" name="packaging" class="form-control">
            </div>
            <div class="form-group">
                <label for="edit_max_stock">Stock Maximum :</label>
                <input type="number" id="edit_max_stock" name="max_stock" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label for="edit_critical_threshold">Seuil de Stock Critique :</label>
                <input type="number" id="edit_critical_threshold" name="critical_threshold" class="form-control" min="0" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Mettre à Jour</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete Produit --> 
 <div id="deleteProductModal" class="modal"> 
<div class="modal-content"> 
    <span class="close-modal" data-modal-target="deleteProductModal">×</span> 
    <h2>Supprimer le Produit</h2> 
    <p>Êtes-vous sûr de vouloir supprimer ce produit ? Cette action est irréversible.</p> 
    <form action="delete_product.php" method="POST"> 
        <input type="hidden" id="delete_product_id" name="product_id"> 
        <div class="form-actions"> 
            <button type="submit" class="btn btn-danger">Supprimer</button> 
            <button type="button" class="btn btn-secondary close-modal" data-modal-target="deleteProductModal">Annuler</button> 
        </div> 
    </form> 
</div>

<?php include_once 'footer.php'; ?>