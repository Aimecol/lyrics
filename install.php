<?php
/**
 * Installation Script for Song Lyrics Platform
 * Run this once to set up the database and initial configuration
 */

// Prevent running if already installed
if (file_exists('config/installed.lock')) {
    die('Platform is already installed. Delete config/installed.lock to reinstall.');
}

$error = '';
$success = '';
$step = intval($_GET['step'] ?? 1);

// Database configuration
$db_config = [
    'host' => $_POST['db_host'] ?? 'localhost',
    'username' => $_POST['db_username'] ?? 'root',
    'password' => $_POST['db_password'] ?? '',
    'database' => $_POST['db_name'] ?? 'lyrics_platform'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 1) {
        // Test database connection
        try {
            $connection = new mysqli($db_config['host'], $db_config['username'], $db_config['password']);
            
            if ($connection->connect_error) {
                throw new Exception("Connection failed: " . $connection->connect_error);
            }
            
            // Create database if it doesn't exist
            $connection->query("CREATE DATABASE IF NOT EXISTS `{$db_config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $connection->select_db($db_config['database']);
            
            // Read and execute schema
            $schema = file_get_contents('database/schema.sql');
            if ($schema === false) {
                throw new Exception("Could not read database schema file.");
            }
            
            // Execute schema (split by semicolons and execute each statement)
            $statements = array_filter(array_map('trim', explode(';', $schema)));
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    if (!$connection->query($statement)) {
                        throw new Exception("Error executing statement: " . $connection->error);
                    }
                }
            }
            
            $connection->close();
            
            // Update config file
            $config_content = file_get_contents('config/config.php');
            $config_content = str_replace("define('DB_HOST', 'localhost');", "define('DB_HOST', '{$db_config['host']}');", $config_content);
            $config_content = str_replace("define('DB_USERNAME', 'root');", "define('DB_USERNAME', '{$db_config['username']}');", $config_content);
            $config_content = str_replace("define('DB_PASSWORD', '');", "define('DB_PASSWORD', '{$db_config['password']}');", $config_content);
            $config_content = str_replace("define('DB_NAME', 'lyrics_platform');", "define('DB_NAME', '{$db_config['database']}');", $config_content);
            
            file_put_contents('config/config.php', $config_content);
            
            // Create installation lock file
            file_put_contents('config/installed.lock', date('Y-m-d H:i:s'));
            
            $success = 'Installation completed successfully!';
            $step = 2;
            
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Song Lyrics Platform</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .install-container {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .install-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .install-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.25rem;
            font-weight: 500;
            color: #495057;
        }
        .form-control {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            border: 1px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            width: 100%;
        }
        .btn-primary {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        .btn-success {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }
        .alert {
            padding: 0.75rem 1rem;
            border: 1px solid transparent;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .alert-error {
            background: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .alert-success {
            background: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.5rem;
            font-weight: 600;
            font-size: 0.8rem;
        }
        .step.active {
            background: #007bff;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
        .step.pending {
            background: #e9ecef;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1 class="install-title">🎵 Song Lyrics Platform</h1>
            <p style="color: #6c757d; margin: 0;">Installation Wizard</p>
        </div>
        
        <div class="step-indicator">
            <div class="step <?php echo $step >= 1 ? 'active' : 'pending'; ?>">1</div>
            <div class="step <?php echo $step >= 2 ? ($step === 2 ? 'completed' : 'active') : 'pending'; ?>">2</div>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($step === 1): ?>
            <h2 style="margin-bottom: 1rem; color: #333;">Database Configuration</h2>
            
            <form method="POST" action="?step=1">
                <div class="form-group">
                    <label for="db_host" class="form-label">Database Host</label>
                    <input type="text" id="db_host" name="db_host" class="form-control" 
                           value="<?php echo htmlspecialchars($db_config['host']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="db_username" class="form-label">Database Username</label>
                    <input type="text" id="db_username" name="db_username" class="form-control" 
                           value="<?php echo htmlspecialchars($db_config['username']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="db_password" class="form-label">Database Password</label>
                    <input type="password" id="db_password" name="db_password" class="form-control" 
                           value="<?php echo htmlspecialchars($db_config['password']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="db_name" class="form-label">Database Name</label>
                    <input type="text" id="db_name" name="db_name" class="form-control" 
                           value="<?php echo htmlspecialchars($db_config['database']); ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Install Database</button>
            </form>
            
        <?php elseif ($step === 2): ?>
            <h2 style="margin-bottom: 1rem; color: #333;">Installation Complete!</h2>
            
            <div style="margin-bottom: 2rem;">
                <p style="color: #6c757d; margin-bottom: 1rem;">
                    Your Song Lyrics Platform has been successfully installed.
                </p>
                
                <div style="background: #f8f9fa; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                    <h4 style="margin-bottom: 0.5rem; color: #333;">Default Admin Credentials:</h4>
                    <p style="margin: 0; font-family: monospace; font-size: 0.9rem;">
                        <strong>Username:</strong> admin<br>
                        <strong>Password:</strong> admin123
                    </p>
                </div>
                
                <p style="color: #dc3545; font-size: 0.9rem; margin-bottom: 1rem;">
                    <strong>Important:</strong> Please change the default admin password after logging in.
                </p>
            </div>
            
            <div style="display: flex; gap: 0.5rem;">
                <a href="index.php" class="btn btn-success" style="flex: 1;">View Website</a>
                <a href="admin/" class="btn btn-primary" style="flex: 1;">Admin Panel</a>
            </div>
            
            <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e9ecef; text-align: center;">
                <p style="color: #6c757d; font-size: 0.8rem; margin: 0;">
                    You can now delete this install.php file for security.
                </p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
