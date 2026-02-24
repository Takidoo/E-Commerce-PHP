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

$stmt = $pdo->query("SELECT id, username, email, role, balance FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateurs - Admin - EShop</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php require_once '../includes/header_admin.php'; ?>

    <div class="container mt-5">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Gérer les Utilisateurs</h1>
            <a href="index.php" class="btn btn-ghost">← Retour au dashboard</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom d'utilisateur</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Solde</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>#<?= $user['id'] ?></td>
                        <td><strong><?= htmlspecialchars($user['username']) ?></strong></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-size: 0.85rem; background-color: var(--bg-hover); color: var(--accent);">
                                <?= $user['role'] === 'admin' ? '👑 Admin' : '👤 User' ?>
                            </span>
                        </td>
                        <td><?= number_format($user['balance'], 2) ?> €</td>
                        <td>
                            <form action="edit_user.php" method="GET" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Éditer</button>
                            </form>
                            <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                <a href="delete_user.php?id=<?= $user['id'] ?>" 
                                   class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.85rem;"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($users)): ?>
            <div class="empty-state" style="margin-top: 2rem;">
                <p>Aucun utilisateur trouvé.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
