<?php
session_start();
require_once '../config/database.php';

if (isset($_SESSION['user_id']) && isset($_POST['amount'])) {
    $amount = (float)$_POST['amount'];
    if ($amount > 0) {
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + :amt WHERE id = :uid");
        $stmt->execute(['amt' => $amount, 'uid' => $_SESSION['user_id']]);
    }
}
header('Location: ../account.php');
exit;