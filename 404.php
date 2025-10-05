<?php
/**
 * 404 Error Page - Song Lyrics Platform
 */

$page_title = 'Page Not Found';
$page_description = 'The page you are looking for could not be found.';

// Include header
include 'includes/header.php';
?>

<div style="text-align: center; padding: 4rem 0;">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="font-size: 6rem; color: #6c757d; margin-bottom: 1rem;">
            <i class="fas fa-music"></i>
        </div>
        
        <h1 style="font-size: 3rem; color: #333; margin-bottom: 1rem;">404</h1>
        
        <h2 style="color: #6c757d; margin-bottom: 2rem;">Page Not Found</h2>
        
        <p style="color: #6c757d; font-size: 1.1rem; margin-bottom: 3rem; line-height: 1.6;">
            The page you are looking for might have been removed, had its name changed, 
            or is temporarily unavailable.
        </p>
        
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
                <i class="fas fa-home"></i> Go to Homepage
            </a>
            
            <a href="<?php echo SITE_URL; ?>/browse" class="btn btn-secondary">
                <i class="fas fa-list"></i> Browse Songs
            </a>
            
            <a href="<?php echo SITE_URL; ?>/search" class="btn btn-secondary">
                <i class="fas fa-search"></i> Search
            </a>
        </div>
        
        <div style="margin-top: 3rem; padding: 2rem; background: #f8f9fa; border-radius: 8px;">
            <h3 style="color: #333; margin-bottom: 1rem;">Popular Suggestions</h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; text-align: left;">
                <div>
                    <h4 style="color: #007bff; margin-bottom: 0.5rem;">Browse Music</h4>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li><a href="<?php echo SITE_URL; ?>/popular">Popular Songs</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/recent">Recent Additions</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/featured">Featured Songs</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 style="color: #007bff; margin-bottom: 0.5rem;">Quick Links</h4>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li><a href="<?php echo SITE_URL; ?>">Homepage</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/browse">All Songs</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/search">Search</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
