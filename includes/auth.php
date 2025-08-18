<?php
// Authentication functions

function login($username, $password) {
    $user = new User();
    $authenticated_user = $user->authenticate($username, $password);
    
    if ($authenticated_user) {
        $_SESSION['user_id'] = $authenticated_user['id'];
        $_SESSION['username'] = $authenticated_user['username'];
        $_SESSION['role'] = $authenticated_user['role'];
        return true;
    }
    
    return false;
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

function get_current_user_data() {
    if (is_logged_in()) {
        $user = new User();
        return $user->findById($_SESSION['user_id']);
    }
    return null;
}

function require_role($required_role) {
    if (!is_logged_in()) {
        header('Location: index.php');
        exit;
    }
    
    $user_data = get_current_user_data();
    if ($user_data['role'] !== $required_role && $user_data['role'] !== 'admin') {
        // Redirect to dashboard or show access denied
        header('Location: dashboard.php');
        exit;
    }
}

?>