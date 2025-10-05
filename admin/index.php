<?php
/**
 * Admin Dashboard - Song Lyrics Platform
 */

// Define platform constant
define('LYRICS_PLATFORM', true);

// Include configuration
require_once __DIR__ . '/../config/config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

// Initialize admin model
$adminModel = new Admin($db);

// Get dashboard statistics
$stats = $adminModel->getDashboardStats();

$page_title = 'Admin Dashboard';
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
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div style="padding: 1rem; border-bottom: 1px solid #495057;">
                <h3 style="color: white; margin: 0; font-size: 1.2rem;">
                    <i class="fas fa-music"></i> <?php echo SITE_NAME; ?>
                </h3>
                <p style="color: #adb5bd; font-size: 0.8rem; margin: 0.25rem 0 0 0;">Admin Panel</p>
            </div>
            
            <nav>
                <ul class="admin-nav">
                    <li class="admin-nav-item">
                        <a href="<?php echo SITE_URL; ?>/admin" class="admin-nav-link active">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="<?php echo SITE_URL; ?>/admin/songs.php" class="admin-nav-link">
                            <i class="fas fa-music"></i> Manage Songs
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="<?php echo SITE_URL; ?>/admin/add-song.php" class="admin-nav-link">
                            <i class="fas fa-plus"></i> Add New Song
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="<?php echo SITE_URL; ?>/admin/artists.php" class="admin-nav-link">
                            <i class="fas fa-microphone"></i> Manage Artists
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="<?php echo SITE_URL; ?>/admin/genres.php" class="admin-nav-link">
                            <i class="fas fa-tags"></i> Manage Genres
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="<?php echo SITE_URL; ?>" class="admin-nav-link" target="_blank">
                            <i class="fas fa-external-link-alt"></i> View Website
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="<?php echo SITE_URL; ?>/admin/logout.php" class="admin-nav-link">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <!-- Header -->
            <header class="admin-header">
                <div>
                    <h1 style="margin: 0; color: #333;">Dashboard</h1>
                    <p style="margin: 0; color: #6c757d; font-size: 0.9rem;">Welcome back, <?php echo sanitize($_SESSION['admin_full_name'] ?? $_SESSION['admin_username']); ?>!</p>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="mobile-menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div style="color: #6c757d; font-size: 0.85rem;">
                        <i class="fas fa-clock"></i> <?php echo date('M j, Y g:i A'); ?>
                    </div>
                </div>
            </header>
            
            <!-- Flash Messages -->
            <?php 
            $flash = getFlashMessage();
            if ($flash): 
            ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo sanitize($flash['message']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($stats['total_songs']); ?></div>
                    <div class="stat-label">Total Songs</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($stats['total_artists']); ?></div>
                    <div class="stat-label">Total Artists</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($stats['total_views']); ?></div>
                    <div class="stat-label">Total Views</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number" style="color: #28a745;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-label">System Status</div>
                </div>
            </div>
            
            <!-- Recent Songs -->
            <div class="admin-table">
                <div style="padding: 1rem; border-bottom: 1px solid #e9ecef; background: #f8f9fa;">
                    <h3 style="margin: 0; color: #333;">Recent Songs</h3>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Song Title</th>
                            <th>Artist</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($stats['recent_songs'])): ?>
                            <?php foreach ($stats['recent_songs'] as $song): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo sanitize($song['title']); ?></strong>
                                    </td>
                                    <td><?php echo sanitize($song['artist_name']); ?></td>
                                    <td><?php echo timeAgo($song['created_at']); ?></td>
                                    <td class="actions">
                                        <a href="<?php echo SITE_URL; ?>/song/<?php echo $song['id'] ?? 0; ?>" 
                                           class="btn btn-sm btn-primary" 
                                           target="_blank" 
                                           title="View Song">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/admin/edit-song.php?id=<?php echo $song['id'] ?? 0; ?>" 
                                           class="btn btn-sm btn-warning" 
                                           title="Edit Song">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #6c757d; padding: 2rem;">
                                    No songs found. <a href="<?php echo SITE_URL; ?>/admin/add-song.php">Add your first song</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div style="padding: 1rem; text-align: center; border-top: 1px solid #e9ecef;">
                    <a href="<?php echo SITE_URL; ?>/admin/songs.php" class="btn btn-primary">
                        <i class="fas fa-list"></i> View All Songs
                    </a>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top: 2rem;">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div style="padding: 1rem;">
                        <a href="<?php echo SITE_URL; ?>/admin/add-song.php" class="btn btn-primary" style="width: 100%; margin-bottom: 0.5rem;">
                            <i class="fas fa-plus"></i> Add New Song
                        </a>
                        <a href="<?php echo SITE_URL; ?>/admin/songs.php" class="btn btn-secondary" style="width: 100%; margin-bottom: 0.5rem;">
                            <i class="fas fa-music"></i> Manage Songs
                        </a>
                        <a href="<?php echo SITE_URL; ?>/admin/artists.php" class="btn btn-secondary" style="width: 100%;">
                            <i class="fas fa-microphone"></i> Manage Artists
                        </a>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">System Information</h3>
                    </div>
                    <div style="padding: 1rem; font-size: 0.85rem;">
                        <div style="margin-bottom: 0.5rem;">
                            <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?>
                        </div>
                        <div style="margin-bottom: 0.5rem;">
                            <strong>Platform Version:</strong> 1.0.0
                        </div>
                        <div style="margin-bottom: 0.5rem;">
                            <strong>Database:</strong> MySQL
                        </div>
                        <div>
                            <strong>Debug Mode:</strong> 
                            <span style="color: <?php echo DEBUG_MODE ? '#dc3545' : '#28a745'; ?>;">
                                <?php echo DEBUG_MODE ? 'Enabled' : 'Disabled'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- JavaScript -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
