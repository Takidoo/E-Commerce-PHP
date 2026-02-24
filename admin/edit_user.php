<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

requireAdmin($_SESSION['user_id'], $pdo);
syncSessionRole($_SESSION['user_id'], $pdo);

$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$user_id) {
    header('Location: users.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: users.php');
    exit;
}

$message = "";
$msgType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $balance = (float)$_POST['balance'];

    if (empty($username) || empty($email)) {
        $message = "Veuillez remplir tous les champs.";
        $msgType = "error";
    } else {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $checkStmt->execute(['email' => $email, 'id' => $user_id]);

        if ($checkStmt->rowCount() > 0) {
            $message = "Cet email est déjà utilisé.";
            $msgType = "error";
        } else {
            try {
                $updateStmt = $pdo->prepare("UPDATE users SET username = :username, email = :email, role = :role, balance = :balance WHERE id = :id");
                $updateStmt->execute([
                    'username' => $username,
                    'email' => $email,
                    'role' => $role,
                    'balance' => $balance,
                    'id' => $user_id
                ]);

                $message = "Utilisateur mis à jour avec succès !";
                $msgType = "success";

                $stmt->execute(['id' => $user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $message = "Erreur lors de la mise à jour : " . $e->getMessage();
                $msgType = "error";
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
    <title>Éditer Utilisateur - Admin - EShop</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container mt-5">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Éditer Utilisateur</h1>
            <a href="users.php" class="btn btn-ghost">← Retour</a>
        </div>

        <?php if ($message): ?>
            <div class="form-<?= $msgType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="card" style="max-width: 600px;">
            <form action="" method="POST">
                <div class="form-group">
                    <label>Nom d'utilisateur</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Rôle</label>
                        <select name="role" required>
                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Solde (€)</label>
                        <input type="number" step="0.01" name="balance" value="<?= $user['balance'] ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 1.5rem;">
                    Mettre à jour l'utilisateur
                </button>
            </form>
        </div>
    </div>

</body>
</html>
