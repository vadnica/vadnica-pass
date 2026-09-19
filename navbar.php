<header class="navbar" id="navbar">
    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
    <div class="navbar-left">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <button class="mobile-menu-toggle auth-menu-toggle" id="auth-menu-toggle" aria-label="<?php echo __('menu'); ?>">
                <span class="material-icons-outlined">menu</span>
            </button>
            <ul class="auth-nav">
                <li><a href="login.php" class="<?php echo $current_page == 'login.php' ? 'active' : ''; ?>">🔑 <?php echo __('login'); ?></a></li>
                <li><a href="register.php" class="<?php echo $current_page == 'register.php' ? 'active' : ''; ?>">📝 <?php echo __('register'); ?></a></li>
            </ul>
        <?php else: ?>
            <button class="mobile-menu-toggle" id="mobile-toggle" aria-label="<?php echo __('menu'); ?>">
                <span class="material-icons-outlined">menu</span>
            </button>
        <?php endif; ?>
    </div>

    <div class="navbar-header">
        <a href="<?php echo isset($_SESSION['user_id']) ? 'index.php' : 'login.php'; ?>" class="logo" style="text-decoration: none; color: inherit;">
            <span class="logo-icon">🛡️</span>
            <span class="logo-text">SafePass</span>
        </a>
    </div>

    <div class="navbar-footer">
        <div class="footer-controls">
            <div class="lang-selector">
                <a href="?lang=sl" class="lang-link <?php echo $lang == 'sl' ? 'active' : ''; ?>">SL</a> | 
                <a href="?lang=en" class="lang-link <?php echo $lang == 'en' ? 'active' : ''; ?>">EN</a>
            </div>
            <button id="theme-toggle" class="theme-toggle-btn" aria-label="Preklopi temo">
                <span class="material-icons-outlined">dark_mode</span>
            </button>
        </div>
    </div>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="auth-dropdown-menu" id="auth-dropdown-menu">
            <ul class="auth-dropdown-list">
                <li>
                    <a href="login.php" class="<?php echo $current_page == 'login.php' ? 'active' : ''; ?>">
                        <span class="dropdown-icon">🔑</span>
                        <span class="dropdown-text"><?php echo __('login'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="register.php" class="<?php echo $current_page == 'register.php' ? 'active' : ''; ?>">
                        <span class="dropdown-icon">📝</span>
                        <span class="dropdown-text"><?php echo __('register'); ?></span>
                    </a>
                </li>
            </ul>
            <div class="auth-dropdown-divider"></div>
            <div class="auth-dropdown-controls">
                <div class="lang-selector">
                    <a href="?lang=sl" class="lang-link <?php echo $lang == 'sl' ? 'active' : ''; ?>">SL</a> | 
                    <a href="?lang=en" class="lang-link <?php echo $lang == 'en' ? 'active' : ''; ?>">EN</a>
                </div>
                <button id="theme-toggle-mobile" class="theme-toggle-btn" aria-label="Preklopi temo">
                    <span class="material-icons-outlined">dark_mode</span>
                </button>
            </div>
        </div>
    <?php endif; ?>
</header>

<main class="main-content">
    <?php if (isset($_SESSION['user_id'])) include "sidebar.php"; ?>
    <div class="navbar-overlay" id="navbar-overlay"></div>
    <?php 
    $is_index = ($current_page == 'index.php');
    if (!$is_index): ?>
        <div class="content-wrapper">
    <?php endif; ?>
