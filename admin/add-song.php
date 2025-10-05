<?php
/**
 * Admin Add Song - Song Lyrics Platform
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

$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF token
    if (!verifyCSRFToken($csrf_token)) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        // Validate and sanitize input
        $title = sanitize($_POST['title'] ?? '');
        $artist_id = intval($_POST['artist_id'] ?? 0);
        $album_id = intval($_POST['album_id'] ?? 0) ?: null;
        $genre_id = intval($_POST['genre_id'] ?? 0) ?: null;
        $lyrics = trim($_POST['lyrics'] ?? '');
        $release_year = intval($_POST['release_year'] ?? 0) ?: null;
        $language = sanitize($_POST['language'] ?? 'English');
        $is_featured = isset($_POST['is_featured']);
        $status = sanitize($_POST['status'] ?? 'published');
        
        // Validation
        if (empty($title)) {
            $errors[] = 'Song title is required.';
        }
        
        if (!$artist_id) {
            $errors[] = 'Please select an artist.';
        }
        
        if (empty($lyrics)) {
            $errors[] = 'Lyrics are required.';
        }
        
        if ($release_year && ($release_year < 1900 || $release_year > date('Y') + 1)) {
            $errors[] = 'Please enter a valid release year.';
        }
        
        // If no errors, add the song
        if (empty($errors)) {
            $songData = [
                'title' => $title,
                'artist_id' => $artist_id,
                'album_id' => $album_id,
                'genre_id' => $genre_id,
                'lyrics' => $lyrics,
                'release_year' => $release_year,
                'language' => $language,
                'is_featured' => $is_featured,
                'status' => $status
            ];
            
            try {
                $songId = $adminModel->addSong($songData);
                
                if ($songId) {
                    setFlashMessage('Song added successfully!', 'success');
                    logActivity('Song added', "Song: $title, ID: $songId");
                    redirect(SITE_URL . '/admin/songs.php');
                } else {
                    $errors[] = 'Error adding song. Please try again.';
                }
            } catch (Exception $e) {
                $errors[] = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// Get data for dropdowns
$artists = $adminModel->getAllArtists();
$albums = $adminModel->getAllAlbums();
$genres = $adminModel->getAllGenres();

$page_title = 'Add New Song';
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
                        <a href="<?php echo SITE_URL; ?>/admin/songs.php" class="admin-nav-link">
                            <i class="fas fa-music"></i> Manage Songs
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="<?php echo SITE_URL; ?>/admin/add-song.php" class="admin-nav-link active">
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
                    <h1 style="margin: 0; color: #333;">Add New Song</h1>
                    <p style="margin: 0; color: #6c757d; font-size: 0.9rem;">Add a new song to the platform</p>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="mobile-menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <a href="<?php echo SITE_URL; ?>/admin/songs.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Songs
                    </a>
                </div>
            </header>
            
            <!-- Flash Messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo sanitize($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Add Song Form -->
            <div class="admin-form">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <!-- Basic Information -->
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="title" class="form-label">Song Title *</label>
                                <input type="text" 
                                       id="title" 
                                       name="title" 
                                       class="form-control" 
                                       placeholder="Enter song title"
                                       value="<?php echo sanitize($_POST['title'] ?? ''); ?>"
                                       required>
                            </div>
                        </div>
                        
                        <div class="form-col">
                            <div class="form-group">
                                <label for="artist_id" class="form-label">Artist *</label>
                                <select id="artist_id" name="artist_id" class="form-control" required>
                                    <option value="">Select Artist</option>
                                    <?php foreach ($artists as $artist): ?>
                                        <option value="<?php echo $artist['id']; ?>" 
                                                <?php echo (intval($_POST['artist_id'] ?? 0) === $artist['id']) ? 'selected' : ''; ?>>
                                            <?php echo sanitize($artist['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="album_id" class="form-label">Album (Optional)</label>
                                <select id="album_id" name="album_id" class="form-control">
                                    <option value="">Select Album</option>
                                    <?php foreach ($albums as $album): ?>
                                        <option value="<?php echo $album['id']; ?>" 
                                                <?php echo (intval($_POST['album_id'] ?? 0) === $album['id']) ? 'selected' : ''; ?>>
                                            <?php echo sanitize($album['title']) . ' - ' . sanitize($album['artist_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-col">
                            <div class="form-group">
                                <label for="genre_id" class="form-label">Genre (Optional)</label>
                                <select id="genre_id" name="genre_id" class="form-control">
                                    <option value="">Select Genre</option>
                                    <?php foreach ($genres as $genre): ?>
                                        <option value="<?php echo $genre['id']; ?>" 
                                                <?php echo (intval($_POST['genre_id'] ?? 0) === $genre['id']) ? 'selected' : ''; ?>>
                                            <?php echo sanitize($genre['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lyrics -->
                    <div class="form-group">
                        <label for="lyrics" class="form-label">Lyrics *</label>
                        <textarea id="lyrics" 
                                  name="lyrics" 
                                  class="form-control" 
                                  rows="15" 
                                  placeholder="Enter song lyrics here...&#10;&#10;Use [Verse 1], [Chorus], [Bridge] etc. to mark sections"
                                  required><?php echo sanitize($_POST['lyrics'] ?? ''); ?></textarea>
                        <small style="color: #6c757d; font-size: 0.8rem;">
                            Tip: Use section markers like [Verse 1], [Chorus], [Bridge] to structure the lyrics
                        </small>
                    </div>
                    
                    <!-- Additional Information -->
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="release_year" class="form-label">Release Year</label>
                                <input type="number" 
                                       id="release_year" 
                                       name="release_year" 
                                       class="form-control" 
                                       placeholder="e.g. 2023"
                                       min="1900" 
                                       max="<?php echo date('Y') + 1; ?>"
                                       value="<?php echo sanitize($_POST['release_year'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-col">
                            <div class="form-group">
                                <label for="language" class="form-label">Language</label>
                                <input type="text" 
                                       id="language" 
                                       name="language" 
                                       class="form-control" 
                                       placeholder="e.g. English"
                                       value="<?php echo sanitize($_POST['language'] ?? 'English'); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Options -->
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="published" <?php echo (($_POST['status'] ?? 'published') === 'published') ? 'selected' : ''; ?>>Published</option>
                                    <option value="draft" <?php echo (($_POST['status'] ?? '') === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                    <option value="archived" <?php echo (($_POST['status'] ?? '') === 'archived') ? 'selected' : ''; ?>>Archived</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">&nbsp;</label>
                                <div style="padding-top: 0.5rem;">
                                    <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: normal;">
                                        <input type="checkbox" 
                                               name="is_featured" 
                                               value="1"
                                               <?php echo isset($_POST['is_featured']) ? 'checked' : ''; ?>>
                                        <i class="fas fa-star" style="color: #ffc107;"></i> Featured Song
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Add Song
                        </button>
                        
                        <a href="<?php echo SITE_URL; ?>/admin/songs.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <!-- JavaScript -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
