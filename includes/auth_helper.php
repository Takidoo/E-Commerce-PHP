<?php
function getCurrentUserRole($user_id, $pdo) {
    if (!$user_id) return null;
    
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? $result['role'] : null;
}

function isAdmin($user_id, $pdo) {
    return getCurrentUserRole($user_id, $pdo) === 'admin';
}

function requireAdmin($user_id, $pdo) {
    if (!isAdmin($user_id, $pdo)) {
        header('Location: ../index.php');
        exit;
    }
}

function syncSessionRole($user_id, $pdo) {
    $role = getCurrentUserRole($user_id, $pdo);
    if ($role) {
        $_SESSION['role'] = $role;
    }
}
?>
