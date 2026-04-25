<?php
/**
 * StyleSheet Affiliate Pro - Home Page
 *
 * Mobile-first affiliate homepage with search and category filtering.
 */
declare(strict_types=1);

$host = '127.0.0.1';
$user = 'root';
$pass = 'root';
$dbName = 'stylesheet_db';
$currencySymbol = '$';

$products = [];
$categories = [];
$errorMessage = '';

$search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
$catId = isset($_GET['cat']) ? (int) $_GET['cat'] : 0;

try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Load categories for filter UI.
    $catStmt = $pdo->query('SELECT id, cat_name, cat_icon FROM categories ORDER BY cat_name ASC');
    $categories = $catStmt->fetchAll();

    // Build product query safely using prepared statements.
    $sql = 'SELECT p.id, p.title, p.description, p.image1, p.old_price, p.new_price, p.is_hot_deal, c.cat_name
            FROM products p
            INNER JOIN categories c ON c.id = p.category_id
            WHERE 1=1';

    $params = [];

    if ($search !== '') {
        $sql .= ' AND (p.title LIKE ? OR p.description LIKE ?)';
        $keyword = '%' . $search . '%';
        $params[] = $keyword;
        $params[] = $keyword;
    }

    if ($catId > 0) {
        $sql .= ' AND p.category_id = ?';
        $params[] = $catId;
    }

    // If no filters, show featured/hot deals first then latest.
    if ($search === '' && $catId === 0) {
        $sql .= ' ORDER BY p.is_hot_deal DESC, p.id DESC';
    } else {
        $sql .= ' ORDER BY p.id DESC';
    }

    $sql .= ' LIMIT 24';

    $productStmt = $pdo->prepare($sql);
    $productStmt->execute($params);
    $products = $productStmt->fetchAll();
} catch (Throwable $e) {
    $errorMessage = 'Unable to load products right now. Please run install.php or check DB settings.';
}

$activeCategoryName = 'All';
if ($catId > 0) {
    foreach ($categories as $cat) {
        if ((int) $cat['id'] === $catId) {
            $activeCategoryName = (string) $cat['cat_name'];
            break;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StyleSheet Affiliate Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-950 text-white selection:bg-cyan-300 selection:text-slate-900">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_10%_10%,_#06b6d430,_transparent_35%),radial-gradient(circle_at_90%_20%,_#8b5cf640,_transparent_35%),radial-gradient(circle_at_50%_90%,_#22d3ee20,_transparent_40%)]"></div>

    <!-- Sticky Glass Navbar -->
    <header class="sticky top-0 z-50 border-b border-white/10 bg-slate-900/60 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3">
            <a href="index.php" class="text-lg font-bold text-cyan-300">StyleSheet <span class="text-fuchsia-300">Affiliate Pro</span></a>
            <form method="get" action="index.php" class="flex w-full max-w-md items-center gap-2 rounded-xl border border-white/20 bg-white/10 p-2">
                <i class="fa-solid fa-magnifying-glass text-cyan-300"></i>
                <input
                    type="text"
                    name="search"
                    value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Search products..."
                    class="w-full bg-transparent text-sm text-white placeholder-slate-300 outline-none"
                >
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 pb-24 pt-6">
        <!-- Hero -->
        <section class="rounded-3xl border border-white/20 bg-white/10 p-6 backdrop-blur-xl shadow-[0_0_45px_rgba(34,211,238,0.25)]">
            <h1 class="bg-gradient-to-r from-cyan-300 via-fuchsia-300 to-cyan-200 bg-clip-text text-3xl font-extrabold text-transparent sm:text-5xl">
                Discover Futuristic Deals, Save Smarter.
            </h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-200 sm:text-base">Your mobile-first cyberpunk storefront for trending Amazon affiliate products.</p>
            <a href="#products" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-cyan-400/90 px-5 py-3 font-semibold text-slate-900 transition-all hover:-translate-y-0.5 hover:shadow-[0_0_26px_rgba(34,211,238,0.8)]">
                <i class="fa-solid fa-bag-shopping"></i> Explore Deals
            </a>
        </section>

        <!-- Filter Chips -->
        <section class="mt-6 flex flex-wrap items-center gap-2">
            <a href="index.php" class="rounded-full border px-4 py-2 text-sm transition-all <?php echo $catId === 0 ? 'border-cyan-300 bg-cyan-300/20 text-cyan-200' : 'border-white/20 bg-white/5 hover:bg-white/10'; ?>">All</a>
            <?php foreach ($categories as $cat): ?>
                <a
                    href="index.php?cat=<?php echo (int) $cat['id']; ?>"
                    class="rounded-full border px-4 py-2 text-sm transition-all <?php echo $catId === (int) $cat['id'] ? 'border-cyan-300 bg-cyan-300/20 text-cyan-200' : 'border-white/20 bg-white/5 hover:bg-white/10'; ?>"
                >
                    <i class="fa-solid <?php echo htmlspecialchars((string) $cat['cat_icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                    <?php echo htmlspecialchars((string) $cat['cat_name'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            <?php endforeach; ?>
        </section>

        <section class="mt-4 text-sm text-slate-300">
            Showing results for <span class="font-semibold text-cyan-300"><?php echo htmlspecialchars($activeCategoryName, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php if ($search !== ''): ?>
                and keyword <span class="font-semibold text-fuchsia-300">"<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"</span>
            <?php endif; ?>
        </section>

        <?php if ($errorMessage !== ''): ?>
            <section class="mt-6 rounded-xl border border-rose-300/40 bg-rose-300/10 p-4 text-rose-200">
                <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
            </section>
        <?php endif; ?>

        <!-- Product Grid -->
        <section id="products" class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($products as $product): ?>
                <?php
                    $oldPrice = (float) $product['old_price'];
                    $newPrice = (float) $product['new_price'];
                    $discount = $oldPrice > 0 ? (int) round((($oldPrice - $newPrice) / $oldPrice) * 100) : 0;
                ?>
                <article class="group overflow-hidden rounded-2xl border border-white/20 bg-white/10 backdrop-blur-xl transition-all hover:-translate-y-1 hover:shadow-[0_0_26px_rgba(168,85,247,0.45)]">
                    <div class="relative">
                        <img src="<?php echo htmlspecialchars((string) $product['image1'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) $product['title'], ENT_QUOTES, 'UTF-8'); ?>" class="h-52 w-full object-cover">
                        <?php if ((int) $product['is_hot_deal'] === 1): ?>
                            <span class="animate-pulse absolute left-2 top-2 rounded-full bg-fuchsia-400/90 px-3 py-1 text-xs font-semibold text-white">Hot Deal</span>
                        <?php endif; ?>
                        <?php if ($discount > 0): ?>
                            <span class="absolute right-2 top-2 rounded-full bg-cyan-400/90 px-3 py-1 text-xs font-semibold text-slate-900">-<?php echo $discount; ?>%</span>
                        <?php endif; ?>
                    </div>

                    <div class="p-4">
                        <p class="text-xs uppercase tracking-wide text-cyan-300"><?php echo htmlspecialchars((string) $product['cat_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <h2 class="mt-1 line-clamp-2 min-h-[48px] text-base font-semibold"><?php echo htmlspecialchars((string) $product['title'], ENT_QUOTES, 'UTF-8'); ?></h2>

                        <div class="mt-2 text-amber-300">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i>
                        </div>

                        <div class="mt-3 flex items-center gap-2">
                            <span class="text-sm text-slate-400 line-through"><?php echo $currencySymbol . number_format($oldPrice, 2); ?></span>
                            <span class="text-lg font-bold text-emerald-300"><?php echo $currencySymbol . number_format($newPrice, 2); ?></span>
                        </div>

                        <p class="mt-1 text-xs text-fuchsia-300">Price drop active</p>

                        <a href="product.php?id=<?php echo (int) $product['id']; ?>" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-cyan-300/90 px-4 py-2 text-sm font-semibold text-slate-900 transition-all hover:shadow-[0_0_20px_rgba(34,211,238,0.8)]">
                            <i class="fa-solid fa-eye"></i> View Product
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <?php if (empty($products) && $errorMessage === ''): ?>
            <section class="mt-10 rounded-xl border border-white/20 bg-white/5 p-6 text-center text-slate-300">
                No products found. Try another keyword or category.
            </section>
        <?php endif; ?>
    </main>

    <!-- Mobile Bottom Nav -->
    <nav class="fixed bottom-0 left-0 right-0 z-50 border-t border-white/20 bg-slate-900/80 backdrop-blur-xl lg:hidden">
        <div class="grid grid-cols-4 text-center text-xs">
            <a href="index.php" class="px-2 py-3 text-cyan-300"><i class="fa-solid fa-house block text-base"></i>Home</a>
            <a href="index.php?search=deal" class="px-2 py-3 text-slate-200"><i class="fa-solid fa-fire block text-base"></i>Hot Deals</a>
            <a href="#products" class="px-2 py-3 text-slate-200"><i class="fa-solid fa-layer-group block text-base"></i>Categories</a>
            <a href="#" class="px-2 py-3 text-slate-200"><i class="fa-solid fa-magnifying-glass block text-base"></i>Search</a>
        </div>
    </nav>

    <script>
        // Basic front-end content protection (as requested).
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
