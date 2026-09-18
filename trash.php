<?php include "header.php"; ?>
<?php include "navbar.php"; ?>

<div class="trash-header" style="margin-bottom: 20px;">
    <h1><?php echo __('trash'); ?></h1>
    <p><?php echo __('trash_desc'); ?></p>
</div>

<?php
    try {
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT * FROM passwords WHERE user_id = ? AND is_deleted = 1 ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $podatki = $stmt->fetchAll();

        if (count($podatki) > 0) {
            echo '<div class="password-table-container">
                    <table class="password-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>' . __('add_new_password_title') . '</th>
                                <th>' . __('show_card_username') . '</th>
                                <th>' . __('show_card_password') . '</th>
                                <th>' . __('show_card_email') . '</th>
                                <th>Kategorija</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>';
            
            foreach ($podatki as $row) {
                renderPasswordRow($row, 'trash');
            }
            
            echo '      </tbody>
                    </table>
                  </div>';
        } else {
            echo "<div class='empty-state'>" . __('add_new_password_empty_state') . "</div>";
        }
    } catch (\PDOException $e) {
        echo "<div class='alert alert-danger'>" . __('add_new_password_alert_danger') . $e->getMessage() . "</div>";
    }
    ?>

<?php include "footer.php"; ?>
