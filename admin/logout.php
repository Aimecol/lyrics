<?php
/**
 * Admin Logout - Song Lyrics Platform
 */

// Define platform constant
define('LYRICS_PLATFORM', true);

// Include configuration
require_once __DIR__ . '/../config/config.php';

// Log activity if admin was logged in
if (isAdminLoggedIn()) {
    logActivity('Admin logout', "User: {$_SESSION['admin_username']}");
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Set flash message for next session
session_start();
setFlashMessage('You have been successfully logged out.', 'success');

// Redirect to login page
redirect(SITE_URL . '/admin/login.php');
?>
