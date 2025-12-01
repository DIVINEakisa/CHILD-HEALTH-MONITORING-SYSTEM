<?php
/**
 * Session Management
 * Child Health Monitoring System (CHMS)
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

/**
 * Check if user has a specific role
 * @param string $role
 * @return bool
 */
function hasRole($role) {
    return isLoggedIn() && $_SESSION['role'] === $role;
}

/**
 * Require login - redirect to login page if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /CHMS/login.php");
        exit();
    }
}

/**
 * Require specific role - redirect if user doesn't have the role
 * @param string $role
 */
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header("Location: /CHMS/index.php");
        exit();
    }
}

/**
 * Get current user ID
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 * @return string|null
 */
function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Get current user name
 * @return string|null
 */
function getCurrentUserName() {
    return $_SESSION['name'] ?? null;
}

/**
 * Login user
 * @param array $user
 */
function loginUser($user) {
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
}

/**
 * Logout user
 */
function logoutUser() {
    session_unset();
    session_destroy();
}

/**
 * Set flash message
 * @param string $message
 * @param string $type (success, error, info, warning)
 */
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Get and clear flash message
 * @return array|null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = [
            'message' => $_SESSION['flash_message'],
            'type' => $_SESSION['flash_type']
        ];
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return $message;
    }
    return null;
}
?>
