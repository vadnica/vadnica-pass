<?php 
$current_cat = isset($_GET['cat']) ? $_GET['cat'] : null;
$current_page_base = basename($_SERVER['PHP_SELF']);
?>
<aside class="app-sidebar" id="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-section">
            <h3 class="sidebar-title"><?php echo __('dashboard'); ?></h3>
            <ul class="sidebar-nav">
                <li>
                    <a href="index.php" class="<?php echo ($current_page_base == 'index.php' && !$current_cat) ? 'active' : ''; ?>">
                        <span class="icon">🏠</span>
                        <span class="label"><?php echo __('all_passwords'); ?></span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section">
            <h3 class="sidebar-title"><?php echo __('categories'); ?></h3>
            <ul class="sidebar-nav">
                <li><a href="index.php?cat=bank" class="<?php echo $current_cat == 'bank' ? 'active' : ''; ?>"><span class="icon">🏦</span><span class="label"><?php echo __('bank'); ?></span></a></li>
                <li><a href="index.php?cat=email" class="<?php echo $current_cat == 'email' ? 'active' : ''; ?>"><span class="icon">📧</span><span class="label"><?php echo __('email'); ?></span></a></li>
                <li><a href="index.php?cat=net" class="<?php echo $current_cat == 'net' ? 'active' : ''; ?>"><span class="icon">🌐</span><span class="label"><?php echo __('internet'); ?></span></a></li>
                <li><a href="index.php?cat=store" class="<?php echo $current_cat == 'store' ? 'active' : ''; ?>"><span class="icon">🛒</span><span class="label"><?php echo __('stores'); ?></span></a></li>
                <li><a href="index.php?cat=game" class="<?php echo $current_cat == 'game' ? 'active' : ''; ?>"><span class="icon">🎮</span><span class="label"><?php echo __('game'); ?></span></a></li>
                <li><a href="index.php?cat=network" class="<?php echo $current_cat == 'network' ? 'active' : ''; ?>"><span class="icon">📶</span><span class="label"><?php echo __('network'); ?></span></a></li>
                <li><a href="index.php?cat=desktop" class="<?php echo $current_cat == 'desktop' ? 'active' : ''; ?>"><span class="icon">💻</span><span class="label"><?php echo __('desktop'); ?></span></a></li>
                <li><a href="index.php?cat=misc" class="<?php echo $current_cat == 'misc' ? 'active' : ''; ?>"><span class="icon">📂</span><span class="label"><?php echo __('misc'); ?></span></a></li>
            </ul>
        </div>

        <div class="sidebar-section">
            <h3 class="sidebar-title"><?php echo __('system'); ?></h3>
            <ul class="sidebar-nav">
                <li><a href="trash.php" class="<?php echo $current_page_base == 'trash.php' ? 'active' : ''; ?>"><span class="icon">🗑️</span><span class="label"><?php echo __('trash'); ?></span></a></li>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                    <li><a href="admin.php" class="<?php echo $current_page_base == 'admin.php' ? 'active' : ''; ?>"><span class="icon">⚙️</span><span class="label"><?php echo __('admin'); ?></span></a></li>
                    <li><a href="statistika.php" class="<?php echo $current_page_base == 'statistika.php' ? 'active' : ''; ?>"><span class="icon">📊</span><span class="label">Statistika</span></a></li>
                <?php endif; ?>
                <li><a href="export.php" class="<?php echo $current_page_base == 'export.php' ? 'active' : ''; ?>"><span class="icon">📤</span><span class="label"><?php echo __('export_file'); ?></span></a></li>
            </ul>
        </div>
        <div class="sidebar-section sidebar-footer">
            <ul class="sidebar-nav">
                <li>
                    <a href="logout.php" class="btn-logout-sidebar">
                        <span class="icon">🚪</span>
                        <span class="label"><?php echo __('logout'); ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
