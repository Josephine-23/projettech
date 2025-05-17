<?php
$page_title = "Gestion des Catégories";
include_once 'header.php';
?>
<link rel="stylesheet" href="admin_style.css">
<?php
$categories = [
    ['id' => 1, 'name' => 'Soft', 'product_count' => 5],
    ['id' => 2, 'name' => 'Alcool Hard', 'product_count' => 2],
];
?>

<div class="page-content-wrapper">
    <nav class="content-tabs">
        <a href="produits.php" class="tab-item">Produits</a>
        <a href="categories.php" class="tab-item active">Catégories</a>
        <a href="inventaires.php" class="tab-item">Inventaires</a>
        <a href="fournisseurs.php" class="tab-item">Fournisseurs</a>
    </nav>

    <div class="page-title-bar">
        <h1>Catégories</h1>
        <div class="actions">
            <input type="search" id="categorySearch" placeholder="🔍 Rechercher des catégories..." class="search-input">
            <button class="btn btn-primary open-modal" data-modal-target="addCategoryModal">+ Ajouter une Catégorie</button>
        </div>
    </div>

     <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?>"><?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="data-table" id="categoriesTable">
            <thead>
                <tr>
                    <th>NOM DE LA CATÉGORIE</th>
                    <th>PRODUITS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td><?php echo htmlspecialchars($category['product_count']); ?> produit(s)</td>
                        <td>
                            <button class="btn btn-sm btn-edit open-modal"
                                    data-modal-target="editCategoryModal"
                                    data-id="<?php echo $category['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($category['name']); ?>">
                                Edit
                            </button>
                            <a href="deletecategory.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? Cela pourrait affecter les produits associés.');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                 <?php if (empty($categories)): ?>
                    <tr><td colspan="3" class="text-center">Aucune catégorie trouvée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajouter Catégorie -->
<div id="addCategoryModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" data-modal-target="addCategoryModal">×</span>
        <h2>Ajouter une Nouvelle Catégorie</h2>
        <form action="addcategory.php" method="POST">
            <div class="form-group">
                <label for="add_category_name">Nom de la Catégorie :</label>
                <input type="text" id="add_category_name" name="category_name" class="form-control" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer Catégorie</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Modifier Catégorie -->
<div id="editCategoryModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" data-modal-target="editCategoryModal">×</span>
        <h2>Modifier la Catégorie</h2>
        <form action="editcategory.php" method="POST">
            <input type="hidden" id="edit_category_id" name="category_id">
            <div class="form-group">
                <label for="edit_category_name">Nom de la Catégorie :</label>
                <input type="text" id="edit_category_name" name="category_name" class="form-control" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Mettre à Jour</button>
            </div>
        </form>
    </div>
</div>
<?php include_once 'footer.php'; ?>