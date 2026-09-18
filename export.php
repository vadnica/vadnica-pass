<?php include "header.php"; ?>
<?php include "navbar.php"; ?>

<div class="empty-state">
    <div class="category-icon" style="font-size: 4rem; margin-bottom: 1.5rem; display: inline-block;
    background: #f9fafb; padding: 20px; border-radius: 50%; box-shadow: var(--shadow);">
        📤
    </div>
    <h2><?php echo __('export_file'); ?></h2>
    <p style="max-width: 600px; margin: 0 auto 1.5rem; color: var(--text-muted);">
        <?php echo __('export_in_progress'); ?>
    </p>
    
    <div class="warning-box" style="background-color: rgba(231, 76, 60, 0.1); border-left: 4px solid #e74c3c;
    padding: 1.5rem; margin: 2rem auto; text-align: left; border-radius: 8px; max-width: 650px; box-shadow: var(--shadow);">
        <div style="display: flex; align-items: flex-start; gap: 15px;">
            <span class="material-icons-outlined" style="color: #e74c3c; font-size: 2rem;">warning</span>
            <div>
                <h4 style="color: #c0392b; margin: 0 0 5px 0; font-weight: 600;"><?php echo $_SESSION['lang'] == 'sl' ? 'Varnostno opozorilo' : 'Security Warning'; ?></h4>
                <p style="color: #c0392b; margin: 0; line-height: 1.5;">
                    <?php echo __('export_warning'); ?>
                </p>
            </div>
        </div>
    </div>

    <div class="instructions-box" style="background-color: var(--bg-color); padding: 1.5rem; margin: 2rem auto;
    text-align: left; border-radius: 8px; max-width: 650px; box-shadow: var(--shadow); border: 1px solid var(--border-color);">
        <h4 style="margin: 0 0 15px 0; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <span class="material-icons-outlined" style="color: var(--primary-color);">help_outline</span>
            <?php echo __('export_how_to_keepass'); ?>
        </h4>
        <ol style="margin: 0; padding-left: 20px; line-height: 1.6; color: var(--text-main);">
            <li style="margin-bottom: 8px;"><?php echo __('export_xml_step1'); ?></li>
            <li style="margin-bottom: 8px;"><?php echo __('export_xml_step2'); ?></li>
            <li style="margin-bottom: 8px;">
                <?php echo __('export_xml_step3'); ?>
            </li>
            <li style="margin-bottom: 8px;"><?php echo __('export_xml_step4'); ?></li>
            <li style="margin-bottom: 8px;"><?php echo __('export_xml_step5'); ?></li>
        </ol>
    </div>

    <div class="instructions-box" style="background-color: var(--bg-color); padding: 1.5rem; margin: 2rem auto;
    text-align: left; border-radius: 8px; max-width: 650px; box-shadow: var(--shadow); border: 1px solid var(--border-color);">
        <h4 style="margin: 0 0 15px 0; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <span class="material-icons-outlined" style="color: var(--primary-color);">help_outline</span>
            <?php echo __('export_how_to_keepassxc'); ?>
        </h4>
        <ol style="margin: 0; padding-left: 20px; line-height: 1.6; color: var(--text-main);">
            <li style="margin-bottom: 8px;"><?php echo __('export_csv_step1'); ?></li>
            <li style="margin-bottom: 8px;"><?php echo __('export_csv_step2'); ?></li>
            <li style="margin-bottom: 8px;">
                <?php echo __('export_csv_step3'); ?>
            </li>
        </ol>
    </div>

    <div class="export-actions" style="margin-top: 2.5rem; display: flex; flex-direction: column; align-items: center; gap: 20px;" 
         data-confirm-msg="<?php echo htmlspecialchars(__('export_confirm_msg')); ?>">
        <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
            <a href="export_csv.php" class="btn-primary" style="text-decoration: none; padding: 14px 30px; border-radius: 10px; display: inline-flex; align-items: center; gap: 10px; font-weight: 600; transition: transform 0.2s; background-color: var(--primary-color); color: white;">
                <span class="material-icons-outlined">description</span>
                <?php echo __('export_csv_btn'); ?>
            </a>

            <a href="export_xml.php" class="btn-secondary" style="text-decoration: none; padding: 14px 30px;
            border-radius: 10px; display: inline-flex; align-items: center; gap: 10px; font-weight: 600;
            transition: transform 0.2s; background: #f9fafb; border: 1px solid var(--border-color); color: var(--text-main);">
                <span class="material-icons-outlined">code</span>
                <?php echo __('export_xml_btn'); ?>
            </a>
        </div>

        <div class="warning-box" style="background-color: rgba(231, 76, 60, 0.1); border-left: 4px solid #e74c3c;
    padding: 1.5rem; margin: 2rem auto; text-align: left; border-radius: 8px; max-width: 650px; box-shadow: var(--shadow);">
            <div style="display: flex; align-items: flex-start; gap: 15px;">
                <span class="material-icons-outlined" style="color: #e74c3c; font-size: 2rem;">warning</span>
                <div>
                    <h4 style="color: #c0392b; margin: 0 0 5px 0; font-weight: 600;"><?php echo $_SESSION['lang'] == 'sl' ? 'Varnostno opozorilo' : 'Security Warning'; ?></h4>
                    <p style="color: #c0392b; margin: 0; line-height: 1.5;">
                        <?php echo __('export_warning'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
</style>

<?php include "footer.php"; ?>
<script src="export_xml.js"></script>
