<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $article_id = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
    $qty_to_add = filter_input(INPUT_POST, 'qty', FILTER_VALIDATE_INT) ?: 1;

    if (!$article_id) {
        header('Location: ../index.php');
        exit;
    }

    $stmtStock = $pdo->prepare("SELECT quantity FROM stock WHERE article_id = :id");
    $stmtStock->execute(['id' => $article_id]);
    $current_stock = $stmtStock->fetchColumn();

    if ($current_stock < $qty_to_add) {
        header("Location: ../detail.php?id=$article_id&error=stock");
        exit;
    }

    $stmtCheck = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = :uid AND article_id = :aid");
    $stmtCheck->execute(['uid' => $user_id, 'aid' => $article_id]);
    $existing_item = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($existing_item) {
        $new_qty = $existing_item['quantity'] + $qty_to_add;
        
        if ($new_qty > $current_stock) $new_qty = $current_stock;

        $updateStmt = $pdo->prepare("UPDATE cart SET quantity = :qty WHERE id = :id");
        $updateStmt->execute(['qty' => $new_qty, 'id' => $existing_item['id']]);
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO cart (user_id, article_id, quantity) VALUES (:uid, :aid, :qty)");
        $insertStmt->execute([
            'uid' => $user_id,
            'aid' => $article_id,
            'qty' => $qty_to_add
        ]);
    }

    header('Location: ../cart.php');
    exit;
} else {
    header('Location: ../index.php');
    exit;
}