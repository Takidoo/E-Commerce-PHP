<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$user_id = $_SESSION['user_id'];

$query = "SELECT c.quantity as cart_qty, a.price 
          FROM cart c JOIN articles a ON c.article_id = a.id 
          WHERE c.user_id = :uid";
$stmt = $pdo->prepare($query);
$stmt->execute(['uid' => $user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($items as $item) { $total += $item['price'] * $item['cart_qty']; }

if ($total <= 0) { header('Location: index.php'); exit; }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation Commande - EShop</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Finaliser ma commande</h1>

        <div class="card" style="max-width: 600px; margin: 2rem 0;">
            <p style="margin-bottom: 1.5rem;">Montant total à régler :</p>
            <div style="font-size: 2.5rem; font-weight: 700; color: var(--success); margin-bottom: 2rem;">
                <?= number_format($total, 2) ?> €
            </div>

            <form action="actions/process_order.php" method="POST" class="product-actions">
                <h3 style="margin-bottom: 1.5rem;">Informations de facturation</h3>
                
                <div class="form-group">
                    <label>Adresse :</label>
                    <input type="text" name="address" required placeholder="123 rue de PHP">
                </div>
                
                <div class="form-group">
                    <label>Ville :</label>
                    <input type="text" name="city" required placeholder="Paris">
                </div>
                
                <div class="form-group">
                    <label>Code Postal :</label>
                    <input type="text" name="zip" required placeholder="75000">
                </div>

                <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 1.5rem;">
                    Payer et générer la facture
                </button>
                
                <a href="cart.php" class="btn btn-ghost" style="width: 100%; text-align: center; margin-top: 1rem;">Retour au panier</a>
            </form>
        </div>
    </div>

</body>
</html>