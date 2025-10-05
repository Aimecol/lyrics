<?php
/**
 * Browse Page - Song Lyrics Platform
 * Browse songs with filtering and pagination
 */

$page_title = 'Browse Songs';
$page_description = 'Browse our complete collection of song lyrics. Filter by genre, artist, or browse all songs.';

// Include header
include 'includes/header.php';

// Initialize models
$songModel = new Song($db);

// Get parameters
$type = sanitize($_GET['type'] ?? 'all');
$page = max(1, intval($_GET['page'] ?? 1));
$genre = sanitize($_GET['genre'] ?? '');
$artist = sanitize($_GET['artist'] ?? '');

// Determine page title and description based on type
switch ($type) {
    case 'popular':
        $page_title = 'Popular Songs';
        $orderBy = 'view_count DESC, created_at DESC';
        break;
    case 'recent':
        $page_title = 'Recent Songs';
        $orderBy = 'created_at DESC';
        break;
    case 'featured':
        $page_title = 'Featured Songs';
        $orderBy = 'created_at DESC';
        break;
    default:
        $page_title = 'Browse All Songs';
        $orderBy = 'created_at DESC';
        break;
}

// Get songs based on type
if ($type === 'featured') {
    $songs = $songModel->getFeaturedSongs(SONGS_PER_PAGE * 5); // Get more for pagination
    $totalSongs = count($songs);
    // Paginate manually for featured songs
    $offset = ($page - 1) * SONGS_PER_PAGE;
    $songs = array_slice($songs, $offset, SONGS_PER_PAGE);
} else {
    $songs = $songModel->getAllSongs($page, SONGS_PER_PAGE, $orderBy);
    $totalSongs = $songModel->getTotalSongsCount();
}

$totalPages = ceil($totalSongs / SONGS_PER_PAGE);

// Get all genres for filter
$genres = $db->fetchAll("SELECT id, name FROM genres ORDER BY name ASC");
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="margin-bottom: 0.5rem;">
                <?php
                switch ($type) {
                    case 'popular':
                        echo '<i class="fas fa-fire" style="color: #dc3545;"></i> Popular Songs';
                        break;
                    case 'recent':
                        echo '<i class="fas fa-clock" style="color: #28a745;"></i> Recent Songs';
                        break;
                    case 'featured':
                        echo '<i class="fas fa-star" style="color: #ffc107;"></i> Featured Songs';
                        break;
                    default:
                        echo '<i class="fas fa-list"></i> Browse All Songs';
                        break;
                }
                ?>
            </h1>
            <p style="color: #6c757d; margin: 0;">
                Showing <?php echo number_format($totalSongs); ?> songs
                <?php if ($totalPages > 1): ?>
                    (Page <?php echo $page; ?> of <?php echo $totalPages; ?>)
                <?php endif; ?>
            </p>
        </div>
        
        <!-- View Type Toggles -->
        <div class="view-toggles" style="display: flex; gap: 0.5rem;">
            <a href="<?php echo SITE_URL; ?>/browse" 
               class="btn btn-sm <?php echo $type === 'all' ? 'btn-primary' : 'btn-secondary'; ?>">
                All Songs
            </a>
            <a href="<?php echo SITE_URL; ?>/popular" 
               class="btn btn-sm <?php echo $type === 'popular' ? 'btn-primary' : 'btn-secondary'; ?>">
                Popular
            </a>
            <a href="<?php echo SITE_URL; ?>/recent" 
               class="btn btn-sm <?php echo $type === 'recent' ? 'btn-primary' : 'btn-secondary'; ?>">
                Recent
            </a>
            <a href="<?php echo SITE_URL; ?>/featured" 
               class="btn btn-sm <?php echo $type === 'featured' ? 'btn-primary' : 'btn-secondary'; ?>">
                Featured
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filters-section" style="background: #fff; border: 1px solid #e9ecef; border-radius: 6px; padding: 1rem; margin-bottom: 2rem;">
    <form method="GET" action="" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
        <input type="hidden" name="type" value="<?php echo $type; ?>">
        
        <div class="form-group" style="margin-bottom: 0; min-width: 200px;">
            <label class="form-label">Filter by Genre:</label>
            <select name="genre" class="form-control">
                <option value="">All Genres</option>
                <?php foreach ($genres as $genreOption): ?>
                    <option value="<?php echo $genreOption['id']; ?>" 
                            <?php echo $genre == $genreOption['id'] ? 'selected' : ''; ?>>
                        <?php echo sanitize($genreOption['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
        </div>
        
        <?php if ($genre): ?>
            <div class="form-group" style="margin-bottom: 0;">
                <a href="?type=<?php echo $type; ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- Songs List -->
<?php if (!empty($songs)): ?>
    <div class="songs-section">
        <div class="song-list">
            <?php foreach ($songs as $index => $song): ?>
                <div class="song-item">
                    <?php if ($type === 'popular'): ?>
                        <div class="song-rank" style="font-weight: 600; color: #007bff; margin-right: 1rem; font-size: 1.1rem;">
                            #<?php echo (($page - 1) * SONGS_PER_PAGE) + $index + 1; ?>
                        </div>
                    <?php endif; ?>
                    
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
                                <span><i class="fas fa-compact-disc"></i> <?php echo sanitize($song['album_title']); ?></span>
                            <?php endif; ?>
                            <?php if ($song['genre_name']): ?>
                                <span><i class="fas fa-tag"></i> <?php echo sanitize($song['genre_name']); ?></span>
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
                <?php
                $queryParams = [];
                if ($type !== 'all') $queryParams['type'] = $type;
                if ($genre) $queryParams['genre'] = $genre;
                
                echo generatePagination($page, $totalPages, $_SERVER['PHP_SELF'], $queryParams);
                ?>
            </div>
        <?php endif; ?>
    </div>
    
<?php else: ?>
    <!-- No Songs Found -->
    <div class="no-results" style="text-align: center; padding: 3rem; background: #fff; border: 1px solid #e9ecef; border-radius: 6px;">
        <i class="fas fa-music" style="font-size: 3rem; color: #6c757d; margin-bottom: 1rem;"></i>
        <h3 style="color: #6c757d; margin-bottom: 1rem;">No Songs Found</h3>
        <p style="color: #6c757d; margin-bottom: 2rem;">
            <?php if ($genre): ?>
                No songs found for the selected genre. Try removing the filter or selecting a different genre.
            <?php else: ?>
                No songs are available at the moment. Please check back later.
            <?php endif; ?>
        </p>
        
        <?php if ($genre): ?>
            <a href="?type=<?php echo $type; ?>" class="btn btn-primary">
                <i class="fas fa-times"></i> Clear Filters
            </a>
        <?php else: ?>
            <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
                <i class="fas fa-home"></i> Go to Homepage
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
