<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Vérifier le rôle en base de données
requireAdmin($_SESSION['user_id'], $pdo);
syncSessionRole($_SESSION['user_id'], $pdo);

$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$user_id || $user_id === $_SESSION['user_id']) {
    header('Location: users.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // Supprimer les articles de l'utilisateur
    $pdo->prepare("DELETE FROM articles WHERE author_id = :uid")->execute(['uid' => $user_id]);

    // Supprimer le panier
    $pdo->prepare("DELETE FROM cart WHERE user_id = :uid")->execute(['uid' => $user_id]);

    // Supprimer les factures
    $pdo->prepare("DELETE FROM invoices WHERE user_id = :uid")->execute(['uid' => $user_id]);

    // Supprimer l'utilisateur
    $pdo->prepare("DELETE FROM users WHERE id = :id")->execute(['id' => $user_id]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
}

header('Location: users.php');
exit;
