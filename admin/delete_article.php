<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

requireAdmin($_SESSION['user_id'], $pdo);
syncSessionRole($_SESSION['user_id'], $pdo);

$article_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$article_id) {
    header('Location: articles.php');
    exit;
}

try {
    $pdo->beginTransaction();

    $pdo->prepare("DELETE FROM stock WHERE article_id = :aid")->execute(['aid' => $article_id]);

    $pdo->prepare("DELETE FROM cart WHERE article_id = :aid")->execute(['aid' => $article_id]);

    $pdo->prepare("DELETE FROM articles WHERE id = :id")->execute(['id' => $article_id]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
}

header('Location: articles.php');
exit;
