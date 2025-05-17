<?php
// Ces inclusions devraient être tout en haut, avant toute sortie HTML si elles gèrent des sessions ou des redirections.
// Pour cet exemple, je suppose qu'elles ne font que définir des variables/constantes de DB.
session_start(); // Important si vous utilisez $_SESSION
 include 'link.php';
 include 'db.php'; 
?>
<link rel="stylesheet" href="indexstyle.css">
<?php   
// Simuler une session active pour l'affichage du nom d'utilisateur
$current_user_name = $_SESSION['nom_utilisateur'] ?? 'Admin User';
$current_page_admin_nav = basename($_SERVER['PHP_SELF']); // Pour mettre en évidence la page active dans l'admin nav
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVADER - Retro Bar Montpellier</title>

    <!-- Polices Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Barre de navigation Vitrine -->
    <header class="admin-header">
        <div class="admin-header-container">
            <div class="admin-logo">
                <a href="index.php">INVADER <span class="logo-subtext">Stock Management</span></a>
            </div>
    <header class="navbar vitrine-navbar">
        <div class="container nav-container">
            <div class="nav-logo">
                <a href="#">INVADER <span>- RETRO BAR</span></a>
            </div>
            <div class="lang-switcher">
                <a href="#">🇬🇧 ENGLISH</a>
            </div>
        </div>

        <nav class="admin-nav">
            <ul>
                <li><a href="produits.php" class="<?php echo ($current_page_admin_nav == 'produits.php') ? 'active' : ''; ?>">Products</a></li>
                <li><a href="categories.php" class="<?php echo ($current_page_admin_nav == 'categorie.php') ? 'active' : ''; ?>">Categories</a></li>
                <li><a href="inventaires.php" class="<?php echo ($current_page_admin_nav == 'inventaires.php') ? 'active' : ''; ?>">Inventaires</a></li>
                <li><a href="fournisseurs.php" calss="<?php echo ($current_page_admin_nav == 'fournisseurs.php') ? 'active' : ''; ?>">Fournisseurs</a></li>
            </ul>
        </nav>
        <div class="admin-user-actions">
            <a href="deconnexion.php" class="btn btn-outline-light btn-sm">Sign out</a>
        </div>
    </div>
    </header>
    <main>
        <!-- Section Hero -->
        <section class="hero">
            <div class="hero-content">
                <div class="welcome-line"></div>
                <p class="welcome-text">BIENVENUE À</p>
                <div class="hero-logo-block">
                    <h1>INVADER</h1>
                    <p class="sub-logo">RETRO BAR</p>
                    <p class="location">MONTPELLIER</p>
                </div>
            </div>
        </section>

        <!-- Section Tagline -->
        <section class="tagline-section">
            <div class="container">
                <h2>LE BAR POP-CULTURE ET GAMING</h2>
            </div>
        </section>

        <!-- Section Concept -->
        <section class="concept-section">
            <div class="container">
                <div class="concept-grid">
                    <div class="text-content">
                        <h3>RENDEZ-VOUS DANS UN DÉCOR FANTASTIQUE ET FUTURISTE</h3>
                        <p>Invader, c’est un <strong>concept bar</strong> totalement inédit en France et dans le Languedoc !</p>
                        <p>Avec vos amis, en famille ou entre collègues, rendez-vous au 34 rue de l’Université à Montpellier pour boire une <span class="highlight">bière</span> ou un <span class="highlight">cocktail</span> plongé dans une déco rétrofuturiste digne de Retour vers le Futur, <span class="highlight">Star Wars</span> ou Ready Player One.</p>
                        <p>Un style bien différent de la gare Saint-Roch et de la place de la Comédie !</p>
                        <a href="#" class="btn btn-primary">NOTRE CONCEPT</a>
                    </div>
                    <div class="image-content">
                        <img src="https://via.placeholder.com/500x350?text=Image+Bar+Int%C3%A9rieur" alt="Intérieur du bar Invader">
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Équipe -->
        <section class="team-section" style="padding: 60px 0; text-align: center; background-color: #15151d;">
            <div class="container">
                <h3>NOS ÉQUIPES SONT À VOTRE SERVICE</h3>
                <p>...</p>
                <a href="#" class="btn btn-secondary">PRIVATISER NOTRE BAR</a>
            </div>
        </section>

        <!-- Footer -->
        <footer class="site-footer">
            <div class="container footer-grid">
                <div class="footer-column">
                    <h4>ADRESSE</h4>
                    <p>34 RUE DE L'UNIVERSITÉ<br>34000 MONTPELLIER</p>
                    <p><a href="mailto:MONTPELLIER@INVADER.BAR">MONTPELLIER@INVADER.BAR</a></p>
                    <p><a href="tel:0467509273">04 67 50 92 73</a></p>
                    <div class="social-icons">
                        <a href="#"><img src="https://via.placeholder.com/30x30?text=FB&bg=3B5998&fg=FFFFFF" alt="Facebook"></a>
                        <a href="#"><img src="https://via.placeholder.com/30x30?text=IG&bg=E1306C&fg=FFFFFF" alt="Instagram"></a>
                        <a href="#"><img src="https://via.placeholder.com/30x30?text=TK&bg=000000&fg=FFFFFF" alt="TikTok"></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h4>LIENS RAPIDES</h4>
                    <ul>
                        <li><a href="#">ACCUEIL</a></li>
                        <li><a href="#">LE BAR</a></li>
                        <li><a href="#">LA CARTE</a></li>
                        <li><a href="#">OUVRIR UNE FRANCHISE</a></li>
                        <li><a href="#">CONTACT</a></li>
                        <li><a href="#">MENTIONS LÉGALES</a></li>
                        <li><a href="#">POLITIQUE DE CONFIDENTIALITÉ</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>HORAIRES</h4>
                    <p>LUNDI AU VENDREDI : 17H30 - 23H30</p>
                    <p>SAMEDI : 16H - 23H30</p>
                    <p>DIMANCHE : 16H - 23H</p>
                </div>
            </div>
            <div class="copyright">
                <p>© <?php echo date("Y"); ?> INVADER BAR - Tous droits réservés.</p>
            </div>
        </footer>
    </main>
</body>
</html>