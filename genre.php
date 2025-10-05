<?php
/**
 * Genre Page - Song Lyrics Platform
 * Display songs by genre
 */

// Include header
include 'includes/header.php';

// Initialize models
$songModel = new Song($db);

// Get genre ID from URL
$genreId = intval($_GET['id'] ?? 0);

if (!$genreId) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Get genre details
$genre = $db->fetchOne("SELECT * FROM genres WHERE id = ?", [$genreId]);

if (!$genre) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Get pagination
$page = max(1, intval($_GET['page'] ?? 1));

// Get songs by genre
$songs = $songModel->getSongsByGenre($genreId, $page, SONGS_PER_PAGE);

// Get total count for pagination
$totalSongs = $db->fetchOne("SELECT COUNT(*) as total FROM songs WHERE genre_id = ? AND status = 'published'", [$genreId])['total'] ?? 0;
$totalPages = ceil($totalSongs / SONGS_PER_PAGE);

// Set page metadata
$page_title = $genre['name'] . ' Songs';
$page_description = 'Browse ' . $genre['name'] . ' songs. Discover ' . $totalSongs . ' songs in the ' . $genre['name'] . ' genre.';
?>

<!-- Genre Header -->
<div class="genre-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem 0; margin: -1.5rem -1rem 2rem -1rem; border-radius: 8px;">
    <div class="container">
        <div style="text-align: center; max-width: 800px; margin: 0 auto;">
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem; font-weight: 600;">
                <i class="fas fa-tag"></i> <?php echo sanitize($genre['name']); ?>
            </h1>
            
            <?php if ($genre['description']): ?>
                <p style="font-size: 1.1rem; margin-bottom: 1rem; opacity: 0.9;">
                    <?php echo sanitize($genre['description']); ?>
                </p>
            <?php endif; ?>
            
            <div class="genre-stats" style="font-size: 1rem; opacity: 0.9;">
                <span><i class="fas fa-music"></i> <?php echo number_format($totalSongs); ?> Songs</span>
            </div>
        </div>
    </div>
</div>

<!-- Songs Section -->
<section class="songs-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 style="color: #333; margin: 0;">
            <i class="fas fa-music"></i> All <?php echo sanitize($genre['name']); ?> Songs
        </h2>
        
        <?php if ($totalPages > 1): ?>
            <p style="color: #6c757d; margin: 0; font-size: 0.9rem;">
                Page <?php echo $page; ?> of <?php echo $totalPages; ?>
            </p>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($songs)): ?>
        <div class="song-list">
            <?php foreach ($songs as $song): ?>
                <div class="song-item">
                    <div class="song-info">
                        <h3 class="song-title">
                            <a href="<?php echo SITE_URL; ?>/song/<?php echo $song['id']; ?>/<?php echo createSlug($song['title']); ?>">
                                <?php echo sanitize($song['title']); ?>
                            </a>
                            <?php if ($song['is_featured']): ?>
                                <span class="tag" style="background: #ffc107; color: #212529; margin-left: 0.5rem;">
                                    <i class="fas fa-star"></i> Featured
                                </span>
                            <?php endif; ?>
                        </h3>
                        
                        <p class="song-artist">
                            by <a href="<?php echo SITE_URL; ?>/artist/<?php echo $song['artist_id']; ?>/<?php echo createSlug($song['artist_name']); ?>">
                                <?php echo sanitize($song['artist_name']); ?>
                            </a>
                        </p>
                        
                        <div class="song-meta">
                            <?php if ($song['album_title']): ?>
                                <span>
                                    <i class="fas fa-compact-disc"></i> 
                                    <a href="<?php echo SITE_URL; ?>/album/<?php echo $song['album_id']; ?>/<?php echo createSlug($song['album_title']); ?>">
                                        <?php echo sanitize($song['album_title']); ?>
                                    </a>
                                </span>
                            <?php endif; ?>
                            <?php if ($song['release_year']): ?>
                                <span><i class="fas fa-calendar"></i> <?php echo $song['release_year']; ?></span>
                            <?php endif; ?>
                            <span><i class="fas fa-eye"></i> <?php echo number_format($song['view_count']); ?> views</span>
                            <span><i class="fas fa-clock"></i> <?php echo timeAgo($song['created_at']); ?></span>
                        </div>
                        
                        <!-- Lyrics Preview -->
                        <div class="lyrics-preview" style="margin-top: 0.5rem; padding: 0.75rem; background: #f8f9fa; border-radius: 4px; font-size: 0.8rem; color: #6c757d; line-height: 1.4;">
                            <?php echo truncateText(strip_tags($song['lyrics']), 150); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-section">
                <?php echo generatePagination($page, $totalPages, $_SERVER['PHP_SELF'], ['id' => $genreId]); ?>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- No Songs -->
        <div class="no-results" style="text-align: center; padding: 2rem; background: #fff; border: 1px solid #e9ecef; border-radius: 6px;">
            <i class="fas fa-music" style="font-size: 2rem; color: #6c757d; margin-bottom: 1rem;"></i>
            <h3 style="color: #6c757d; margin-bottom: 1rem;">No Songs Available</h3>
            <p style="color: #6c757d; margin-bottom: 2rem;">
                No songs are available in the <?php echo sanitize($genre['name']); ?> genre yet.
            </p>
            
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo SITE_URL; ?>/browse" class="btn btn-primary">
                    <i class="fas fa-list"></i> Browse All Songs
                </a>
                <a href="<?php echo SITE_URL; ?>/popular" class="btn btn-secondary">
                    <i class="fas fa-fire"></i> Popular Songs
                </a>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
