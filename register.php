<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/database.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $avatar_link = "";

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "Veuillez remplir tous les champs.";
    } 
    elseif ($password !== $confirm_password) {
        $message = "Les mots de passe ne correspondent pas.";
    } 
    else {
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {

            $upload_dir = "assets/uploads/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $img = $_FILES['avatar'];

            if ($img['size'] > 5 * 1024 * 1024) {
                die("Fichier trop volumineux.");
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($img['tmp_name']);

            $allowed_types = [
                'image/jpeg' => 'jpg',  
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (!array_key_exists($mime, $allowed_types)) {
                die("Type de fichier non autorisé.");
            }

            $extension = $allowed_types[$mime];
            $file_name = uniqid('', true) . "." . $extension;
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($img['tmp_name'], $file_path)) {
                $avatar_link = $file_path;
            } else {
                die("Erreur lors de l’upload.");
            }
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
        $stmt->execute(['email' => $email, 'username' => $username]);
        
        if ($stmt->rowCount() > 0) {
            $message = "Cet email ou ce nom d'utilisateur est déjà pris.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $insertQuery = "INSERT INTO users (username, email, password, balance, role, profile_picture) VALUES (:username, :email, :pass, 0, 'user', :pp)";
            $insertStmt = $pdo->prepare($insertQuery);
            
            try {
                $insertStmt->execute([
                    'username' => $username,
                    'email' => $email,
                    'pass' => $hashed_password,
                    'pp' => $avatar_link
                ]);


                $newUserId = $pdo->lastInsertId();

                $_SESSION['user_id'] = $newUserId;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user';

                header('Location: index.php');
                exit;

            } catch (PDOException $e) {
                $message = "Erreur lors de l'inscription : " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - EShop</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="form-container">
            <h2>Inscription</h2>

            <?php if (!empty($message)): ?>
                <div class="form-error"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required 
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required
                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="form-group">
                    <label for="avatar">Choisir un avatar</label>
                    <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">S'inscrire</button>
            </form>

            <div class="form-links">
                Déjà un compte ? <a href="login.php">Se connecter</a>
            </div>
        </div>
    </div>
    
</body>
</html>
