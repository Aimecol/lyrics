-- Song Lyrics Platform Database Schema
-- Normalized database design for scalability and performance

-- Drop tables if they exist (for fresh installation)
DROP TABLE IF EXISTS song_views;
DROP TABLE IF EXISTS song_tags;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS user_favorites;
DROP TABLE IF EXISTS songs;
DROP TABLE IF EXISTS albums;
DROP TABLE IF EXISTS artists;
DROP TABLE IF EXISTS genres;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS admin_users;

-- Create database
CREATE DATABASE IF NOT EXISTS lyrics_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lyrics_platform;

-- Artists table
CREATE TABLE artists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    bio TEXT DEFAULT NULL,
    country VARCHAR(100) DEFAULT NULL,
    website VARCHAR(255) DEFAULT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_artist_name (name)
);

-- Genres table
CREATE TABLE genres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_genre_name (name)
);

-- Albums table
CREATE TABLE albums (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    artist_id INT NOT NULL,
    genre_id INT DEFAULT NULL,
    release_year YEAR DEFAULT NULL,
    cover_image VARCHAR(500) DEFAULT NULL,
    total_tracks INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE SET NULL,
    INDEX idx_album_title (title),
    INDEX idx_album_artist (artist_id),
    INDEX idx_album_year (release_year)
);

-- Songs table (main content table)
CREATE TABLE songs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    artist_id INT NOT NULL,
    album_id INT DEFAULT NULL,
    genre_id INT DEFAULT NULL,
    lyrics TEXT NOT NULL,
    duration TIME DEFAULT NULL,
    track_number INT DEFAULT NULL,
    release_year YEAR DEFAULT NULL,
    language VARCHAR(50) DEFAULT 'English',
    is_explicit BOOLEAN DEFAULT FALSE,
    is_featured BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    status ENUM('draft', 'published', 'archived') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE,
    FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE SET NULL,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE SET NULL,
    INDEX idx_song_title (title),
    INDEX idx_song_artist (artist_id),
    INDEX idx_song_album (album_id),
    INDEX idx_song_genre (genre_id),
    INDEX idx_song_status (status),
    INDEX idx_song_views (view_count DESC),
    INDEX idx_song_created (created_at DESC),
    FULLTEXT idx_song_search (title, lyrics)
);

-- Tags table (for additional categorization)
CREATE TABLE tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    color VARCHAR(7) DEFAULT '#007bff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tag_name (name)
);

-- Song-Tags junction table (many-to-many relationship)
CREATE TABLE song_tags (
    song_id INT NOT NULL,
    tag_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (song_id, tag_id),
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Admin users table
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) DEFAULT NULL,
    role ENUM('admin', 'moderator', 'editor') DEFAULT 'editor',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_admin_username (username),
    INDEX idx_admin_email (email)
);

-- Users table (for future user features like favorites, comments)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) DEFAULT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_username (username),
    INDEX idx_user_email (email)
);

-- User favorites table
CREATE TABLE user_favorites (
    user_id INT NOT NULL,
    song_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, song_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE
);

-- Song views tracking table
CREATE TABLE song_views (
    id INT PRIMARY KEY AUTO_INCREMENT,
    song_id INT NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE,
    INDEX idx_view_song (song_id),
    INDEX idx_view_date (viewed_at)
);

-- Insert sample data

-- Sample genres
INSERT INTO genres (name, description) VALUES
('Pop', 'Popular music genre'),
('Rock', 'Rock music genre'),
('Hip Hop', 'Hip hop and rap music'),
('R&B', 'Rhythm and blues music'),
('Electronic', 'Electronic music'),
('Country', 'Country music'),
('Jazz', 'Jazz music'),
('Classical', 'Classical music'),
('Folk', 'Folk music'),
('Indie', 'Independent music');

-- Sample artists
INSERT INTO artists (name, country) VALUES
('Sample Artist One', 'USA'),
('Example Band', 'UK'),
('Demo Singer', 'Canada'),
('Test Group', 'Australia'),
('Sample Duo', 'Germany');

-- Sample albums
INSERT INTO albums (title, artist_id, genre_id, release_year, total_tracks) VALUES
('First Album', 1, 1, 2023, 12),
('Greatest Hits', 2, 2, 2022, 15),
('New Sounds', 3, 3, 2024, 10);

-- Sample tags
INSERT INTO tags (name, color) VALUES
('Love Songs', '#ff6b6b'),
('Upbeat', '#4ecdc4'),
('Sad', '#45b7d1'),
('Party', '#f9ca24'),
('Acoustic', '#6c5ce7'),
('Live', '#fd79a8'),
('Cover', '#00b894'),
('Remix', '#e17055');

-- Sample songs (with placeholder lyrics)
INSERT INTO songs (title, artist_id, album_id, genre_id, lyrics, release_year, language, is_featured) VALUES
('Sample Song One', 1, 1, 1, '[Verse 1]\nThis is a sample song\nWith placeholder lyrics\nFor demonstration purposes\n\n[Chorus]\nSample lyrics here\nNot copyrighted content\nJust for testing\n\n[Verse 2]\nAnother verse example\nShowing the format\nOf how lyrics display', 2023, 'English', TRUE),

('Demo Track Two', 2, 2, 2, '[Intro]\nDemo track beginning\nInstrumental section\n\n[Verse 1]\nExample lyrics content\nOriginal placeholder text\nNot real song lyrics\n\n[Bridge]\nBridge section example\nTransition in the song\n\n[Outro]\nSong ending section\nFade out example', 2022, 'English', FALSE),

('Test Song Three', 3, 3, 3, '[Verse 1]\nTest lyrics for display\nShowing formatting\nLine breaks and sections\n\n[Chorus]\nChorus placeholder text\nRepeated section example\nNot actual copyrighted lyrics\n\n[Verse 2]\nSecond verse content\nContinuation of song\nDemo purposes only', 2024, 'English', TRUE);

-- Link songs with tags
INSERT INTO song_tags (song_id, tag_id) VALUES
(1, 1), (1, 2),
(2, 3), (2, 6),
(3, 2), (3, 4);

-- Create default admin user (username: admin, password: admin123)
-- Password hash for 'admin123' using PHP password_hash()
INSERT INTO admin_users (username, email, password_hash, full_name, role) VALUES
('admin', 'admin@lyricsplatform.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- Create indexes for performance
CREATE INDEX idx_songs_featured ON songs(is_featured, created_at DESC);
CREATE INDEX idx_songs_popular ON songs(view_count DESC, created_at DESC);
CREATE INDEX idx_songs_recent ON songs(created_at DESC);

-- Views for common queries
CREATE VIEW popular_songs AS
SELECT s.*, a.name as artist_name, al.title as album_title, g.name as genre_name
FROM songs s
JOIN artists a ON s.artist_id = a.id
LEFT JOIN albums al ON s.album_id = al.id
LEFT JOIN genres g ON s.genre_id = g.id
WHERE s.status = 'published'
ORDER BY s.view_count DESC, s.created_at DESC;

CREATE VIEW recent_songs AS
SELECT s.*, a.name as artist_name, al.title as album_title, g.name as genre_name
FROM songs s
JOIN artists a ON s.artist_id = a.id
LEFT JOIN albums al ON s.album_id = al.id
LEFT JOIN genres g ON s.genre_id = g.id
WHERE s.status = 'published'
ORDER BY s.created_at DESC;

CREATE VIEW featured_songs AS
SELECT s.*, a.name as artist_name, al.title as album_title, g.name as genre_name
FROM songs s
JOIN artists a ON s.artist_id = a.id
LEFT JOIN albums al ON s.album_id = al.id
LEFT JOIN genres g ON s.genre_id = g.id
WHERE s.status = 'published' AND s.is_featured = TRUE
ORDER BY s.created_at DESC;
