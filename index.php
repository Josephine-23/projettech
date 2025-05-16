<?php
// Ces inclusions devraient être tout en haut, avant toute sortie HTML si elles gèrent des sessions ou des redirections.
// Pour cet exemple, je suppose qu'elles ne font que définir des variables/constantes de DB.
session_start(); // Important si vous utilisez $_SESSION
 include 'link.php';
 include 'db.php'; 

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

    <!-- Votre CSS -->
    <style>
        :root {
            --vitrine-background-color: #15151d;
            --vitrine-background-section: #12121a;
            --vitrine-invader-purple: #6200ea;
            --vitrine-invader-purple-glow: #7e3ff2;
            --vitrine-invader-pink: #ff00ff;
            --vitrine-invader-yellow: #f7df1e;
            --vitrine-invader-red-button: #d90429;
            --vitrine-text-color: #e0e0e0;
            --vitrine-text-color-darker: #a0a0a0;

            --vitrine-font-primary: 'Orbitron', sans-serif;
            --vitrine-font-secondary: 'Roboto', sans-serif;
            --vitrine-font-tertiary: 'Courier New', monospace;
            --admin-header-bg: #23272b; 
            --admin-header-text: #f8f9fa;
            --admin-header-link-hover-bg: rgba(255,255,255,0.1);
            --admin-primary: #6a0dad; 
        }

        {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--vitrine-font-secondary); 
            background-color: var(--vitrine-background-color); 
            color: var(--vitrine-text-color); 
            line-height: 1.7;
            font-size: 16px;
        }

        .container { 
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

  
        .admin-header {
            background-color: var(--admin-header-bg);
            color: var(--admin-header-text);
            padding: 10px 0; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed; 
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1010; 
             }
        .admin-header-container {
            width: 95%; 
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-logo a {
            color: var(--admin-header-text);
            text-decoration: none;
            font-size: 1.3em; 
            font-weight: bold;
            font-family: var(--vitrine-font-primary); 
        }
        .admin-logo .logo-subtext {
            font-size: 0.7em;
            color: #adb5bd;
            font-weight: normal;
        }
        .admin-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .admin-nav ul li a {
            color: #ced4da;
            text-decoration: none;
            padding: 8px 12px; 
            border-radius: 4px;
            transition: background-color 0.2s ease, color 0.2s ease;
            font-size: 0.9em;
        }
        .admin-nav ul li a:hover,
        .admin-nav ul li a.active {
            background-color: var(--admin-header-link-hover-bg);
            color: var(--admin-header-text);
        }
        .admin-user-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 0.9em;
        }
        .admin-user-actions span {
            color: #e9ecef;
        }

        .admin-user-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            border-radius: 0.2rem;
            text-decoration: none;
        }
        .admin-user-actions .btn-outline-light {
            color: var(--admin-header-bg); 
            background-color: var(--admin-header-text); 
            border: 1px solid var(--admin-header-text);
        }
        .admin-user-actions .btn-outline-light:hover {
            color: var(--admin-header-text); 
            background-color: rgba(0,0,0, 0.2); 
        }
        .navbar.vitrine-navbar { 
            background-color: rgba(10, 10, 15, 0.85);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: fixed;
            width: 100%;
    
            top: 50px; 
            left: 0;
            z-index: 1000;
            padding: 15px 0;
        }
        body {

            padding-top: 120px;
        }
        .nav-container { 
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo a {
            font-family: var(--vitrine-font-primary);
            font-size: 1.8em;
            font-weight: 900;
            color: var(--vitrine-text-color);
        }
        .nav-logo span {
            font-size: 0.6em;
            font-weight: 400;
            color: var(--vitrine-text-color-darker);
            letter-spacing: 1px;
        }

        .navbar.vitrine-navbar nav ul { 
            list-style: none;
            display: flex;
        }

        .navbar.vitrine-navbar nav ul li { 
            margin-left: 30px;
        }

        .navbar.vitrine-navbar nav ul li a { 
            color: var(--vitrine-text-color);
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9em;
        }
        .navbar.vitrine-navbar nav ul li a:hover,
        .navbar.vitrine-navbar nav ul li a.active {
            color: var(--vitrine-invader-yellow);
        }

        .lang-switcher a {
            color: var(--vitrine-text-color);
            font-weight: bold;
            font-size: 0.9em;
        }
        .hero {
            min-height: calc(100vh - 120px); 
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding-bottom: 40px;
            background: var(--vitrine-background-color);
        }

        .welcome-line {
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--vitrine-invader-purple-glow), var(--vitrine-invader-pink));
            margin: 0 auto 15px;
            border-radius: 2px;
        }

        .welcome-text {
            font-family: var(--vitrine-font-primary);
            color: var(--vitrine-text-color-darker);
            font-size: 1.1em;
            margin-bottom: 25px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .hero-logo-block {
            border: 4px solid var(--vitrine-invader-purple);
            padding: 25px 40px;
            border-radius: 20px;
            box-shadow: 0 0 30px rgba(126, 63, 242, 0.5), 0 0 15px rgba(126, 63, 242, 0.7) inset;
            display: inline-block;
        }

        .hero-logo-block h1 {
            font-size: clamp(3em, 10vw, 6em);
            font-weight: 900;
            margin: 0;
            color: var(--vitrine-text-color);
            text-shadow: 0 0 8px #fff, 0 0 15px var(--vitrine-invader-pink), 0 0 20px var(--vitrine-invader-pink);
            letter-spacing: 3px;
        }

        .hero-logo-block .sub-logo {
            font-family: var(--vitrine-font-primary);
            font-size: clamp(0.8em, 3vw, 1.5em);
            margin: 8px 0 0;
            color: #ccc;
            letter-spacing: 6px;
            text-transform: uppercase;
        }
        .hero-logo-block .location {
            font-family: var(--vitrine-font-primary);
            font-size: clamp(0.7em, 2.5vw, 1.2em);
            margin: 12px 0 0;
            color: var(--vitrine-text-color-darker);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .tagline-section {
            padding: 70px 0;
            text-align: center;
            background-color: var(--vitrine-background-section);
        }
        .tagline-section h2 {
            font-size: clamp(1.5em, 5vw, 2.5em);
            font-weight: 700;
            color: var(--vitrine-invader-purple-glow);
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 0 10px var(--vitrine-invader-pink), 0 0 5px var(--vitrine-invader-purple-glow);
        }


        .concept-section {
            padding: 70px 0;
            background-color: var(--vitrine-background-color);
        }
        .concept-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 50px;
        }
        .concept-section .text-content h3 {
            color: var(--vitrine-invader-yellow);
            font-size: clamp(1.3em, 4vw, 1.8em);
            margin-bottom: 25px;
        }
        .concept-section .text-content p {
            margin-bottom: 20px;
            color: var(--vitrine-text-color-darker);
            font-size: 1.05em;
        }
        .concept-section .text-content strong {
            color: var(--vitrine-text-color);
            font-weight: bold;
        }
        .concept-section .text-content .highlight {
            color: var(--vitrine-invader-yellow);
            font-weight: bold;
        }
        .concept-section .image-content img {
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.4);
        }

        .btn {
            display: inline-block;
            padding: 14px 30px;
            text-decoration: none;
            text-transform: uppercase;
            font-weight: bold;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            letter-spacing: 1.2px;
            font-family: var(--vitrine-font-primary);
            margin-top: 15px;
        }
        .btn-primary {
            background-color: var(--vitrine-invader-red-button);
            color: var(--vitrine-text-color);
            box-shadow: 0 4px 15px rgba(217, 4, 41, 0.4);
        }
        .btn-primary:hover {
            background-color: #b80322;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(217, 4, 41, 0.6);
        }
        .btn-secondary {
            background-color: var(--vitrine-invader-purple);
            color: var(--vitrine-text-color);
            box-shadow: 0 4px 15px rgba(98, 0, 234, 0.4);
        }
        .btn-secondary:hover {
            background-color: #4e00b8;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(98, 0, 234, 0.6);
        }


        .team-section {
            background-color: var(--vitrine-background-section);
             padding: 60px 0; text-align: center; 
        }
        .team-section h3 {
            color: var(--vitrine-invader-yellow);
            font-size: clamp(1.3em, 4vw, 1.8em);
            margin-bottom: 25px;
        }

        .site-footer {
            background-color: #08080c;
            padding: 60px 0 30px;
            font-size: 0.95em;
            color: var(--vitrine-text-color-darker);
        }
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        .footer-column h4 {
            color: var(--vitrine-invader-yellow);
            margin-bottom: 20px;
            font-size: 1.2em;
            text-transform: uppercase;
        }
        .footer-column p, .footer-column ul {
            margin-bottom: 12px;
        }
        .footer-column ul {
            list-style: none;
        }
        .footer-column li {
            margin-bottom: 10px;
        }
        .footer-column a {
            color: var(--vitrine-text-color-darker);
        }
        .footer-column a:hover {
            color: var(--vitrine-invader-yellow);
        }
        .social-icons img {
            width: 28px;
            margin-right: 12px;
            opacity: 0.8;
        }
        .social-icons img:hover {
            opacity: 1;
            transform: scale(1.1);
        }
        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.85em;
            color: #777;
        }
        @media (max-width: 992px) {
            .concept-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .concept-section .image-content {
                margin-top: 40px;
                order: -1;
            }
            .concept-section .image-content img {
                margin: 0 auto;
            }
        }

        @media (max-width: 768px) {
             body {
                font-size: 15px;
            
                padding-top: 100px; 
            }
            .navbar.vitrine-navbar {
                padding: 10px 0;
            
                top: 50px; 
            }
            .nav-container { 
                flex-direction: column;
                gap: 10px;
            }

            .navbar.vitrine-navbar {
                padding: 10px 0;
            
                top: 60px; 
            .nav-container { 
                flex-direction: column;
                gap: 10px;
            }
            .navbar.vitrine-navbar nav ul { 
                margin-top: 10px;
                flex-wrap: wrap;
                justify-content: center;
            }
            .navbar.vitrine-navbar nav ul li { 
                margin: 5px 10px;
            }

            .hero {
                min-height: calc(100vh - 100px); 
            }

            .tagline-section, .concept-section, .team-section, .site-footer {
                padding: 50px 0;
            }
            .footer-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .footer-column .social-icons {
                justify-content: center;
                display: flex;
            }
        }
    </style>
</head>
<body>

    <!-- Barre de navigation Vitrine -->
    <header class="navbar vitrine-navbar">
        <div class="container nav-container">
            <div class="nav-logo">
                <a href="#">INVADER <span>- RETRO BAR</span></a>
            </div>
            <div class="lang-switcher">
                <a href="#">🇬🇧 ENGLISH</a>
            </div>
        </div>
            <header class="admin-header">
        <div class="admin-header-container">
            <div class="admin-logo">
                <a href="admin_dashboard.php">INVADER <span class="logo-subtext">Stock Management</span></a>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="admin_products.php" class="<?php echo ($current_page_admin_nav == 'admin_products.php') ? 'active' : ''; ?>">Products</a></li>
                    <li><a href="categorie.php" class="<?php echo ($current_page_admin_nav == 'admin_categories.php') ? 'active' : ''; ?>">Categories</a></li>
                    <li><a href="admin_inventories.php" class="<?php echo ($current_page_admin_nav == 'admin_inventories.php') ? 'active' : ''; ?>">Inventories</a></li>
                </ul>
            </nav>
            <div class="admin-user-actions">
                <a href="deconnexion.php" class="btn btn-outline-light btn-sm">Sign out</a>
            </div>
        </div>
    </header>
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