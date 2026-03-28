<?php
// includes/footer.php
?>
    </main>
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="logo" style="color: white; margin-bottom: 1.5rem;">DigitalMarket</div>
                    <p style="opacity: 0.7; font-size: 0.875rem;">Premium digital products marketplace for high-quality assets, templates, and scripts.</p>
                </div>
                <div>
                    <h4 class="footer-title">Company</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo BASE_URL; ?>/about.php" class="footer-link">About Us</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/contact.php" class="footer-link">Contact Us</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/category.php" class="footer-link">Marketplace</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-title">Support & Legal</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo BASE_URL; ?>/privacy.php" class="footer-link">Privacy Policy</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/terms.php" class="footer-link">Terms & Conditions</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/disclaimer.php" class="footer-link">Disclaimer</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-title">Stay Updated</h4>
                    <div class="search-bar" style="max-width: none;">
                        <input type="email" placeholder="Email address" style="background: rgba(255,255,255,0.05); color: white; border-color: rgba(255,255,255,0.1);">
                        <button class="btn btn-primary" style="position: absolute; right: 4px; top: 4px; padding: 0.5rem 1rem; font-size: 0.875rem;">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; <?php echo date('Y'); ?> DigitalMarket. All rights reserved.
            </div>
        </div>
    </footer>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
