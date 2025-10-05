<?php
/**
 * Artist Page - Song Lyrics Platform
 * Display artist information and their songs
 */

// Include header
include 'includes/header.php';

// Initialize models
$songModel = new Song($db);
$artistModel = new Artist($db);

// Get artist ID from URL
$artistId = intval($_GET['id'] ?? 0);

if (!$artistId) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Get artist details
$artist = $artistModel->getArtistById($artistId);

if (!$artist) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Get pagination
$page = max(1, intval($_GET['page'] ?? 1));

// Get artist's songs
$songs = $songModel->getSongsByArtist($artistId, $page, SONGS_PER_PAGE);
$totalSongs = $artist['song_count'];
$totalPages = ceil($totalSongs / SONGS_PER_PAGE);

// Get artist's albums
$albums = $artistModel->getArtistAlbums($artistId);

// Set page metadata
$page_title = $artist['name'];
$page_description = 'View all songs by ' . $artist['name'] . '. Browse ' . $totalSongs . ' songs and albums.';
?>

<!-- Structured Data for SEO -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "MusicGroup",
    "name": "<?php echo sanitize($artist['name']); ?>",
    <?php if ($artist['country']): ?>
    "foundingLocation": "<?php echo sanitize($artist['country']); ?>",
    <?php endif; ?>
    "url": "<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>"
}
</script>

<!-- Artist Header -->
<div class="artist-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem 0; margin: -1.5rem -1rem 2rem -1rem; border-radius: 8px;">
    <div class="container">
        <div style="text-align: center; max-width: 800px; margin: 0 auto;">
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem; font-weight: 600;">
                <i class="fas fa-microphone"></i> <?php echo sanitize($artist['name']); ?>
            </h1>
            
            <div class="artist-stats" style="display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap; font-size: 1rem; opacity: 0.9;">
                <span><i class="fas fa-music"></i> <?php echo number_format($totalSongs); ?> Songs</span>
                <?php if (count($albums) > 0): ?>
                    <span><i class="fas fa-compact-disc"></i> <?php echo count($albums); ?> Albums</span>
                <?php endif; ?>
                <?php if ($artist['country']): ?>
                    <span><i class="fas fa-globe"></i> <?php echo sanitize($artist['country']); ?></span>
                <?php endif; ?>
            </div>
            
            <?php if ($artist['bio']): ?>
                <p style="margin-top: 1rem; font-size: 1rem; opacity: 0.9; max-width: 600px; margin-left: auto; margin-right: auto;">
                    <?php echo sanitize($artist['bio']); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Content Layout -->
<div style="display: grid; grid-template-columns: 1fr 300px; gap: 2rem; align-items: start;">
    
    <!-- Main Content -->
    <div class="artist-content">
        
        <!-- Albums Section -->
        <?php if (!empty($albums)): ?>
            <section class="albums-section" style="margin-bottom: 3rem;">
                <h2 style="margin-bottom: 1.5rem; color: #333;">
                    <i class="fas fa-compact-disc"></i> Albums
                </h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    <?php foreach ($albums as $album): ?>
                        <div class="album-card card">
                            <h3 class="card-title" style="font-size: 1rem;">
                                <a href="<?php echo SITE_URL; ?>/album/<?php echo $album['id']; ?>/<?php echo createSlug($album['title']); ?>">
                                    <?php echo sanitize($album['title']); ?>
                                </a>
                            </h3>
                            
                            <div class="card-meta">
                                <?php if ($album['release_year']): ?>
                                    <span><i class="fas fa-calendar"></i> <?php echo $album['release_year']; ?></span>
                                <?php endif; ?>
                                <span><i class="fas fa-music"></i> <?php echo $album['song_count']; ?> songs</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        
        <!-- Songs Section -->
        <section class="songs-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="color: #333; margin: 0;">
                    <i class="fas fa-music"></i> All Songs
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
                                
                                <div class="song-meta">
                                    <?php if ($song['album_title']): ?>
                                        <span>
                                            <i class="fas fa-compact-disc"></i> 
                                            <a href="<?php echo SITE_URL; ?>/album/<?php echo $song['album_id']; ?>/<?php echo createSlug($song['album_title']); ?>">
                                                <?php echo sanitize($song['album_title']); ?>
                                            </a>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($song['genre_name']): ?>
                                        <span>
                                            <i class="fas fa-tag"></i> 
                                            <a href="<?php echo SITE_URL; ?>/genre/<?php echo $song['genre_id']; ?>/<?php echo createSlug($song['genre_name']); ?>">
                                                <?php echo sanitize($song['genre_name']); ?>
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
                        <?php echo generatePagination($page, $totalPages, $_SERVER['PHP_SELF'], ['id' => $artistId]); ?>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <!-- No Songs -->
                <div class="no-results" style="text-align: center; padding: 2rem; background: #fff; border: 1px solid #e9ecef; border-radius: 6px;">
                    <i class="fas fa-music" style="font-size: 2rem; color: #6c757d; margin-bottom: 1rem;"></i>
                    <h3 style="color: #6c757d; margin-bottom: 1rem;">No Songs Available</h3>
                    <p style="color: #6c757d;">
                        This artist doesn't have any published songs yet.
                    </p>
                </div>
            <?php endif; ?>
        </section>
    </div>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Artist Info -->
        <div class="artist-info card">
            <div class="card-header">
                <h3 class="card-title">Artist Information</h3>
            </div>
            
            <div style="padding: 0;">
                <div style="padding: 0.75rem; border-bottom: 1px solid #e9ecef;">
                    <strong>Total Songs:</strong><br>
                    <?php echo number_format($totalSongs); ?>
                </div>
                
                <?php if (count($albums) > 0): ?>
                    <div style="padding: 0.75rem; border-bottom: 1px solid #e9ecef;">
                        <strong>Albums:</strong><br>
                        <?php echo count($albums); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($artist['country']): ?>
                    <div style="padding: 0.75rem; border-bottom: 1px solid #e9ecef;">
                        <strong>Country:</strong><br>
                        <?php echo sanitize($artist['country']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($artist['website']): ?>
                    <div style="padding: 0.75rem;">
                        <strong>Website:</strong><br>
                        <a href="<?php echo sanitize($artist['website']); ?>" target="_blank" rel="noopener">
                            Visit Official Site <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            
            <div style="padding: 0;">
                <a href="<?php echo SITE_URL; ?>/search?q=<?php echo urlencode($artist['name']); ?>" 
                   class="btn btn-sm btn-secondary" 
                   style="width: 100%; margin-bottom: 0.5rem; text-align: left;">
                    <i class="fas fa-search"></i> Search for this artist
                </a>
                
                <a href="<?php echo SITE_URL; ?>/popular" 
                   class="btn btn-sm btn-secondary" 
                   style="width: 100%; margin-bottom: 0.5rem; text-align: left;">
                    <i class="fas fa-fire"></i> Popular songs
                </a>
                
                <a href="<?php echo SITE_URL; ?>/browse" 
                   class="btn btn-sm btn-secondary" 
                   style="width: 100%; text-align: left;">
                    <i class="fas fa-list"></i> Browse all artists
                </a>
            </div>
        </div>
        
        <!-- Share Section -->
        <div class="share-section card">
            <div class="card-header">
                <h3 class="card-title">Share This Artist</h3>
            </div>
            
            <div style="display: flex; gap: 0.5rem; justify-content: center; padding: 1rem;">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>" 
                   target="_blank" 
                   class="btn btn-sm" 
                   style="background: #3b5998; color: white;"
                   title="Share on Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                
                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode('Check out songs by ' . $artist['name']); ?>" 
                   target="_blank" 
                   class="btn btn-sm" 
                   style="background: #1da1f2; color: white;"
                   title="Share on Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                
                <button onclick="copyToClipboard('<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>')" 
                        class="btn btn-sm btn-secondary" 
                        title="Copy link">
                    <i class="fas fa-link"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Link copied to clipboard!');
        });
    } else {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showToast('Link copied to clipboard!');
    }
}

function showToast(message) {
    if (typeof window.showToast === 'function') {
        window.showToast(message);
    } else {
        alert(message);
    }
}
</script>

<!-- Responsive adjustments -->
<style>
@media (max-width: 768px) {
    .main .container > div {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    
    .artist-header {
        margin: -1rem -0.75rem 1rem -0.75rem !important;
        padding: 1.5rem 0 !important;
    }
    
    .artist-header h1 {
        font-size: 1.8rem !important;
    }
    
    .artist-stats {
        flex-direction: column !important;
        gap: 0.5rem !important;
    }
    
    .albums-section > div {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
