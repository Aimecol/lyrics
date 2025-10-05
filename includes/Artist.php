<?php
/**
 * Artist model class for handling artist-related database operations
 */

class Artist {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Get all artists with song count
     */
    public function getAllArtists($page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT a.*, COUNT(s.id) as song_count 
                FROM artists a 
                LEFT JOIN songs s ON a.id = s.artist_id AND s.status = 'published'
                GROUP BY a.id 
                ORDER BY a.name ASC 
                LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$limit, $offset]);
    }
    
    /**
     * Get artist by ID with details
     */
    public function getArtistById($id) {
        $sql = "SELECT a.*, COUNT(s.id) as song_count 
                FROM artists a 
                LEFT JOIN songs s ON a.id = s.artist_id AND s.status = 'published'
                WHERE a.id = ? 
                GROUP BY a.id";
        
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Get popular artists (by total song views)
     */
    public function getPopularArtists($limit = 10) {
        $sql = "SELECT a.*, COUNT(s.id) as song_count, SUM(s.view_count) as total_views 
                FROM artists a 
                LEFT JOIN songs s ON a.id = s.artist_id AND s.status = 'published'
                GROUP BY a.id 
                HAVING song_count > 0
                ORDER BY total_views DESC, song_count DESC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    /**
     * Search artists by name
     */
    public function searchArtists($query, $limit = 20) {
        $searchTerm = '%' . $query . '%';
        
        $sql = "SELECT a.*, COUNT(s.id) as song_count 
                FROM artists a 
                LEFT JOIN songs s ON a.id = s.artist_id AND s.status = 'published'
                WHERE a.name LIKE ? 
                GROUP BY a.id 
                ORDER BY a.name ASC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$searchTerm, $limit]);
    }
    
    /**
     * Get artist's albums
     */
    public function getArtistAlbums($artistId) {
        $sql = "SELECT al.*, COUNT(s.id) as song_count 
                FROM albums al 
                LEFT JOIN songs s ON al.id = s.album_id AND s.status = 'published'
                WHERE al.artist_id = ? 
                GROUP BY al.id 
                ORDER BY al.release_year DESC, al.title ASC";
        
        return $this->db->fetchAll($sql, [$artistId]);
    }
    
    /**
     * Get total artists count
     */
    public function getTotalArtistsCount() {
        $sql = "SELECT COUNT(*) as total FROM artists";
        $result = $this->db->fetchOne($sql);
        return $result['total'] ?? 0;
    }
}
?>
