<?php
/**
 * Admin Songs Management - Song Lyrics Platform
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

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $songId = intval($_GET['id']);
    $csrf_token = $_GET['csrf_token'] ?? '';
    
    if (verifyCSRFToken($csrf_token)) {
        if ($adminModel->deleteSong($songId)) {
            setFlashMessage('Song deleted successfully.', 'success');
            logActivity('Song deleted', "Song ID: $songId");
        } else {
            setFlashMessage('Error deleting song.', 'error');
        }
    } else {
        setFlashMessage('Invalid security token.', 'error');
    }
    
    redirect(SITE_URL . '/admin/songs.php');
}

// Get parameters
$page = max(1, intval($_GET['page'] ?? 1));
$status = sanitize($_GET['status'] ?? '');

// Get songs
$songs = $adminModel->getAllSongs($page, ADMIN_ITEMS_PER_PAGE, $status ?: null);
$totalSongs = $adminModel->getTotalSongsCount($status ?: null);
$totalPages = ceil($totalSongs / ADMIN_ITEMS_PER_PAGE);

$page_title = 'Manage Songs';
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
                        <a href="<?php echo SITE_URL; ?>/admin" class="admin-nav-link">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="<?php echo SITE_URL; ?>/admin/songs.php" class="admin-nav-link active">
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
                    <h1 style="margin: 0; color: #333;">Manage Songs</h1>
                    <p style="margin: 0; color: #6c757d; font-size: 0.9rem;">
                        Total: <?php echo number_format($totalSongs); ?> songs
                        <?php if ($totalPages > 1): ?>
                            (Page <?php echo $page; ?> of <?php echo $totalPages; ?>)
                        <?php endif; ?>
                    </p>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="mobile-menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <a href="<?php echo SITE_URL; ?>/admin/add-song.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Song
                    </a>
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
            
            <!-- Filters -->
            <div class="admin-form" style="margin-bottom: 1.5rem;">
                <form method="GET" action="" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
                    <div class="form-group" style="margin-bottom: 0; min-width: 200px;">
                        <label class="form-label">Filter by Status:</label>
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="archived" <?php echo $status === 'archived' ? 'selected' : ''; ?>>Archived</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Apply Filter
                        </button>
                    </div>
                    
                    <?php if ($status): ?>
                        <div class="form-group" style="margin-bottom: 0;">
                            <a href="<?php echo SITE_URL; ?>/admin/songs.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear Filter
                            </a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- Songs Table -->
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>Song Title</th>
                            <th>Artist</th>
                            <th>Album</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($songs)): ?>
                            <?php foreach ($songs as $song): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo sanitize($song['title']); ?></strong>
                                        <?php if ($song['is_featured']): ?>
                                            <span class="status-badge" style="background: #ffc107; color: #212529; margin-left: 0.5rem;">
                                                <i class="fas fa-star"></i> Featured
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo sanitize($song['artist_name']); ?></td>
                                    <td><?php echo $song['album_title'] ? sanitize($song['album_title']) : '-'; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $song['status']; ?>">
                                            <?php echo ucfirst($song['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($song['view_count']); ?></td>
                                    <td><?php echo timeAgo($song['created_at']); ?></td>
                                    <td class="actions">
                                        <a href="<?php echo SITE_URL; ?>/song/<?php echo $song['id']; ?>" 
                                           class="btn btn-sm btn-primary" 
                                           target="_blank" 
                                           title="View Song">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/admin/edit-song.php?id=<?php echo $song['id']; ?>" 
                                           class="btn btn-sm btn-warning" 
                                           title="Edit Song">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/admin/songs.php?action=delete&id=<?php echo $song['id']; ?>&csrf_token=<?php echo generateCSRFToken(); ?>" 
                                           class="btn btn-sm btn-danger confirm-delete" 
                                           data-item-name="<?php echo sanitize($song['title']); ?>"
                                           title="Delete Song">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #6c757d; padding: 2rem;">
                                    <?php if ($status): ?>
                                        No songs found with status "<?php echo sanitize($status); ?>".
                                        <a href="<?php echo SITE_URL; ?>/admin/songs.php">View all songs</a>
                                    <?php else: ?>
                                        No songs found. <a href="<?php echo SITE_URL; ?>/admin/add-song.php">Add your first song</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination-section">
                    <?php
                    $queryParams = [];
                    if ($status) $queryParams['status'] = $status;
                    
                    echo generatePagination($page, $totalPages, $_SERVER['PHP_SELF'], $queryParams);
                    ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <!-- JavaScript -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
