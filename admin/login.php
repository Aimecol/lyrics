<?php
/**
 * Admin Login Page - Song Lyrics Platform
 */

// Define platform constant
define('LYRICS_PLATFORM', true);

// Include configuration
require_once __DIR__ . '/../config/config.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin');
}

// Initialize admin model
$adminModel = new Admin($db);

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF token
    if (!verifyCSRFToken($csrf_token)) {
        $error = 'Invalid security token. Please try again.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Attempt authentication
        $admin = $adminModel->authenticate($username, $password);
        
        if ($admin) {
            // Set session variables
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['admin_full_name'] = $admin['full_name'];
            
            // Log activity
            logActivity('Admin login', "User: {$admin['username']}");
            
            // Redirect to admin dashboard
            redirect(SITE_URL . '/admin');
        } else {
            $error = 'Invalid username or password.';
            logActivity('Failed admin login attempt', "Username: $username");
        }
    }
}

$page_title = 'Admin Login';
$additional_css = ['admin.css'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' - ' . SITE_NAME; ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1 class="login-title">
                    <i class="fas fa-music"></i> <?php echo SITE_NAME; ?>
                </h1>
                <p class="login-subtitle">Admin Panel Login</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo sanitize($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="login-form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-control" 
                           placeholder="Enter your username"
                           value="<?php echo sanitize($_POST['username'] ?? ''); ?>"
                           required 
                           autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="Enter your password"
                           required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem;">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div style="margin-top: 2rem; text-align: center; padding-top: 1rem; border-top: 1px solid #e9ecef;">
                <p style="color: #6c757d; font-size: 0.85rem; margin-bottom: 0.5rem;">
                    <i class="fas fa-info-circle"></i> Default Login Credentials:
                </p>
                <p style="color: #6c757d; font-size: 0.8rem; margin: 0;">
                    Username: <strong>admin</strong> | Password: <strong>admin123</strong>
                </p>
            </div>
            
            <div style="margin-top: 1rem; text-align: center;">
                <a href="<?php echo SITE_URL; ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Website
                </a>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    
    <script>
        // Auto-focus on username field
        document.addEventListener('DOMContentLoaded', function() {
            const usernameField = document.getElementById('username');
            if (usernameField) {
                usernameField.focus();
            }
            
            // Handle Enter key to submit form
            document.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const form = document.querySelector('.login-form');
                    if (form) {
                        form.submit();
                    }
                }
            });
        });
    </script>
</body>
</html>
