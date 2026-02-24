<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth_helper.php';

$article_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$article_id) {
    header('Location: index.php');
    exit;
}

$query = "SELECT a.*, u.username as author_name, s.quantity as stock_qty 
          FROM articles a 
          JOIN users u ON a.author_id = u.id 
          LEFT JOIN stock s ON a.id = s.article_id 
          WHERE a.id = :id";

$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    die("Cet article n'existe pas.");
}

$user_is_author = false;
$user_is_admin = false;

if (isset($_SESSION['user_id'])) {
    $user_is_author = $_SESSION['user_id'] == $article['author_id'];
    $user_is_admin = getCurrentUserRole($_SESSION['user_id'], $pdo) === 'admin';
}
?>
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['name']) ?> - EShop</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="container mt-5">
        <a href="index.php" class="text-secondary" style="margin-bottom: 2rem; display: inline-block;">← Retour à l'accueil</a>

        <div class="product-detail">
            <div class="product-detail-image">
                <img src="<?= htmlspecialchars($article['image_link'] ?: 'assets/default.jpg') ?>" alt="<?= htmlspecialchars($article['name']) ?>">
            </div>

            <div class="product-detail-info">
                <h1><?= htmlspecialchars($article['name']) ?></h1>
                <p class="product-detail-seller">Vendu par <strong><?= htmlspecialchars($article['author_name']) ?></strong></p>
                <div class="product-detail-price"><?= number_format($article['price'], 2) ?> €</div>
                
                <div class="product-description">
                    <h3>Description</h3>
                    <p><?= nl2br(htmlspecialchars($article['description'])) ?></p>
                </div>

                <div class="stock-info">
                    Stock disponible : 
                    <?php if ($article['stock_qty'] > 0): ?>
                        <span class="stock-available"><?= $article['stock_qty'] ?> unités</span>
                    <?php else: ?>
                        <span class="stock-unavailable">Rupture de stock</span>
                    <?php endif; ?>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($article['stock_qty'] > 0): ?>
                        <form action="actions/add_to_cart.php" method="POST" class="product-actions">
                            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                            
                            <div class="form-group">
                                <label for="qty">Quantité :</label>
                                <div class="quantity-selector">
                                    <input type="number" id="qty" name="qty" value="1" min="1" max="<?= $article['stock_qty'] ?>">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Ajouter au panier</button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <p><a href="login.php">Connectez-vous</a> pour acheter cet article.</p>
                <?php endif; ?>
                
                <?php if ($user_is_author || $user_is_admin): ?>
                    <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border);">
                    <a href="edit.php?id=<?= $article['id'] ?>" class="btn btn-secondary" style="width: 100%;">✏️ Modifier cet article</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>