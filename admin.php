<?php
include "header.php";
include "navbar.php";

// Preverjanje če je uporabnik admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo "<div class='alert alert-danger'>Nimate dostopa do te strani.</div>";
    include "footer.php";
    exit;
}

// Pridobitev vseh uporabnikov in števila njihovih gesel
try {
    $sql = "SELECT u.id, u.username, u.email, u.created_at, u.is_admin, u.is_active, COUNT(p.id) as password_count 
            FROM users u 
            LEFT JOIN passwords p ON u.id = p.user_id 
            GROUP BY u.id 
            ORDER BY u.created_at DESC";
    $stmt = $pdo->query($sql);
    $users = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Napaka pri pridobivanju uporabnikov: " . $e->getMessage());
}
?>

<div class="admin-panel">
    <div class="page-header">
        <h1>Administracija uporabnikov</h1>
        <p>Pregled vseh registriranih uporabnikov in njihove aktivnosti.</p>
    </div>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th data-label="ID">ID</th>
                    <th data-label="Uporabniško ime">Uporabniško ime</th>
                    <th data-label="E-pošta">E-pošta</th>
                    <th data-label="Datum registracije">Datum registracije</th>
                    <th data-label="Status">Status</th>
                    <th data-label="Št. gesel">Št. gesel</th>
                    <th data-label="Vloga">Vloga</th>
                    <th data-label="Akcije">Akcije</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td data-label="ID"><?php echo $user['id']; ?></td>
                    <td data-label="Uporabniško ime"><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                    <td data-label="E-pošta"><?php echo htmlspecialchars($user['email'] ?: '/'); ?></td>
                    <td data-label="Datum registracije"><?php echo date('d. m. Y H:i', strtotime($user['created_at'])); ?></td>
                    <td data-label="Status">
                        <?php if ($user['is_active']): ?>
                            <span class="status-badge active">Aktiven</span>
                        <?php else: ?>
                            <span class="status-badge pending">Čaka potrditev</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Št. gesel"><span class="badge"><?php echo $user['password_count']; ?></span></td>
                    <td data-label="Vloga">
                        <?php if ($user['is_admin']): ?>
                            <span class="status-badge admin">Admin</span>
                        <?php else: ?>
                            <span class="status-badge">Uporabnik</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Akcije">
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn-delete" onclick="return confirm('Ali ste prepričani, da želite izbrisati tega uporabnika in vsa njegova gesla?')">Izbriši</a>
                        <?php else: ?>
                            <span class="text-muted">Admin</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "footer.php"; ?>
