<?php
/**
 * Song Details Page - Song Lyrics Platform
 * Display individual song with full lyrics and metadata
 */

// Include header
include 'includes/header.php';

// Initialize models
$songModel = new Song($db);

// Get song ID from URL
$songId = intval($_GET['id'] ?? 0);

if (!$songId) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Get song details
$song = $songModel->getSongById($songId);

if (!$song) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Increment view count and record view
$songModel->incrementViewCount($songId);
$songModel->recordView($songId, $_SERVER['REMOTE_ADDR'] ?? null, $_SERVER['HTTP_USER_AGENT'] ?? null);

// Get song tags
$tags = $songModel->getSongTags($songId);

// Set page metadata
$page_title = $song['title'] . ' by ' . $song['artist_name'];
$page_description = 'Read the lyrics for "' . $song['title'] . '" by ' . $song['artist_name'] . 
                   ($song['album_title'] ? ' from the album "' . $song['album_title'] . '"' : '') . 
                   '. ' . truncateText(strip_tags($song['lyrics']), 150);

// Get related songs by same artist
$relatedSongs = $songModel->getSongsByArtist($song['artist_id'], 1, 5);
// Remove current song from related songs
$relatedSongs = array_filter($relatedSongs, function($relatedSong) use ($songId) {
    return $relatedSong['id'] != $songId;
});
$relatedSongs = array_slice($relatedSongs, 0, 4);
?>

<!-- Structured Data for SEO -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "MusicRecording",
    "name": "<?php echo sanitize($song['title']); ?>",
    "byArtist": {
        "@type": "MusicGroup",
        "name": "<?php echo sanitize($song['artist_name']); ?>"
    },
    <?php if ($song['album_title']): ?>
    "inAlbum": {
        "@type": "MusicAlbum",
        "name": "<?php echo sanitize($song['album_title']); ?>"
    },
    <?php endif; ?>
    <?php if ($song['genre_name']): ?>
    "genre": "<?php echo sanitize($song['genre_name']); ?>",
    <?php endif; ?>
    <?php if ($song['release_year']): ?>
    "datePublished": "<?php echo $song['release_year']; ?>",
    <?php endif; ?>
    "url": "<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>"
}
</script>

<!-- Song Header -->
<div class="song-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem 0; margin: -1.5rem -1rem 2rem -1rem; border-radius: 8px;">
    <div class="container">
        <div style="text-align: center; max-width: 800px; margin: 0 auto;">
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem; font-weight: 600;">
                <?php echo sanitize($song['title']); ?>
            </h1>
            
            <p style="font-size: 1.3rem; margin-bottom: 1rem; opacity: 0.9;">
                by <a href="<?php echo SITE_URL; ?>/artist/<?php echo $song['artist_id']; ?>/<?php echo createSlug($song['artist_name']); ?>" 
                      style="color: white; text-decoration: underline;">
                    <?php echo sanitize($song['artist_name']); ?>
                </a>
            </p>
            
            <div class="song-meta-header" style="display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap; font-size: 0.9rem; opacity: 0.8;">
                <?php if ($song['album_title']): ?>
                    <span>
                        <i class="fas fa-compact-disc"></i> 
                        <a href="<?php echo SITE_URL; ?>/album/<?php echo $song['album_id']; ?>/<?php echo createSlug($song['album_title']); ?>" 
                           style="color: white;">
                            <?php echo sanitize($song['album_title']); ?>
                        </a>
                    </span>
                <?php endif; ?>
                
                <?php if ($song['genre_name']): ?>
                    <span>
                        <i class="fas fa-tag"></i> 
                        <a href="<?php echo SITE_URL; ?>/genre/<?php echo $song['genre_id']; ?>/<?php echo createSlug($song['genre_name']); ?>" 
                           style="color: white;">
                            <?php echo sanitize($song['genre_name']); ?>
                        </a>
                    </span>
                <?php endif; ?>
                
                <?php if ($song['release_year']): ?>
                    <span><i class="fas fa-calendar"></i> <?php echo $song['release_year']; ?></span>
                <?php endif; ?>
                
                <span><i class="fas fa-eye"></i> <?php echo number_format($song['view_count']); ?> views</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Layout -->
<div style="display: grid; grid-template-columns: 1fr 300px; gap: 2rem; align-items: start;">
    
    <!-- Lyrics Section -->
    <div class="lyrics-section">
        <!-- Action Buttons -->
        <div class="song-actions" style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <button class="btn btn-primary copy-lyrics" title="Copy lyrics to clipboard">
                <i class="fas fa-copy"></i> Copy Lyrics
            </button>
            <button class="btn btn-secondary" onclick="window.print()" title="Print lyrics">
                <i class="fas fa-print"></i> Print
            </button>
            <button class="btn btn-secondary" onclick="toggleFontSize()" title="Increase font size">
                <i class="fas fa-text-height"></i> Font Size
            </button>
        </div>
        
        <!-- Lyrics Display -->
        <div class="lyrics-container" id="lyrics-container">
            <?php echo formatLyrics($song['lyrics']); ?>
        </div>
        
        <!-- Tags -->
        <?php if (!empty($tags)): ?>
            <div class="song-tags" style="margin-top: 1.5rem;">
                <h4 style="margin-bottom: 0.75rem; color: #495057;">Tags:</h4>
                <div class="tags">
                    <?php foreach ($tags as $tag): ?>
                        <a href="<?php echo SITE_URL; ?>/search?q=<?php echo urlencode($tag['name']); ?>" 
                           class="tag" 
                           style="background-color: <?php echo sanitize($tag['color']); ?>; color: white;">
                            <?php echo sanitize($tag['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Song Information -->
        <div class="song-info-detailed" style="margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 6px;">
            <h3 style="margin-bottom: 1rem; color: #333;">Song Information</h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div>
                    <strong>Artist:</strong><br>
                    <a href="<?php echo SITE_URL; ?>/artist/<?php echo $song['artist_id']; ?>/<?php echo createSlug($song['artist_name']); ?>">
                        <?php echo sanitize($song['artist_name']); ?>
                    </a>
                </div>
                
                <?php if ($song['album_title']): ?>
                    <div>
                        <strong>Album:</strong><br>
                        <a href="<?php echo SITE_URL; ?>/album/<?php echo $song['album_id']; ?>/<?php echo createSlug($song['album_title']); ?>">
                            <?php echo sanitize($song['album_title']); ?>
                        </a>
                    </div>
                <?php endif; ?>
                
                <?php if ($song['genre_name']): ?>
                    <div>
                        <strong>Genre:</strong><br>
                        <a href="<?php echo SITE_URL; ?>/genre/<?php echo $song['genre_id']; ?>/<?php echo createSlug($song['genre_name']); ?>">
                            <?php echo sanitize($song['genre_name']); ?>
                        </a>
                    </div>
                <?php endif; ?>
                
                <?php if ($song['release_year']): ?>
                    <div>
                        <strong>Release Year:</strong><br>
                        <?php echo $song['release_year']; ?>
                    </div>
                <?php endif; ?>
                
                <div>
                    <strong>Language:</strong><br>
                    <?php echo sanitize($song['language']); ?>
                </div>
                
                <div>
                    <strong>Views:</strong><br>
                    <?php echo number_format($song['view_count']); ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Related Songs -->
        <?php if (!empty($relatedSongs)): ?>
            <div class="related-songs card">
                <div class="card-header">
                    <h3 class="card-title">More by <?php echo sanitize($song['artist_name']); ?></h3>
                </div>
                
                <div class="related-songs-list">
                    <?php foreach ($relatedSongs as $relatedSong): ?>
                        <div class="related-song-item" style="padding: 0.75rem; border-bottom: 1px solid #e9ecef; last-child:border-bottom: none;">
                            <h4 style="font-size: 0.9rem; margin-bottom: 0.25rem;">
                                <a href="<?php echo SITE_URL; ?>/song/<?php echo $relatedSong['id']; ?>/<?php echo createSlug($relatedSong['title']); ?>">
                                    <?php echo sanitize($relatedSong['title']); ?>
                                </a>
                            </h4>
                            
                            <div style="font-size: 0.75rem; color: #6c757d;">
                                <?php if ($relatedSong['album_title']): ?>
                                    <span><i class="fas fa-compact-disc"></i> <?php echo sanitize($relatedSong['album_title']); ?></span><br>
                                <?php endif; ?>
                                <span><i class="fas fa-eye"></i> <?php echo number_format($relatedSong['view_count']); ?> views</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="padding: 0.75rem; text-align: center; border-top: 1px solid #e9ecef;">
                    <a href="<?php echo SITE_URL; ?>/artist/<?php echo $song['artist_id']; ?>/<?php echo createSlug($song['artist_name']); ?>" 
                       class="btn btn-sm btn-primary">
                        View All Songs by <?php echo sanitize($song['artist_name']); ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Quick Actions -->
        <div class="quick-actions card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            
            <div style="padding: 0;">
                <a href="<?php echo SITE_URL; ?>/search?q=<?php echo urlencode($song['artist_name']); ?>" 
                   class="btn btn-sm btn-secondary" 
                   style="width: 100%; margin-bottom: 0.5rem; text-align: left;">
                    <i class="fas fa-search"></i> More by this artist
                </a>
                
                <?php if ($song['genre_name']): ?>
                    <a href="<?php echo SITE_URL; ?>/genre/<?php echo $song['genre_id']; ?>/<?php echo createSlug($song['genre_name']); ?>" 
                       class="btn btn-sm btn-secondary" 
                       style="width: 100%; margin-bottom: 0.5rem; text-align: left;">
                        <i class="fas fa-tag"></i> More <?php echo sanitize($song['genre_name']); ?> songs
                    </a>
                <?php endif; ?>
                
                <a href="<?php echo SITE_URL; ?>/popular" 
                   class="btn btn-sm btn-secondary" 
                   style="width: 100%; margin-bottom: 0.5rem; text-align: left;">
                    <i class="fas fa-fire"></i> Popular songs
                </a>
                
                <a href="<?php echo SITE_URL; ?>/recent" 
                   class="btn btn-sm btn-secondary" 
                   style="width: 100%; text-align: left;">
                    <i class="fas fa-clock"></i> Recent additions
                </a>
            </div>
        </div>
        
        <!-- Share Section -->
        <div class="share-section card">
            <div class="card-header">
                <h3 class="card-title">Share This Song</h3>
            </div>
            
            <div style="display: flex; gap: 0.5rem; justify-content: center; padding: 1rem;">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>" 
                   target="_blank" 
                   class="btn btn-sm" 
                   style="background: #3b5998; color: white;"
                   title="Share on Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                
                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode('Check out "' . $song['title'] . '" by ' . $song['artist_name']); ?>" 
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

<!-- JavaScript for enhanced functionality -->
<script>
let currentFontSize = 1;

function toggleFontSize() {
    const lyricsContainer = document.getElementById('lyrics-container');
    currentFontSize = currentFontSize >= 1.5 ? 1 : currentFontSize + 0.25;
    lyricsContainer.style.fontSize = currentFontSize + 'rem';
}

function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Link copied to clipboard!');
        });
    } else {
        // Fallback for older browsers
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
    // This function is defined in main.js
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
    
    .song-header {
        margin: -1rem -0.75rem 1rem -0.75rem !important;
        padding: 1.5rem 0 !important;
    }
    
    .song-header h1 {
        font-size: 1.8rem !important;
    }
    
    .song-header p {
        font-size: 1.1rem !important;
    }
    
    .song-meta-header {
        flex-direction: column !important;
        gap: 0.5rem !important;
    }
    
    .song-actions {
        justify-content: center;
    }
    
    .song-info-detailed > div {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
