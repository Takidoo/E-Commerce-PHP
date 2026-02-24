<?php
/**
 * Récupère le rôle actuel de l'utilisateur depuis la base de données
 * Cela garantit que les changements de rôle sont immédiatement effectifs
 */
function getCurrentUserRole($user_id, $pdo) {
    if (!$user_id) return null;
    
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? $result['role'] : null;
}

/**
 * Vérifie si l'utilisateur est admin (en vérifiant la DB)
 */
function isAdmin($user_id, $pdo) {
    return getCurrentUserRole($user_id, $pdo) === 'admin';
}

/**
 * Vérifie si l'utilisateur est admin ou redirige vers l'accueil
 */
function requireAdmin($user_id, $pdo) {
    if (!isAdmin($user_id, $pdo)) {
        header('Location: ../index.php');
        exit;
    }
}

/**
 * Met à jour le rôle en session après une vérification DB
 * Appelé après chaque action importante
 */
function syncSessionRole($user_id, $pdo) {
    $role = getCurrentUserRole($user_id, $pdo);
    if ($role) {
        $_SESSION['role'] = $role;
    }
}
?>
