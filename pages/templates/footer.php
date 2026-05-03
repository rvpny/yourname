</main>
    </div>

    <footer>
        <p>&copy; 2025 Perpustakaan Bilgi Evi. Semua Hak Cipta Dilindungi.</p>
    </footer>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
        <script src="../../assets/js/script.js"></script>
        <script src="../../assets/js/admin.js"></script>
    <?php endif; ?>
</body>
</html>