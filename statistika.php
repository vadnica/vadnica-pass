<?php
// statistika.php
include "header.php";
include "navbar.php";

// Strog preverjanje za admina
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo "<div class='auth-card' style='margin: 50px auto; max-width: 500px; text-align: center;'>";
    echo "    <div class='alert alert-danger'>";
    echo "        <span class='material-icons-outlined' style='vertical-align: middle;'>block</span> ";
    echo          $_SESSION['lang'] == 'sl' ? "Dostop zavrnjen! Samo za administratorje." : "Access denied! Administrators only.";
    echo "    </div>";
    echo "    <a href='index.php' class='btn-primary' style='display: inline-block; margin-top: 20px; text-decoration: none;'>" . (__('dashboard')) . "</a>";
    echo "</div>";
    include "footer.php";
    exit;
}

$countFile = __DIR__ . '/visitor_count.txt';
if (file_exists($countFile)) {
    $count = (int)file_get_contents($countFile);
} else {
    $count = 0;
}
?>

<div class="auth-container" style="padding: 20px;">
    <div class="auth-card" style="text-align: center; max-width: 600px; margin: 40px auto; padding: 40px;">
        <div class="card-header" style="justify-content: center; margin-bottom: 30px;">
            <h2 class="card-title">
                <span class="material-icons-outlined">analytics</span>
                <?php echo $_SESSION['lang'] == 'sl' ? "Statistika obiskov" : "Visit Statistics"; ?>
            </h2>
        </div>
        
        <div class="stats-display">
            <h1 style="color: #64748b; font-size: 1.2rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">
                <?php echo $_SESSION['lang'] == 'sl' ? "Skupno število obiskovalcev" : "Total Visitor Count"; ?>
            </h1>
            <div class="count" style="font-size: 6rem; color: #10b981; font-weight: 800; line-height: 1;">
                <?php echo number_format($count); ?>
            </div>
            <p style="color: #94a3b8; margin-top: 20px; font-size: 0.9rem;">
                <span class="material-icons-outlined" style="font-size: 1rem; vertical-align: middle;">schedule</span>
                <?php echo $_SESSION['lang'] == 'sl' ? "Štejemo samo obiske, daljše od 30 sekund." : "Counting visits longer than 30 seconds only."; ?>
            </p>
        </div>

        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
            <p class="note" style="color: #64748b; font-style: italic; font-size: 0.85rem;">
                <?php echo $_SESSION['lang'] == 'sl' ? "Vaši (admin) obiski in lokalni testi se ne štejejo." : "Your (admin) visits and local tests are not counted."; ?>
            </p>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
