<?php
// Define platform constant
define('LYRICS_PLATFORM', true);

// Include configuration
require_once __DIR__ . '/../config/config.php';

// Get current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($page_description) ? sanitize($page_description) : SITE_DESCRIPTION; ?>">
    <meta name="keywords" content="lyrics, songs, music, artists, albums">
    <meta name="author" content="<?php echo SITE_NAME; ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:title" content="<?php echo isset($page_title) ? sanitize($page_title) . ' - ' . SITE_NAME : SITE_NAME; ?>">
    <meta property="og:description" content="<?php echo isset($page_description) ? sanitize($page_description) : SITE_DESCRIPTION; ?>">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    <meta property="twitter:title" content="<?php echo isset($page_title) ? sanitize($page_title) . ' - ' . SITE_NAME : SITE_NAME; ?>">
    <meta property="twitter:description" content="<?php echo isset($page_description) ? sanitize($page_description) : SITE_DESCRIPTION; ?>">
    
    <title><?php echo isset($page_title) ? sanitize($page_title) . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.ico">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <!-- Additional CSS for specific pages -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css_file): ?>
            <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/<?php echo $css_file; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "<?php echo SITE_NAME; ?>",
        "url": "<?php echo SITE_URL; ?>",
        "description": "<?php echo SITE_DESCRIPTION; ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "<?php echo SITE_URL; ?>/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <a href="<?php echo SITE_URL; ?>" class="logo">
                    <i class="fas fa-music"></i> <?php echo SITE_NAME; ?>
                </a>
                
                <!-- Search Bar -->
                <div class="search-container">
                    <form class="search-form" action="<?php echo SITE_URL; ?>/search" method="GET">
                        <input type="text" 
                               name="q" 
                               class="search-input" 
                               placeholder="Search songs, artists, or lyrics..." 
                               value="<?php echo isset($_GET['q']) ? sanitize($_GET['q']) : ''; ?>"
                               autocomplete="off">
                        <button type="submit" class="search-btn" aria-label="Search">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Navigation -->
                <nav class="nav">
                    <a href="<?php echo SITE_URL; ?>" 
                       class="nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <a href="<?php echo SITE_URL; ?>/browse" 
                       class="nav-link <?php echo $current_page === 'browse' ? 'active' : ''; ?>">
                        <i class="fas fa-list"></i> Browse
                    </a>
                    <a href="<?php echo SITE_URL; ?>/popular" 
                       class="nav-link <?php echo $current_page === 'popular' ? 'active' : ''; ?>">
                        <i class="fas fa-fire"></i> Popular
                    </a>
                    <a href="<?php echo SITE_URL; ?>/recent" 
                       class="nav-link <?php echo $current_page === 'recent' ? 'active' : ''; ?>">
                        <i class="fas fa-clock"></i> Recent
                    </a>
                    <?php if (isAdminLoggedIn()): ?>
                        <a href="<?php echo SITE_URL; ?>/admin" 
                           class="nav-link">
                            <i class="fas fa-cog"></i> Admin
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>
    
    <!-- Flash Messages -->
    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
        <div class="container" style="margin-top: 1rem;">
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo sanitize($flash['message']); ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="main">
        <div class="container">
