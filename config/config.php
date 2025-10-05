<?php
/**
 * Configuration file for Song Lyrics Platform
 * Contains database settings, site configuration, and constants
 */

// Prevent direct access
if (!defined('LYRICS_PLATFORM')) {
    die('Direct access not permitted');
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'lyrics_platform');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_NAME', 'Lyrics Platform');
define('SITE_DESCRIPTION', 'Your ultimate destination for song lyrics');
define('SITE_URL', 'http://localhost/lyrics');
define('SITE_EMAIL', 'admin@lyricsplatform.com');

// Pagination Settings
define('SONGS_PER_PAGE', 20);
define('ADMIN_ITEMS_PER_PAGE', 15);

// Security Settings
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes in seconds

// File Upload Settings (for future features)
define('MAX_UPLOAD_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Cache Settings
define('ENABLE_CACHE', false);
define('CACHE_DURATION', 3600); // 1 hour

// Error Reporting (set to false in production)
define('DEBUG_MODE', true);

// Timezone
date_default_timezone_set('UTC');

// Error reporting based on debug mode
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-include common functions and classes
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Song.php';
require_once __DIR__ . '/../includes/Artist.php';
require_once __DIR__ . '/../includes/Admin.php';

// Initialize database connection
try {
    $db = new Database();
} catch (Exception $e) {
    if (DEBUG_MODE) {
        die('Database connection failed: ' . $e->getMessage());
    } else {
        die('Database connection failed. Please try again later.');
    }
}
?>
