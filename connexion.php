<?php
session_start(); // Démarrer la session pour stocker les infos de l'utilisateur connecté
require_once 'db.php';

$errors = [];
$login_input = ''; // Pour pré-remplir le champ après une tentative échouée

// Si l'utilisateur est déjà connecté, redirigez-le vers la page principale (ex: produits.php)
if (isset($_SESSION['id_utilisateur'])) {
    header("Location: index.php"); // Ou votre page d'accueil après connexion
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_input = trim($_POST['login'] ?? ''); // Peut être nom_utilisateur ou email
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (empty($login_input) || empty($mot_de_passe)) {
        $errors[] = "Tous les champs sont requis.";
    } else {
        try {
            // Vérifier si c'est un email ou un nom d'utilisateur
            $sql = "SELECT id_utilisateur, nom_utilisateur, email, mot_de_passe, role FROM utilisateurs WHERE nom_utilisateur = :login OR email = :login";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['login' => $login_input]);
            $utilisateur = $stmt->fetch();

            if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
                // Mot de passe correct, démarrer la session
                $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
                $_SESSION['nom_utilisateur'] = $utilisateur['nom_utilisateur'];
                $_SESSION['role'] = $utilisateur['role']; // Utile pour la gestion des droits

                // Rediriger vers la page principale (par exemple, la page des produits)
                header("Location: produits.php"); // Adaptez cette redirection
                exit();
            } else {
                $errors[] = "Nom d'utilisateur/Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur de connexion : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion de Stock INVADER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include 'link.php'; // Inclure le fichier de configuration de la base de données ?>
    <style>
        body { background-color: #f8f9fa; }
        .login-container { max-width: 400px; margin: 50px auto; padding: 30px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .login-container h2 { margin-bottom: 20px; text-align: center; }
        body, .login-title { font-family: 'Inter', sans-serif; }
        .login-title { font-size: 24px; font-weight: 700; }
        .form-label { font-weight: 500; }
        .form-control { border-radius: 5px; }
        .btn-primary { background-color: #007bff; border: none; }
    </style>
</head>
<body>
    
<!-- DANS LE FICHIER connexion.php (à l'intérieur de la balise <body>) -->

<div class="container"> <!-- Ou une autre div pour centrer/contenir votre formulaire -->
    <div class="login-container"> <!-- Vous pouvez donner une classe à votre conteneur de formulaire -->
        <h2 class="login-title">Login</h2> <!-- Titre de votre page Figma -->

        <?php
        // Afficher le message de succès de l'inscription si présent
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']);
        }
        ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="connexion.php" method="post">
            <div class="form-group mb-3"> <!-- mb-3 est une classe Bootstrap pour la marge en bas -->
                <!-- Le label "Email address" comme dans votre Figma -->
                <label for="login" class="form-label">Email address</label>
                <input type="text" class="form-control" id="login" name="login" placeholder="Enter your email" value="<?php echo htmlspecialchars($login_input ?? ''); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="mot_de_passe" class="form-label">Password</label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" placeholder="Enter your password" required>
                <!-- Vous avez un "Forgot password?" dans votre Figma -->
                <div class="text-end mt-1">
                    <a href="#" class="forgot-password-link">Forgot password?</a>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button> <!-- Le bouton Login -->
        </form>
        <p class="mt-4 text-center signup-link"> <!-- mt-4 pour la marge en haut -->
            Don't have an account? <a href="inscription.php">Sign up</a>
        </p>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>