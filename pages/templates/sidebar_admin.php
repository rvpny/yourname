<aside class="sidebar">
    <div class="sidebar-header">
        <h3>BILGI EVI</h3>
        <p>Admin: <?= htmlspecialchars($_SESSION['username'] ?? '') ?></p>
    </div>
    <?php include 'navigasi_admin.php'; ?>
</aside>