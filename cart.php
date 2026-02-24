<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];


$query = "SELECT c.id as cart_id, c.quantity as cart_qty, a.*, s.quantity as stock_qty 
          FROM cart c 
          JOIN articles a ON c.article_id = a.id 
          JOIN stock s ON a.id = s.article_id
          WHERE c.user_id = :uid";

$stmt = $pdo->prepare($query);
$stmt->execute(['uid' => $user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);


$total_panier = 0;
foreach ($items as $item) {
    $total_panier += $item['price'] * $item['cart_qty'];
}

$stmtUser = $pdo->prepare("SELECT balance FROM users WHERE id = :uid");
$stmtUser->execute(['uid' => $user_id]);
$user_balance = $stmtUser->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - EShop</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Mon Panier</h1>

        <?php if (empty($items)): ?>
            <div class="empty-state">
                <p>Votre panier est vide.</p>
                <a href="index.php" class="btn btn-primary" style="margin-top: 1rem;">Continuer mes achats</a>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix Unitaire</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($item['name']) ?></strong>
                            </td>
                            <td><?= number_format($item['price'], 2) ?> €</td>
                            <td>
                                <form action="actions/update_cart.php" method="POST" style="display:flex; gap: 0.5rem;">
                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                    <input type="number" name="new_qty" value="<?= $item['cart_qty'] ?>" 
                                        min="1" max="<?= $item['stock_qty'] ?>" style="width: 70px;">
                                    <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">OK</button>
                                </form>
                                <small class="text-tertiary" style="display: block; margin-top: 0.5rem;">(Stock : <?= $item['stock_qty'] ?>)</small>
                            </td>
                            <td><?= number_format($item['price'] * $item['cart_qty'], 2) ?> €</td>
                            <td>
                                <a href="actions/remove_from_cart.php?id=<?= $item['cart_id'] ?>" 
                                   class="btn btn-danger" style="font-size: 0.85rem; padding: 0.5rem 1rem;"
                                   onclick="return confirm('Supprimer cet article ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="cart-summary-row">
                    <span class="cart-summary-label">Votre solde :</span>
                    <span class="cart-summary-value"><?= number_format($user_balance, 2) ?> €</span>
                </div>
                <div class="cart-summary-row" style="border-bottom: none;">
                    <span style="font-size: 1.25rem;">Total :</span>
                    <span class="cart-total"><?= number_format($total_panier, 2) ?> €</span>
                </div>

                <div style="margin-top: 2rem;">
                    <?php if ($user_balance >= $total_panier): ?>
                        <a href="cart_validate.php" class="btn btn-success" style="width: 100%; text-align: center;">Passer la commande</a>
                    <?php else: ?>
                        <p class="insufficient-balance">Solde insuffisant pour commander.</p>
                        <a href="account.php" class="btn btn-primary" style="width: 100%; text-align: center; margin-top: 1rem;">Recharger mon compte</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>