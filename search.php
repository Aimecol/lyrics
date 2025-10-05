<?php
/**
 * Search Page - Song Lyrics Platform
 * Search songs by title, artist, or lyrics content
 */

// Include header
include 'includes/header.php';

// Initialize models
$songModel = new Song($db);
$artistModel = new Artist($db);

// Get search parameters
$query = sanitize($_GET['q'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));

$songs = [];
$artists = [];
$totalSongs = 0;
$totalPages = 0;

if ($query) {
    // Search songs
    $songs = $songModel->searchSongs($query, $page, SONGS_PER_PAGE);
    $totalSongs = $songModel->getSearchCount($query);
    $totalPages = ceil($totalSongs / SONGS_PER_PAGE);
    
    // Search artists (limited results)
    $artists = $artistModel->searchArtists($query, 5);
}

$page_title = $query ? "Search Results for \"$query\"" : 'Search';
$page_description = $query ? "Search results for \"$query\" - Find song lyrics and artists" : 'Search for song lyrics, artists, and albums';
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <h1 style="margin-bottom: 0.5rem;">
        <i class="fas fa-search"></i> 
        <?php echo $query ? 'Search Results' : 'Search'; ?>
    </h1>
    
    <?php if ($query): ?>
        <p style="color: #6c757d; margin: 0;">
            Results for "<strong><?php echo sanitize($query); ?></strong>"
            <?php if ($totalSongs > 0): ?>
                - Found <?php echo number_format($totalSongs); ?> songs
                <?php if ($totalPages > 1): ?>
                    (Page <?php echo $page; ?> of <?php echo $totalPages; ?>)
                <?php endif; ?>
            <?php endif; ?>
        </p>
    <?php else: ?>
        <p style="color: #6c757d; margin: 0;">
            Search for songs, artists, or lyrics content
        </p>
    <?php endif; ?>
</div>

<!-- Enhanced Search Form -->
<div class="search-section" style="background: #fff; border: 1px solid #e9ecef; border-radius: 6px; padding: 2rem; margin-bottom: 2rem;">
    <form method="GET" action="" class="search-form-enhanced">
        <div style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 300px; margin-bottom: 0;">
                <label class="form-label">Search Query:</label>
                <input type="text" 
                       name="q" 
                       class="form-control" 
                       placeholder="Enter song title, artist name, or lyrics..." 
                       value="<?php echo sanitize($query); ?>"
                       style="font-size: 1rem; padding: 0.75rem;">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
            
            <?php if ($query): ?>
                <div class="form-group" style="margin-bottom: 0;">
                    <a href="<?php echo SITE_URL; ?>/search" class="btn btn-secondary" style="padding: 0.75rem 1rem;">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </form>
    
    <?php if (!$query): ?>
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e9ecef;">
            <h4 style="margin-bottom: 1rem; color: #495057;">Search Tips:</h4>
            <ul style="color: #6c757d; font-size: 0.9rem; line-height: 1.6;">
                <li>Search by song title: "Bohemian Rhapsody"</li>
                <li>Search by artist name: "Queen"</li>
                <li>Search by lyrics content: "Is this the real life"</li>
                <li>Use quotes for exact phrases</li>
                <li>Search is case-insensitive</li>
            </ul>
        </div>
    <?php endif; ?>
</div>

<?php if ($query): ?>
    
    <!-- Artists Results (if any) -->
    <?php if (!empty($artists)): ?>
        <section class="artists-results" style="margin-bottom: 2rem;">
            <h2 style="margin-bottom: 1rem; color: #333;">
                <i class="fas fa-microphone" style="color: #6f42c1;"></i> Artists
            </h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <?php foreach ($artists as $artist): ?>
                    <div class="artist-card card" style="text-align: center;">
                        <h4 style="margin-bottom: 0.5rem;">
                            <a href="<?php echo SITE_URL; ?>/artist/<?php echo $artist['id']; ?>/<?php echo createSlug($artist['name']); ?>">
                                <?php echo sanitize($artist['name']); ?>
                            </a>
                        </h4>
                        <p style="color: #6c757d; font-size: 0.85rem; margin: 0;">
                            <?php echo $artist['song_count']; ?> songs
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
    
    <!-- Songs Results -->
    <?php if (!empty($songs)): ?>
        <section class="songs-results">
            <h2 style="margin-bottom: 1rem; color: #333;">
                <i class="fas fa-music"></i> Songs
            </h2>
            
            <div class="song-list">
                <?php foreach ($songs as $song): ?>
                    <div class="song-item">
                        <div class="song-info">
                            <h3 class="song-title">
                                <a href="<?php echo SITE_URL; ?>/song/<?php echo $song['id']; ?>/<?php echo createSlug($song['title']); ?>">
                                    <?php 
                                    // Highlight search term in title
                                    $highlightedTitle = str_ireplace($query, '<mark style="background: #fff3cd; padding: 0.1rem 0.2rem;">' . $query . '</mark>', sanitize($song['title']));
                                    echo $highlightedTitle;
                                    ?>
                                </a>
                                <?php if ($song['is_featured']): ?>
                                    <span class="tag" style="background: #ffc107; color: #212529; margin-left: 0.5rem;">
                                        <i class="fas fa-star"></i> Featured
                                    </span>
                                <?php endif; ?>
                            </h3>
                            
                            <p class="song-artist">
                                by <a href="<?php echo SITE_URL; ?>/artist/<?php echo $song['artist_id']; ?>/<?php echo createSlug($song['artist_name']); ?>">
                                    <?php 
                                    // Highlight search term in artist name
                                    $highlightedArtist = str_ireplace($query, '<mark style="background: #fff3cd; padding: 0.1rem 0.2rem;">' . $query . '</mark>', sanitize($song['artist_name']));
                                    echo $highlightedArtist;
                                    ?>
                                </a>
                            </p>
                            
                            <div class="song-meta">
                                <?php if ($song['album_title']): ?>
                                    <span><i class="fas fa-compact-disc"></i> <?php echo sanitize($song['album_title']); ?></span>
                                <?php endif; ?>
                                <?php if ($song['genre_name']): ?>
                                    <span><i class="fas fa-tag"></i> <?php echo sanitize($song['genre_name']); ?></span>
                                <?php endif; ?>
                                <span><i class="fas fa-eye"></i> <?php echo number_format($song['view_count']); ?> views</span>
                                <span><i class="fas fa-clock"></i> <?php echo timeAgo($song['created_at']); ?></span>
                            </div>
                            
                            <!-- Lyrics Preview with Highlighting -->
                            <div class="lyrics-preview" style="margin-top: 0.5rem; padding: 0.75rem; background: #f8f9fa; border-radius: 4px; font-size: 0.8rem; color: #6c757d; line-height: 1.4;">
                                <?php 
                                $lyricsText = strip_tags($song['lyrics']);
                                
                                // Find the position of the search term in lyrics
                                $pos = stripos($lyricsText, $query);
                                if ($pos !== false) {
                                    // Show context around the found term
                                    $start = max(0, $pos - 75);
                                    $length = 150;
                                    $excerpt = substr($lyricsText, $start, $length);
                                    
                                    // Add ellipsis if needed
                                    if ($start > 0) $excerpt = '...' . $excerpt;
                                    if (strlen($lyricsText) > $start + $length) $excerpt .= '...';
                                    
                                    // Highlight the search term
                                    $highlightedExcerpt = str_ireplace($query, '<mark style="background: #fff3cd; padding: 0.1rem 0.2rem;">' . $query . '</mark>', $excerpt);
                                    echo $highlightedExcerpt;
                                } else {
                                    // Show regular preview if search term not found in lyrics
                                    echo truncateText($lyricsText, 150);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination-section">
                    <?php echo generatePagination($page, $totalPages, $_SERVER['PHP_SELF'], ['q' => $query]); ?>
                </div>
            <?php endif; ?>
        </section>
        
    <?php elseif (empty($artists)): ?>
        <!-- No Results Found -->
        <div class="no-results" style="text-align: center; padding: 3rem; background: #fff; border: 1px solid #e9ecef; border-radius: 6px;">
            <i class="fas fa-search" style="font-size: 3rem; color: #6c757d; margin-bottom: 1rem;"></i>
            <h3 style="color: #6c757d; margin-bottom: 1rem;">No Results Found</h3>
            <p style="color: #6c757d; margin-bottom: 2rem;">
                We couldn't find any songs or artists matching "<strong><?php echo sanitize($query); ?></strong>".
            </p>
            
            <div style="margin-bottom: 2rem;">
                <h4 style="color: #495057; margin-bottom: 1rem;">Try these suggestions:</h4>
                <ul style="color: #6c757d; text-align: left; display: inline-block; font-size: 0.9rem;">
                    <li>Check your spelling</li>
                    <li>Try different keywords</li>
                    <li>Use more general terms</li>
                    <li>Try searching for the artist name instead</li>
                </ul>
            </div>
            
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
    
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
