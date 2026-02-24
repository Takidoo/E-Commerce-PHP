<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) { exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = filter_input(INPUT_POST, 'cart_id', FILTER_VALIDATE_INT);
    $new_qty = filter_input(INPUT_POST, 'new_qty', FILTER_VALIDATE_INT);

    if ($cart_id && $new_qty > 0) {
        $stmt = $pdo->prepare("SELECT s.quantity FROM stock s 
                               JOIN cart c ON s.article_id = c.article_id 
                               WHERE c.id = :cid AND c.user_id = :uid");
        $stmt->execute(['cid' => $cart_id, 'uid' => $_SESSION['user_id']]);
        $max_stock = $stmt->fetchColumn();

        if ($new_qty > $max_stock) {
            $new_qty = $max_stock;
        }

        $update = $pdo->prepare("UPDATE cart SET quantity = :qty WHERE id = :cid AND user_id = :uid");
        $update->execute(['qty' => $new_qty, 'cid' => $cart_id, 'uid' => $_SESSION['user_id']]);
    }
}

header('Location: ../cart.php');
exit;