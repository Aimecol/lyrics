# Song Lyrics Platform

A professional, scalable, responsive website for browsing and reading song lyrics. Built with PHP, MySQL, HTML5, CSS3, and minimal JavaScript for optimal performance.

## Features

### Frontend
- **Responsive Design**: Mobile-first approach that scales beautifully across all devices
- **Modern UI**: Clean, professional interface with Google Fonts and Font Awesome icons
- **Fast Loading**: Optimized CSS and minimal JavaScript for quick page loads
- **Search Functionality**: Advanced search by song title, artist, or lyrics content
- **Intuitive Navigation**: Easy browsing with categories, filters, and pagination

### Backend
- **PHP & MySQLi**: Robust backend using PHP with MySQLi for database operations
- **Normalized Database**: Scalable database design with proper relationships
- **Security**: CSRF protection, input sanitization, and secure admin authentication
- **Performance**: Optimized queries with proper indexing and caching support

### Admin Panel
- **Secure Login**: Protected admin area with session management
- **Song Management**: Add, edit, delete songs with rich metadata
- **Artist & Genre Management**: Organize content with proper categorization
- **Dashboard**: Overview with statistics and recent activity
- **Responsive Admin UI**: Mobile-friendly admin interface

## Installation

### Requirements
- **XAMPP** (or similar LAMP/WAMP stack)
- **PHP 7.4+**
- **MySQL 5.7+**
- **Apache** with mod_rewrite enabled

### Setup Instructions

1. **Download and Extract**
   - Place all files in your XAMPP `htdocs` directory (e.g., `htdocs/lyrics/`)

2. **Database Setup**
   - Open phpMyAdmin or MySQL command line
   - Import the database schema: `database/schema.sql`
   - This will create the database and sample data

3. **Configuration**
   - Edit `config/config.php` if needed (default settings work with XAMPP)
   - Ensure the database credentials match your setup:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USERNAME', 'root');
     define('DB_PASSWORD', '');
     define('DB_NAME', 'lyrics_platform');
     ```

4. **Apache Configuration**
   - Ensure mod_rewrite is enabled in Apache
   - The `.htaccess` file handles URL rewriting automatically

5. **Permissions**
   - Ensure the `logs/` directory is writable (for debug mode)

6. **Access the Platform**
   - Frontend: `http://localhost/lyrics/`
   - Admin Panel: `http://localhost/lyrics/admin/`
   - Default admin credentials:
     - Username: `admin`
     - Password: `admin123`

## File Structure

```
lyrics/
├── admin/                  # Admin panel files
│   ├── index.php          # Admin dashboard
│   ├── login.php          # Admin login
│   ├── logout.php         # Admin logout
│   ├── songs.php          # Manage songs
│   └── add-song.php       # Add new song
├── assets/                # Static assets
│   ├── css/               # Stylesheets
│   │   ├── style.css      # Main styles
│   │   └── admin.css      # Admin styles
│   └── js/                # JavaScript files
│       └── main.js        # Main JavaScript
├── config/                # Configuration files
│   └── config.php         # Main configuration
├── database/              # Database files
│   └── schema.sql         # Database schema and sample data
├── includes/              # PHP includes
│   ├── header.php         # Header template
│   ├── footer.php         # Footer template
│   ├── functions.php      # Common functions
│   ├── Database.php       # Database class
│   ├── Song.php           # Song model
│   ├── Artist.php         # Artist model
│   └── Admin.php          # Admin model
├── logs/                  # Log files (when debug mode enabled)
├── index.php              # Homepage
├── browse.php             # Browse songs
├── search.php             # Search functionality
├── song.php               # Individual song page
├── artist.php             # Artist page
├── genre.php              # Genre page
├── 404.php                # 404 error page
├── .htaccess              # Apache configuration
└── README.md              # This file
```

## Database Schema

The platform uses a normalized database design with the following main tables:

- **songs**: Main content table with lyrics and metadata
- **artists**: Artist information
- **albums**: Album information
- **genres**: Music genres
- **tags**: Additional categorization
- **admin_users**: Admin authentication
- **song_views**: View tracking for analytics

## Key Features Explained

### Search Functionality
- Full-text search across song titles, artist names, and lyrics
- Search result highlighting
- Intelligent ranking (title matches first, then artist, then lyrics)
- Pagination for large result sets

### Responsive Design
- Mobile-first CSS approach
- Flexible grid layouts
- Touch-friendly interface elements
- Optimized for all screen sizes

### Performance Optimization
- Minimal JavaScript usage
- Optimized database queries with proper indexing
- CSS and JavaScript compression ready
- Browser caching headers configured

### Security Features
- CSRF token protection
- Input sanitization and validation
- Secure password hashing
- Session management
- SQL injection prevention

## Customization

### Styling
- Edit `assets/css/style.css` for main site styling
- Edit `assets/css/admin.css` for admin panel styling
- Colors, fonts, and layouts are easily customizable

### Configuration
- Modify `config/config.php` for site settings
- Update database credentials, site name, URLs, etc.
- Enable/disable debug mode and other features

### Adding Content
- Use the admin panel to add songs, artists, and genres
- Import bulk data via SQL if needed
- Extend the database schema for additional fields

## Browser Support

- **Modern Browsers**: Chrome, Firefox, Safari, Edge (latest versions)
- **Mobile Browsers**: iOS Safari, Chrome Mobile, Samsung Internet
- **Graceful Degradation**: Older browsers receive basic functionality

## Performance Notes

- **Page Load**: Optimized for sub-2-second load times
- **Database**: Indexed for fast queries even with thousands of songs
- **Caching**: Ready for Redis/Memcached integration
- **CDN Ready**: Static assets can be served from CDN

## Security Considerations

- Change default admin credentials immediately
- Disable debug mode in production
- Use HTTPS in production
- Regular security updates recommended
- Consider additional authentication methods for production

## Support

This is a complete, production-ready lyrics platform. The code is well-documented and follows PHP best practices for easy maintenance and extension.

## License

This project is provided as-is for educational and commercial use.
