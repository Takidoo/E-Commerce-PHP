<?php
session_start();
require_once 'config/database.php';

$is_my_account = true;
$user_id = $_SESSION['user_id'] ?? null;

if (isset($_GET['id']) && $_GET['id'] != $user_id) {
    $view_id = (int)$_GET['id'];
    $is_my_account = false;
} else {
    if (!$user_id) {
        header('Location: login.php');
        exit;
    }
    $view_id = $user_id;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $view_id]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$account) { die("Utilisateur introuvable."); }

$stmtArt = $pdo->prepare("SELECT * FROM articles WHERE author_id = :id ORDER BY publication_date DESC");
$stmtArt->execute(['id' => $view_id]);
$my_articles = $stmtArt->fetchAll(PDO::FETCH_ASSOC);

$invoices = [];
if ($is_my_account) {
    $stmtInv = $pdo->prepare("SELECT * FROM invoices WHERE user_id = :id ORDER BY transaction_date DESC");
    $stmtInv->execute(['id' => $user_id]);
    $invoices = $stmtInv->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte de <?= htmlspecialchars($account['username']) ?> - EShop</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="container mt-5">
        <div class="profile-header">
            <img src="<?= htmlspecialchars($account['profile_picture'] ?: 'assets/default-avatar.png') ?>" class="profile-picture">
            <div>
                <h1><?= htmlspecialchars($account['username']) ?></h1>
                <p class="profile-email"><?= htmlspecialchars($account['email']) ?></p>

                <?php if ($is_my_account): ?>
                    <div class="balance-badge">
                        Solde : <?= number_format($account['balance'], 2) ?> €
                    </div>
                    <form action="actions/add_money.php" method="POST" class="profile-actions">
                        <input type="number" name="amount" min="1" placeholder="Montant" required>
                        <button type="submit" class="btn btn-success">Ajouter</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Articles en vente</h2>
            <?php if (empty($my_articles)): ?>
                <div class="empty-state">
                    <p>Aucun article en vente pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($my_articles as $art): ?>
                        <div class="product-card">
                            <img src="<?= htmlspecialchars($art['image_link']) ?>" class="product-image">
                            <div class="product-body">
                                <h4 class="product-title"><?= htmlspecialchars($art['name']) ?></h4>
                                <div style="display: flex; gap: 0.5rem; margin-top: auto;">
                                    <a href="detail.php?id=<?= $art['id'] ?>" class="btn btn-primary" style="flex: 1; text-align: center;">Voir</a>
                                    <a href="edit.php?id=<?= $art['id'] ?>" class="btn btn-secondary" style="flex: 1; text-align: center;">Éditer</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($is_my_account): ?>
        <div class="section">
            <h2 class="section-title">Mes Factures</h2>
            <?php if (empty($invoices)): ?>
                <div class="empty-state">
                    <p>Aucune commande passée.</p>
                </div>
            <?php else: ?>
                <?php foreach ($invoices as $inv): ?>
                    <div class="invoice-card">
                        <strong>Commande #<?= $inv['id'] ?></strong>
                        <div class="invoice-detail">
                            <div>
                                <p class="text-sm"><span class="text-tertiary">Date :</span> <?= $inv['transaction_date'] ?></p>
                            </div>
                            <div>
                                <p class="text-sm"><span class="text-tertiary">Montant :</span> <strong><?= number_format($inv['amount'], 2) ?> €</strong></p>
                            </div>
                            <div>
                                <p class="text-sm"><span class="text-tertiary">Adresse :</span> <?= htmlspecialchars($inv['billing_address']) ?>, <?= htmlspecialchars($inv['billing_city']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

</body>
</html>
