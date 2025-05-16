<?php
session_start(); // Important pour gérer les messages flash ou la redirection si déjà connecté
require_once 'db.php'; // Inclure la connexion à la DB

$errors = [];
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_utilisateur = trim($_POST['nom_utilisateur'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe'] ?? '';

    // Validations simples (vous pouvez ajouter plus de validations)
    if (empty($nom_utilisateur)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    } elseif (strlen($nom_utilisateur) < 3) {
        $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
    }

    if (empty($email)) {
        $errors[] = "L'adresse e-mail est requise.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse e-mail n'est pas valide.";
    }

    if (empty($mot_de_passe)) {
        $errors[] = "Le mot de passe est requis.";
    } elseif (strlen($mot_de_passe) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($mot_de_passe !== $confirmer_mot_de_passe) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Vérifier si l'utilisateur ou l'email existe déjà
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE nom_utilisateur = :nom_utilisateur OR email = :email");
        $stmt->execute(['nom_utilisateur' => $nom_utilisateur, 'email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = "Ce nom d'utilisateur ou cet e-mail est déjà utilisé.";
        }
    }

    if (empty($errors)) {
        // Hasher le mot de passe
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // Insérer dans la base de données
        try {
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe) VALUES (:nom_utilisateur, :email, :mot_de_passe)");
            $stmt->execute([
                'nom_utilisateur' => $nom_utilisateur,
                'email' => $email,
                'mot_de_passe' => $mot_de_passe_hash
            ]);
            $_SESSION['success_message'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            header("Location: connexion.php"); // Rediriger vers la page de connexion
            exit();
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Gestion de Stock INVADER</title>
    <!-- Intégration de Bootstrap CSS (via CDN pour la simplicité) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include 'link.php'; // Inclure le fichier de configuration de la base de données ?>
    <style>
        body { background-color: #f8f9fa; }
        .register-container { max-width: 500px; margin: 50px auto; padding: 30px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .register-container h2 { margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <h2>Créer un compte</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form action="inscription.php" method="post">
                <div class="mb-3">
                    <label for="nom_utilisateur" class="form-label">Nom d'utilisateur</label>
                    <input type="text" class="form-control" id="nom_utilisateur" name="nom_utilisateur" value="<?php echo htmlspecialchars($_POST['nom_utilisateur'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse e-mail</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="mot_de_passe" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                </div>
                <div class="mb-3">
                    <label for="confirmer_mot_de_passe" class="form-label">Confirmer le mot de passe</label>
                    <input type="password" class="form-control" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
            </form>
            <p class="mt-3 text-center">
                Déjà un compte ? <a href="connexion.php">Se connecter</a>
            </p>
        </div>
    </div>
    <!-- Optionnel: Bootstrap JS Bundle (si vous utilisez des composants JS de Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>