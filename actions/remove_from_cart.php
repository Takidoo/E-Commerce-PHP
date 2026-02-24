<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) { exit; }

$cart_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($cart_id) {

    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = :cid AND user_id = :uid");
    $stmt->execute([
        'cid' => $cart_id, 
        'uid' => $_SESSION['user_id']
    ]);
}

header('Location: ../cart.php');
exit;