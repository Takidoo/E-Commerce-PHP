<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Vérifier le rôle en base de données
requireAdmin($_SESSION['user_id'], $pdo);
syncSessionRole($_SESSION['user_id'], $pdo);

$message = "";
$msgType = "";

// Récupérer tous les articles
$stmt = $pdo->prepare("SELECT a.*, u.username as author_name, s.quantity as stock FROM articles a 
                      JOIN users u ON a.author_id = u.id 
                      LEFT JOIN stock s ON a.id = s.article_id 
                      ORDER BY a.publication_date DESC");
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles - Admin - EShop</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php require_once '../includes/header_admin.php'; ?>

    <div class="container mt-5">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Gérer les Articles</h1>
            <a href="index.php" class="btn btn-ghost">← Retour au dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="form-<?= $msgType === 'success' ? 'success' : 'error' ?>"><?= $message ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td>#<?= $article['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars(substr($article['name'], 0, 30)) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($article['author_name']) ?></td>
                        <td><?= number_format($article['price'], 2) ?> €</td>
                        <td><?= $article['stock'] ?? 0 ?> unités</td>
                        <td class="text-sm text-tertiary"><?= date('d/m/Y', strtotime($article['publication_date'])) ?></td>
                        <td>
                            <form action="edit_article.php" method="GET" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $article['id'] ?>">
                                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Éditer</button>
                            </form>
                            <a href="delete_article.php?id=<?= $article['id'] ?>" 
                               class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.85rem;"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($articles)): ?>
            <div class="empty-state" style="margin-top: 2rem;">
                <p>Aucun article trouvé.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
