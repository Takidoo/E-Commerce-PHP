<?php
session_start();
require_once 'config/database.php';

$sql = "SELECT a.*, u.username as author_name 
        FROM articles a 
        JOIN users u ON a.author_id = u.id";

$params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $sql .= " WHERE a.name LIKE :search OR a.description LIKE :search";
    $params['search'] = "%$search%";
}

$sql .= " ORDER BY a.publication_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - EShop</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Découvrir les articles</h1>

        <div class="search-container">
            <form action="index.php" method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Rechercher un article..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </form>

            <?php if(isset($_GET['search'])): ?>
                <a href="index.php" class="reset-link">Réinitialiser la recherche</a>
            <?php endif; ?>
        </div>

        <?php if (count($articles) > 0): ?>
            <div class="products-grid">
                <?php foreach ($articles as $article): ?>
                    <div class="product-card">
                        <?php $img = !empty($article['image_link']) ? htmlspecialchars($article['image_link']) : 'assets/default.jpg'; ?>
                        <img src="<?= $img ?>" alt="<?= htmlspecialchars($article['name']) ?>" class="product-image">

                        <div class="product-body">
                            <div>
                                <h4 class="product-title"><?= htmlspecialchars($article['name']) ?></h4>
                                <div class="product-price"><?= number_format($article['price'], 2) ?> €</div>
                                <div class="product-seller">Vendu par <?= htmlspecialchars($article['author_name']) ?></div>
                            </div>

                            <a href="detail.php?id=<?= $article['id'] ?>" class="btn btn-primary">Voir les détails</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>Aucun article trouvé pour cette recherche.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>