<?php
/**
 * Admin model class for handling admin-related operations
 */

class Admin {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Authenticate admin user
     */
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM admin_users WHERE username = ? AND is_active = 1";
        $admin = $this->db->fetchOne($sql, [$username]);
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Update last login
            $this->updateLastLogin($admin['id']);
            return $admin;
        }
        
        return false;
    }
    
    /**
     * Update last login timestamp
     */
    private function updateLastLogin($adminId) {
        $sql = "UPDATE admin_users SET last_login = NOW() WHERE id = ?";
        $this->db->execute($sql, [$adminId]);
    }
    
    /**
     * Get all songs for admin (including drafts)
     */
    public function getAllSongs($page = 1, $limit = ADMIN_ITEMS_PER_PAGE, $status = null) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT s.*, a.name as artist_name, al.title as album_title, g.name as genre_name 
                FROM songs s 
                JOIN artists a ON s.artist_id = a.id 
                LEFT JOIN albums al ON s.album_id = al.id 
                LEFT JOIN genres g ON s.genre_id = g.id";
        
        $params = [];
        if ($status) {
            $sql .= " WHERE s.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get total songs count for admin
     */
    public function getTotalSongsCount($status = null) {
        $sql = "SELECT COUNT(*) as total FROM songs";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }
    
    /**
     * Add new song
     */
    public function addSong($data) {
        $sql = "INSERT INTO songs (title, artist_id, album_id, genre_id, lyrics, release_year, language, is_featured, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['artist_id'],
            $data['album_id'] ?: null,
            $data['genre_id'] ?: null,
            $data['lyrics'],
            $data['release_year'] ?: null,
            $data['language'] ?: 'English',
            isset($data['is_featured']) ? 1 : 0,
            $data['status'] ?: 'published'
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->getLastInsertId();
    }
    
    /**
     * Update song
     */
    public function updateSong($id, $data) {
        $sql = "UPDATE songs SET 
                title = ?, artist_id = ?, album_id = ?, genre_id = ?, 
                lyrics = ?, release_year = ?, language = ?, is_featured = ?, status = ?, 
                updated_at = NOW() 
                WHERE id = ?";
        
        $params = [
            $data['title'],
            $data['artist_id'],
            $data['album_id'] ?: null,
            $data['genre_id'] ?: null,
            $data['lyrics'],
            $data['release_year'] ?: null,
            $data['language'] ?: 'English',
            isset($data['is_featured']) ? 1 : 0,
            $data['status'] ?: 'published',
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * Delete song
     */
    public function deleteSong($id) {
        $sql = "DELETE FROM songs WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    /**
     * Get song by ID for editing
     */
    public function getSongById($id) {
        $sql = "SELECT s.*, a.name as artist_name, al.title as album_title, g.name as genre_name 
                FROM songs s 
                JOIN artists a ON s.artist_id = a.id 
                LEFT JOIN albums al ON s.album_id = al.id 
                LEFT JOIN genres g ON s.genre_id = g.id 
                WHERE s.id = ?";
        
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Get all artists for dropdown
     */
    public function getAllArtists() {
        $sql = "SELECT id, name FROM artists ORDER BY name ASC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get all albums for dropdown
     */
    public function getAllAlbums() {
        $sql = "SELECT al.id, al.title, a.name as artist_name 
                FROM albums al 
                JOIN artists a ON al.artist_id = a.id 
                ORDER BY a.name ASC, al.title ASC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get all genres for dropdown
     */
    public function getAllGenres() {
        $sql = "SELECT id, name FROM genres ORDER BY name ASC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Add new artist
     */
    public function addArtist($name, $country = null, $bio = null) {
        $sql = "INSERT INTO artists (name, country, bio) VALUES (?, ?, ?)";
        $this->db->execute($sql, [$name, $country, $bio]);
        return $this->db->getLastInsertId();
    }
    
    /**
     * Add new genre
     */
    public function addGenre($name, $description = null) {
        $sql = "INSERT INTO genres (name, description) VALUES (?, ?)";
        $this->db->execute($sql, [$name, $description]);
        return $this->db->getLastInsertId();
    }
    
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats() {
        $stats = [];
        
        // Total songs
        $result = $this->db->fetchOne("SELECT COUNT(*) as total FROM songs WHERE status = 'published'");
        $stats['total_songs'] = $result['total'] ?? 0;
        
        // Total artists
        $result = $this->db->fetchOne("SELECT COUNT(*) as total FROM artists");
        $stats['total_artists'] = $result['total'] ?? 0;
        
        // Total views
        $result = $this->db->fetchOne("SELECT SUM(view_count) as total FROM songs WHERE status = 'published'");
        $stats['total_views'] = $result['total'] ?? 0;
        
        // Recent songs
        $stats['recent_songs'] = $this->db->fetchAll(
            "SELECT s.title, a.name as artist_name, s.created_at 
             FROM songs s 
             JOIN artists a ON s.artist_id = a.id 
             WHERE s.status = 'published' 
             ORDER BY s.created_at DESC 
             LIMIT 5"
        );
        
        return $stats;
    }
}
?>
