<?php
// Simuler une session active pour l'affichage du nom d'utilisateur
// Dans une vraie application, cela viendrait de $_SESSION
$current_user_name = $_SESSION['nom_utilisateur'] ?? 'Admin User';
$current_page = basename($_SERVER['PHP_SELF']); // Pour mettre en évidence la page active
?>
<header class="admin-header">
    <div class="admin-header-container">
        <div class="admin-logo">
            <a href="admin_dashboard.php">INVADER <span class="logo-subtext">Stock Management</span></a>
        </div>
        <nav class="admin-nav">
            <ul>
                <li><a href="admin_products.php" class="<?php echo ($current_page == 'admin_products.php') ? 'active' : ''; ?>">Products</a></li>
                <li><a href="admin_categories.php" class="<?php echo ($current_page == 'admin_categories.php') ? 'active' : ''; ?>">Categories</a></li>
                <li><a href="admin_inventories.php" class="<?php echo ($current_page == 'admin_inventories.php') ? 'active' : ''; ?>">Inventories</a></li>
                <!-- Ajoutez d'autres liens si nécessaire (ex: Fournisseurs) -->
            </ul>
        </nav>
        <div class="admin-user-actions">
            <span><?php echo htmlspecialchars($current_user_name); ?></span>
            <a href="deconnexion.php" class="btn btn-outline-light btn-sm">Sign out</a>
        </div>
    </div>
</header>