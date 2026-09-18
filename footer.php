                <?php 
                $current_page = basename($_SERVER['PHP_SELF']);
                if ($current_page != 'index.php'): ?>
                    </div>
                <?php endif; ?>
            </main>
            <?php 
            $footer_lang = $_SESSION['lang'] ?? 'sl';
            $logo_light = ($footer_lang === 'en') ? 'icons/vadnica-logo-en.svg' : 'icons/vadnica-logo-sl.svg';
            $logo_dark = ($footer_lang === 'en') ? 'icons/vadnica-logo-en-dark.svg' : 'icons/vadnica-logo-sl-dark.svg';
            ?>
            <footer class="app-footer">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> SafePass - Modern Password Manager</p>
                <a href="https://vadnica.org" target="_blank" rel="noopener noreferrer" class="footer-logo-link" title="Vadnica.org">
                    <img src="<?php echo $logo_light; ?>" alt="Vadnica.org Logo" class="footer-logo logo-light">
                    <img src="<?php echo $logo_dark; ?>" alt="Vadnica.org Logo" class="footer-logo logo-dark">
                </a>
            </div>
        </footer>
    </div>

    <!-- Globalne Skripte -->
    <script type="text/javascript" src="modals.js"></script>
    <script type="text/javascript" src="footer.js"></script>
</body>
</html>
