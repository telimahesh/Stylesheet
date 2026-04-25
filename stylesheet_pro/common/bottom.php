<footer class="mt-10 border-t border-white/20 bg-white/5 p-4 pb-24 text-center text-xs text-slate-300">
    <?php echo e($global_disclaimer); ?>
</footer>

<nav class="fixed bottom-0 left-0 right-0 z-50 border-t border-white/20 bg-slate-900/85 backdrop-blur-xl lg:hidden">
    <div class="grid grid-cols-4 text-center text-xs">
        <a href="/stylesheet_pro/index.php" class="px-2 py-3"><i class="fa-solid fa-house block text-base text-cyan-300"></i>Home</a>
        <a href="/stylesheet_pro/index.php?hot=1" class="px-2 py-3"><i class="fa-solid fa-fire block text-base text-fuchsia-300"></i>Hot Deals</a>
        <a href="/stylesheet_pro/category.php" class="px-2 py-3"><i class="fa-solid fa-layer-group block text-base text-cyan-300"></i>Categories</a>
        <a href="/stylesheet_pro/index.php?search=" class="px-2 py-3"><i class="fa-solid fa-magnifying-glass block text-base text-fuchsia-300"></i>Search</a>
    </div>
</nav>

<script>
    document.addEventListener('contextmenu', function (e) { e.preventDefault(); });
    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey && (e.key === 'u' || e.key === 'U')) {
            e.preventDefault();
        }
    });
    document.addEventListener('selectstart', function (e) { e.preventDefault(); });
</script>
</body>
</html>
