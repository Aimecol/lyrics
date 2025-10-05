<?php
/**
 * Homepage - Song Lyrics Platform
 * Displays featured songs, popular songs, and recent additions
 */

$page_title = 'Home';
$page_description = 'Discover and read song lyrics from your favorite artists. Browse our extensive collection of songs, albums, and artists.';

// Include header
include 'includes/header.php';

// Initialize models
$songModel = new Song($db);
$artistModel = new Artist($db);

// Get featured songs
$featuredSongs = $songModel->getFeaturedSongs(6);

// Get popular songs
$popularSongs = $songModel->getPopularSongs(8);

// Get recent songs
$recentSongs = $songModel->getRecentSongs(8);

// Get popular artists
$popularArtists = $artistModel->getPopularArtists(6);
?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 3rem 0; margin-bottom: 2rem; border-radius: 8px;">
    <div style="text-align: center; max-width: 600px; margin: 0 auto;">
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem; font-weight: 600;">
            <i class="fas fa-music"></i> Welcome to <?php echo SITE_NAME; ?>
        </h1>
        <p style="font-size: 1.1rem; margin-bottom: 2rem; opacity: 0.9;">
            Discover and read lyrics from thousands of songs. Search by title, artist, or browse our curated collections.
        </p>
        <div style="max-width: 400px; margin: 0 auto;">
            <form class="search-form" action="<?php echo SITE_URL; ?>/search" method="GET" style="border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                <input type="text" 
                       name="q" 
                       class="search-input" 
                       placeholder="Search for songs or artists..." 
                       style="font-size: 1rem; padding: 0.75rem 1rem;">
                <button type="submit" class="search-btn" style="padding: 0.75rem 1rem;">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
</section>

<?php if (!empty($featuredSongs)): ?>
<!-- Featured Songs Section -->
<section class="featured-section" style="margin-bottom: 3rem;">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 style="color: #333; font-weight: 600;">
            <i class="fas fa-star" style="color: #ffc107;"></i> Featured Songs
        </h2>
        <a href="<?php echo SITE_URL; ?>/featured" class="btn btn-primary btn-sm">View All</a>
    </div>
    
    <div class="songs-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
        <?php foreach ($featuredSongs as $song): ?>
            <div class="song-card card">
                <div class="card-header">
                    <h3 class="card-title">
                        <a href="<?php echo SITE_URL; ?>/song/<?php echo $song['id']; ?>/<?php echo createSlug($song['title']); ?>">
                            <?php echo sanitize($song['title']); ?>
                        </a>
                    </h3>
                    <p class="card-subtitle">
                        <a href="<?php echo SITE_URL; ?>/artist/<?php echo $song['artist_id']; ?>/<?php echo createSlug($song['artist_name']); ?>">
                            <?php echo sanitize($song['artist_name']); ?>
                        </a>
                    </p>
                </div>
                
                <div class="card-content">
                    <p style="color: #6c757d; font-size: 0.85rem; line-height: 1.4;">
                        <?php echo truncateText(strip_tags($song['lyrics']), 120); ?>
                    </p>
                </div>
                
                <div class="card-meta">
                    <?php if ($song['album_title']): ?>
                        <span><i class="fas fa-compact-disc"></i> <?php echo sanitize($song['album_title']); ?></span>
                    <?php endif; ?>
                    <?php if ($song['genre_name']): ?>
                        <span><i class="fas fa-tag"></i> <?php echo sanitize($song['genre_name']); ?></span>
                    <?php endif; ?>
                    <span><i class="fas fa-eye"></i> <?php echo number_format($song['view_count']); ?> views</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Popular Songs Section -->
<section class="popular-section" style="margin-bottom: 3rem;">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 style="color: #333; font-weight: 600;">
            <i class="fas fa-fire" style="color: #dc3545;"></i> Popular Songs
        </h2>
        <a href="<?php echo SITE_URL; ?>/popular" class="btn btn-primary btn-sm">View All</a>
    </div>
    
    <div class="song-list">
        <?php foreach ($popularSongs as $index => $song): ?>
            <div class="song-item">
                <div class="song-rank" style="font-weight: 600; color: #007bff; margin-right: 1rem; font-size: 1.1rem;">
                    #<?php echo $index + 1; ?>
                </div>
                <div class="song-info">
                    <h3 class="song-title">
                        <a href="<?php echo SITE_URL; ?>/song/<?php echo $song['id']; ?>/<?php echo createSlug($song['title']); ?>">
                            <?php echo sanitize($song['title']); ?>
                        </a>
                    </h3>
                    <p class="song-artist">
                        by <a href="<?php echo SITE_URL; ?>/artist/<?php echo $song['artist_id']; ?>/<?php echo createSlug($song['artist_name']); ?>">
                            <?php echo sanitize($song['artist_name']); ?>
                        </a>
                    </p>
                    <div class="song-meta">
                        <?php if ($song['album_title']): ?>
                            <span><i class="fas fa-compact-disc"></i> <?php echo sanitize($song['album_title']); ?></span>
                        <?php endif; ?>
                        <span><i class="fas fa-eye"></i> <?php echo number_format($song['view_count']); ?> views</span>
                        <span><i class="fas fa-clock"></i> <?php echo timeAgo($song['created_at']); ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Two Column Layout for Recent Songs and Popular Artists -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
    
    <!-- Recent Songs -->
    <section class="recent-section">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="color: #333; font-weight: 600;">
                <i class="fas fa-clock" style="color: #28a745;"></i> Recent Additions
            </h2>
            <a href="<?php echo SITE_URL; ?>/recent" class="btn btn-primary btn-sm">View All</a>
        </div>
        
        <div class="song-list">
            <?php foreach (array_slice($recentSongs, 0, 5) as $song): ?>
                <div class="song-item" style="padding: 0.5rem; margin-bottom: 0.5rem;">
                    <div class="song-info">
                        <h4 class="song-title" style="font-size: 0.9rem; margin-bottom: 0.25rem;">
                            <a href="<?php echo SITE_URL; ?>/song/<?php echo $song['id']; ?>/<?php echo createSlug($song['title']); ?>">
                                <?php echo sanitize($song['title']); ?>
                            </a>
                        </h4>
                        <p class="song-artist" style="font-size: 0.8rem; margin-bottom: 0.25rem;">
                            by <a href="<?php echo SITE_URL; ?>/artist/<?php echo $song['artist_id']; ?>/<?php echo createSlug($song['artist_name']); ?>">
                                <?php echo sanitize($song['artist_name']); ?>
                            </a>
                        </p>
                        <div class="song-meta" style="font-size: 0.7rem;">
                            <span><i class="fas fa-clock"></i> <?php echo timeAgo($song['created_at']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- Popular Artists -->
    <section class="artists-section">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="color: #333; font-weight: 600;">
                <i class="fas fa-microphone" style="color: #6f42c1;"></i> Popular Artists
            </h2>
            <a href="<?php echo SITE_URL; ?>/browse?type=artists" class="btn btn-primary btn-sm">View All</a>
        </div>
        
        <div class="artists-list">
            <?php foreach ($popularArtists as $artist): ?>
                <div class="artist-item" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #e9ecef; border-radius: 4px; background: #fff;">
                    <div>
                        <h4 style="font-size: 0.9rem; margin-bottom: 0.25rem;">
                            <a href="<?php echo SITE_URL; ?>/artist/<?php echo $artist['id']; ?>/<?php echo createSlug($artist['name']); ?>">
                                <?php echo sanitize($artist['name']); ?>
                            </a>
                        </h4>
                        <p style="font-size: 0.75rem; color: #6c757d; margin: 0;">
                            <?php echo $artist['song_count']; ?> songs
                        </p>
                    </div>
                    <div style="font-size: 0.7rem; color: #6c757d;">
                        <?php echo number_format($artist['total_views'] ?? 0); ?> views
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<!-- Quick Stats -->
<section class="stats-section" style="background: #f8f9fa; padding: 2rem; border-radius: 8px; text-align: center;">
    <h2 style="margin-bottom: 1.5rem; color: #333;">Platform Statistics</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
        <?php
        // Get quick stats
        $totalSongs = $songModel->getTotalSongsCount();
        $totalArtists = $artistModel->getTotalArtistsCount();
        ?>
        <div class="stat-item">
            <div style="font-size: 2rem; font-weight: 600; color: #007bff; margin-bottom: 0.5rem;">
                <?php echo number_format($totalSongs); ?>
            </div>
            <div style="color: #6c757d; font-size: 0.9rem;">Songs</div>
        </div>
        <div class="stat-item">
            <div style="font-size: 2rem; font-weight: 600; color: #28a745; margin-bottom: 0.5rem;">
                <?php echo number_format($totalArtists); ?>
            </div>
            <div style="color: #6c757d; font-size: 0.9rem;">Artists</div>
        </div>
        <div class="stat-item">
            <div style="font-size: 2rem; font-weight: 600; color: #dc3545; margin-bottom: 0.5rem;">
                <i class="fas fa-heart"></i>
            </div>
            <div style="color: #6c757d; font-size: 0.9rem;">Made with Love</div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
