</main>

<script src="<?= URL_ROOT ?>/assets/js/admin.js"></script>
<script>
(function() {
    var btn     = document.querySelector('.mobile-topbar .hamburger');
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var close   = document.getElementById('sidebarClose');
    if (!btn || !sidebar) return;
    function open() {
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function shut() {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('open');
        document.body.style.overflow = '';
    }
    btn.addEventListener('click', open);
    if (close)   close.addEventListener('click', shut);
    if (overlay) overlay.addEventListener('click', shut);
    sidebar.querySelectorAll('nav a').forEach(function(a) { a.addEventListener('click', shut); });
})();
</script>
</body>
</html>
