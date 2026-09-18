<header class="navbar" id="navbar">
    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
    <div class="navbar-left">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <ul class="auth-nav">
                <li><a href="login.php" class="<?php echo $current_page == 'login.php' ? 'active' : ''; ?>">🔑 <?php echo __('login'); ?></a></li>
                <li><a href="register.php" class="<?php echo $current_page == 'register.php' ? 'active' : ''; ?>">📝 <?php echo __('register'); ?></a></li>
            </ul>
        <?php else: ?>
            <button class="mobile-menu-toggle" id="mobile-toggle">
                <span class="material-icons-outlined">menu</span>
            </button>
        <?php endif; ?>
    </div>

    <div class="navbar-header">
        <div class="logo">
            <span class="logo-icon">🛡️</span>
            <span class="logo-text">SafePass</span>
        </div>
    </div>

    <div class="navbar-footer">
        <div class="footer-controls">
            <div class="lang-selector">
                <a href="?lang=sl" class="lang-link <?php echo $lang == 'sl' ? 'active' : ''; ?>">SL</a> | 
                <a href="?lang=en" class="lang-link <?php echo $lang == 'en' ? 'active' : ''; ?>">EN</a>
            </div>
            <button id="theme-toggle" class="theme-toggle-btn">
                <span class="material-icons-outlined">dark_mode</span>
            </button>
        </div>
    </div>
</header>

<main class="main-content">
    <?php if (isset($_SESSION['user_id'])) include "sidebar.php"; ?>
    <div class="navbar-overlay" id="navbar-overlay"></div>
    <?php 
    $is_index = ($current_page == 'index.php');
    if (!$is_index): ?>
        <div class="content-wrapper">
    <?php endif; ?>
