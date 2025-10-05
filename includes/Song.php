<?php
/**
 * Song model class for handling song-related database operations
 */

class Song {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Get all published songs with pagination
     */
    public function getAllSongs($page = 1, $limit = SONGS_PER_PAGE, $orderBy = 'created_at DESC') {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT s.*, a.name as artist_name, al.title as album_title, g.name as genre_name 
                FROM songs s 
                JOIN artists a ON s.artist_id = a.id 
                LEFT JOIN albums al ON s.album_id = al.id 
                LEFT JOIN genres g ON s.genre_id = g.id 
                WHERE s.status = 'published' 
                ORDER BY s.$orderBy 
                LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$limit, $offset]);
    }
    
    /**
     * Get total count of published songs
     */
    public function getTotalSongsCount() {
        $sql = "SELECT COUNT(*) as total FROM songs WHERE status = 'published'";
        $result = $this->db->fetchOne($sql);
        return $result['total'] ?? 0;
    }
    
    /**
     * Get song by ID with full details
     */
    public function getSongById($id) {
        $sql = "SELECT s.*, a.name as artist_name, a.id as artist_id, 
                       al.title as album_title, al.id as album_id,
                       g.name as genre_name, g.id as genre_id
                FROM songs s 
                JOIN artists a ON s.artist_id = a.id 
                LEFT JOIN albums al ON s.album_id = al.id 
                LEFT JOIN genres g ON s.genre_id = g.id 
                WHERE s.id = ? AND s.status = 'published'";
        
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Get featured songs
     */
    public function getFeaturedSongs($limit = 10) {
        $sql = "SELECT s.*, a.name as artist_name, al.title as album_title, g.name as genre_name 
                FROM songs s 
                JOIN artists a ON s.artist_id = a.id 
                LEFT JOIN albums al ON s.album_id = al.id 
                LEFT JOIN genres g ON s.genre_id = g.id 
                WHERE s.status = 'published' AND s.is_featured = 1 
                ORDER BY s.created_at DESC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    /**
     * Get popular songs (by view count)
     */
    public function getPopularSongs($limit = 10) {
        $sql = "SELECT s.*, a.name as artist_name, al.title as album_title, g.name as genre_name 
                FROM songs s 
                JOIN artists a ON s.artist_id = a.id 
                LEFT JOIN albums al ON s.album_id = al.id 
                LEFT JOIN genres g ON s.genre_id = g.id 
                WHERE s.status = 'published' 
                ORDER BY s.view_count DESC, s.created_at DESC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    /**
     * Get recent songs
     */
    public function getRecentSongs($limit = 10) {
        $sql = "SELECT s.*, a.name as artist_name, al.title as album_title, g.name as genre_name 
                FROM songs s 
                JOIN artists a ON s.artist_id = a.id 
                LEFT JOIN albums al ON s.album_id = al.id 
                LEFT JOIN genres g ON s.genre_id = g.id 
                WHERE s.status = 'published' 
                ORDER BY s.created_at DESC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    /**
     * Search songs by title, artist, or lyrics
     */
    public function searchSongs($query, $page = 1, $limit = SONGS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        $searchTerm = '%' . $query . '%';
        
        $sql = "SELECT s.*, a.name as artist_name, al.title as album_title, g.name as genre_name 
                FROM songs s 
                JOIN artists a ON s.artist_id = a.id 
                LEFT JOIN albums al ON s.album_id = al.id 
                LEFT JOIN genres g ON s.genre_id = g.id 
                WHERE s.status = 'published' 
                AND (s.title LIKE ? OR a.name LIKE ? OR s.lyrics LIKE ?) 
                ORDER BY 
                    CASE 
                        WHEN s.title LIKE ? THEN 1
                        WHEN a.name LIKE ? THEN 2
                        ELSE 3
                    END,
                    s.view_count DESC
                LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit, $offset]);
    }
    
    /**
     * Get search results count
     */
    public function getSearchCount($query) {
        $searchTerm = '%' . $query . '%';
        
        $sql = "SELECT COUNT(*) as total 
                FROM songs s 
                JOIN artists a ON s.artist_id = a.id 
                WHERE s.status = 'published' 
                AND (s.title LIKE ? OR a.name LIKE ? OR s.lyrics LIKE ?)";
        
        $result = $this->db->fetchOne($sql, [$searchTerm, $searchTerm, $searchTerm]);
        return $result['total'] ?? 0;
    }
    
    /**
     * Get songs by artist
     */
    public function getSongsByArtist($artistId, $page = 1, $limit = SONGS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT s.*, a.name as artist_name, al.title as album_title, g.name as genre_name 
                FROM songs s 
                JOIN artists a ON s.artist_id = a.id 
                LEFT JOIN albums al ON s.album_id = al.id 
                LEFT JOIN genres g ON s.genre_id = g.id 
                WHERE s.artist_id = ? AND s.status = 'published' 
                ORDER BY s.created_at DESC 
                LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$artistId, $limit, $offset]);
    }
    
    /**
     * Get songs by genre
     */
    public function getSongsByGenre($genreId, $page = 1, $limit = SONGS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT s.*, a.name as artist_name, al.title as album_title, g.name as genre_name 
                FROM songs s 
                JOIN artists a ON s.artist_id = a.id 
                LEFT JOIN albums al ON s.album_id = al.id 
                LEFT JOIN genres g ON s.genre_id = g.id 
                WHERE s.genre_id = ? AND s.status = 'published' 
                ORDER BY s.created_at DESC 
                LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$genreId, $limit, $offset]);
    }
    
    /**
     * Increment view count for a song
     */
    public function incrementViewCount($songId) {
        $sql = "UPDATE songs SET view_count = view_count + 1 WHERE id = ?";
        return $this->db->execute($sql, [$songId]);
    }
    
    /**
     * Record song view for analytics
     */
    public function recordView($songId, $ipAddress = null, $userAgent = null) {
        $sql = "INSERT INTO song_views (song_id, ip_address, user_agent) VALUES (?, ?, ?)";
        return $this->db->execute($sql, [$songId, $ipAddress, $userAgent]);
    }
    
    /**
     * Get song tags
     */
    public function getSongTags($songId) {
        $sql = "SELECT t.* FROM tags t 
                JOIN song_tags st ON t.id = st.tag_id 
                WHERE st.song_id = ?";
        
        return $this->db->fetchAll($sql, [$songId]);
    }
}
?>
