<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header('Location: ../index.php'); exit;
}

$user_id = $_SESSION['user_id'];
$address = htmlspecialchars($_POST['address']);
$city = htmlspecialchars($_POST['city']);
$zip = htmlspecialchars($_POST['zip']);

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT c.article_id, c.quantity, a.price, s.quantity as stock_qty 
                           FROM cart c 
                           JOIN articles a ON c.article_id = a.id 
                           JOIN stock s ON a.id = s.article_id
                           WHERE c.user_id = :uid");
    $stmt->execute(['uid' => $user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_order = 0;
    foreach ($cart_items as $item) {
        $total_order += $item['price'] * $item['quantity'];
        if ($item['quantity'] > $item['stock_qty']) {
            throw new Exception("Stock insuffisant pour l'un des articles.");
        }
    }

    $stmtUser = $pdo->prepare("SELECT balance FROM users WHERE id = :uid FOR UPDATE");
    $stmtUser->execute(['uid' => $user_id]);
    $balance = $stmtUser->fetchColumn();

    if ($balance < $total_order) {
        throw new Exception("Solde insuffisant.");
    }

    $updateUser = $pdo->prepare("UPDATE users SET balance = balance - :total WHERE id = :uid");
    $updateUser->execute(['total' => $total_order, 'uid' => $user_id]);

    foreach ($cart_items as $item) {
        $updateStock = $pdo->prepare("UPDATE stock SET quantity = quantity - :q WHERE article_id = :aid");
        $updateStock->execute(['q' => $item['quantity'], 'aid' => $item['article_id']]);
    }

    $insertInvoice = $pdo->prepare("INSERT INTO invoices (user_id, amount, billing_address, billing_city, billing_zip) 
                                    VALUES (:uid, :amount, :addr, :city, :zip)");
    $insertInvoice->execute([
        'uid' => $user_id,
        'amount' => $total_order,
        'addr' => $address,
        'city' => $city,
        'zip' => $zip
    ]);

    $clearCart = $pdo->prepare("DELETE FROM cart WHERE user_id = :uid");
    $clearCart->execute(['uid' => $user_id]);

    $pdo->commit();

    header('Location: ../account.php?success=order');

} catch (Exception $e) {
    $pdo->rollBack();
    die("Erreur lors de la commande : " . $e->getMessage());
}