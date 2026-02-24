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

$stmtArticles = $pdo->query("SELECT COUNT(*) as count FROM articles");
$totalArticles = $stmtArticles->fetch(PDO::FETCH_ASSOC)['count'];

$stmtUsers = $pdo->query("SELECT COUNT(*) as count FROM users");
$totalUsers = $stmtUsers->fetch(PDO::FETCH_ASSOC)['count'];

$stmtInvoices = $pdo->query("SELECT COUNT(*) as count FROM invoices");
$totalInvoices = $stmtInvoices->fetch(PDO::FETCH_ASSOC)['count'];

$stmtRevenue = $pdo->query("SELECT SUM(amount) as total FROM invoices");
$totalRevenue = $stmtRevenue->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableaul Administrateur - EShop</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .admin-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .stat-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
        }

        .stat-card .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-label {
            color: var(--text-tertiary);
            font-size: 0.95rem;
        }

        .admin-nav {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .admin-link {
            background: linear-gradient(135deg, var(--accent), #9333ea);
            padding: 2rem;
            border-radius: 1rem;
            text-decoration: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border: 2px solid transparent;
        }

        .admin-link:hover {
            transform: translateY(-4px);
            border-color: var(--accent);
            box-shadow: 0 20px 40px rgba(168, 85, 247, 0.2);
        }

        .admin-link.articles {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        .admin-link.users {
            background: linear-gradient(135deg, #a855f7, #9333ea);
        }
    </style>
</head>
<body>
    <?php require_once '../includes/header_admin.php'; ?>

    <div class="container mt-5">
        <h1>Tableau Administrateur</h1>
        <p class="text-tertiary">Bienvenue <?= htmlspecialchars($_SESSION['username']) ?>, gérez votre plateforme ici.</p>

        <div class="admin-container">
            <div class="stat-card">
                <div class="stat-value"><?= $totalArticles ?></div>
                <div class="stat-label">Articles publiés</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $totalUsers ?></div>
                <div class="stat-label">Utilisateurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $totalInvoices ?></div>
                <div class="stat-label">Commandes</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= number_format($totalRevenue, 2) ?> €</div>
                <div class="stat-label">Chiffre d'affaires</div>
            </div>
        </div>

        <h2 style="margin-top: 3rem; margin-bottom: 1.5rem;">Gestion</h2>
        <div class="admin-nav">
            <a href="articles.php" class="admin-link articles">
                📰 Gérer les Articles
            </a>
            <a href="users.php" class="admin-link users">
                👥 Gérer les Utilisateurs
            </a>
        </div>
    </div>

</body>
</html>
