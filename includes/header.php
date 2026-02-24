<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_helper.php';

$is_logged_in = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? null;
$role = $_SESSION['role'] ?? null;
$current_page = basename($_SERVER['PHP_SELF']);

if ($is_logged_in) {
    $role = getCurrentUserRole($_SESSION['user_id'], $pdo);
}
?>

<header>
    <div class="header-container">
        <a href="index.php" class="logo">E-Girl shop</a>
        
        <nav>
            <a href="index.php" class="<?= $current_page === 'index.php' ? 'text-primary' : '' ?>">Accueil</a>
            
            <?php if ($is_logged_in): ?>
                <a href="sell.php" class="<?= $current_page === 'sell.php' ? 'text-primary' : '' ?>">Vendre</a>
                <a href="cart.php" class="<?= $current_page === 'cart.php' ? 'text-primary' : '' ?>">Panier</a>
            <?php endif; ?>
            
            <?php if ($role === 'admin'): ?>
                <a href="admin/index.php" class="<?= strpos($current_page, 'admin') !== false ? 'text-primary' : '' ?>" style="color: var(--accent);">👑 Admin</a>
            <?php endif; ?>
            
            <div class="nav-spacer"></div>
            
            <div class="nav-buttons">
                <?php if ($is_logged_in): ?>
                    <a href="account.php" class="btn btn-ghost">
                        <?= htmlspecialchars($username) ?>
                    </a>
                    <a href="actions/logout.php" class="btn btn-danger">Déconnexion</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary">Connexion</a>
                    <a href="register.php" class="btn btn-primary">S'inscrire</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>