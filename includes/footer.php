        </div> <!-- End container -->
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-left">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                </div>
                
                <div class="footer-center">
                    <nav class="footer-nav">
                        <a href="<?php echo SITE_URL; ?>">Home</a>
                        <a href="<?php echo SITE_URL; ?>/browse">Browse</a>
                        <a href="<?php echo SITE_URL; ?>/popular">Popular</a>
                        <a href="<?php echo SITE_URL; ?>/recent">Recent</a>
                    </nav>
                </div>
                
                <div class="footer-right">
                    <div class="social-links">
                        <a href="#" aria-label="Facebook" title="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" aria-label="Twitter" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" aria-label="Instagram" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" aria-label="YouTube" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    
    <!-- Additional JavaScript for specific pages -->
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js_file): ?>
            <script src="<?php echo SITE_URL; ?>/assets/js/<?php echo $js_file; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Google Analytics (replace with your tracking ID) -->
    <?php if (!DEBUG_MODE): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_TRACKING_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_TRACKING_ID');
    </script>
    <?php endif; ?>
</body>
</html>
