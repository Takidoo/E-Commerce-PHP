<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth_helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$article_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$article_id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT author_id FROM articles WHERE id = :id");
$stmt->execute(['id' => $article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header('Location: index.php');
    exit;
}

$current_role = getCurrentUserRole($_SESSION['user_id'], $pdo);

if ($_SESSION['user_id'] != $article['author_id'] && $current_role !== 'admin') {
    header('Location: index.php');
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

header('Location: index.php');
exit;
