<?php
declare(strict_types=1);
require_once __DIR__ . '/common/config.php';

$page_title = $site_name;

$search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
$catId = isset($_GET['cat']) ? (int) $_GET['cat'] : 0;
$hot = isset($_GET['hot']) ? 1 : 0;

$products = [];
$categories = [];

if ($pdo instanceof PDO) {
    $catStmt = $pdo->prepare('SELECT id, cat_name, cat_icon FROM categories ORDER BY cat_name ASC');
    $catStmt->execute();
    $categories = $catStmt->fetchAll();

    $sql = 'SELECT p.*, c.cat_name FROM products p INNER JOIN categories c ON c.id = p.category_id WHERE 1=1';
    $params = [];

    if ($search !== '') {
        $sql .= ' AND (p.title LIKE ? OR p.description LIKE ?)';
        $word = '%' . $search . '%';
        $params[] = $word;
        $params[] = $word;
    }
    if ($catId > 0) {
        $sql .= ' AND p.category_id = ?';
        $params[] = $catId;
    }
    if ($hot === 1) {
        $sql .= ' AND p.is_hot_deal = 1';
    }

    $sql .= ' ORDER BY p.is_hot_deal DESC, p.id DESC LIMIT 24';

    $pStmt = $pdo->prepare($sql);
    $pStmt->execute($params);
    $products = $pStmt->fetchAll();
}

require_once __DIR__ . '/common/header.php';
?>
<main class="mx-auto max-w-7xl px-4 pb-24 pt-6">
    <section class="rounded-3xl border border-white/20 bg-white/10 p-6 backdrop-blur-xl shadow-[0_0_30px_rgba(168,85,247,0.4)]">
        <h1 class="bg-gradient-to-r from-cyan-300 via-fuchsia-300 to-cyan-300 bg-clip-text text-3xl font-extrabold text-transparent sm:text-5xl">Best Tech Deals of 2024</h1>
        <p class="mt-3 text-slate-200">Premium picks with cyberpunk vibes and real savings.</p>
        <a href="#products" class="mt-4 inline-block rounded-xl bg-cyan-300 px-5 py-3 font-semibold text-slate-900 transition-all hover:scale-105 hover:shadow-[0_0_25px_rgba(34,211,238,0.8)]">Shop Deals</a>
    </section>

    <section class="mt-6 flex flex-wrap gap-2">
        <a href="index.php" class="rounded-full border border-white/20 bg-white/5 px-3 py-2 text-sm">All</a>
        <?php foreach ($categories as $cat): ?>
            <a href="index.php?cat=<?php echo (int) $cat['id']; ?>" class="rounded-full border border-white/20 bg-white/5 px-3 py-2 text-sm hover:bg-white/10 transition-all">
                <i class="fa-solid <?php echo e((string) $cat['cat_icon']); ?>"></i> <?php echo e((string) $cat['cat_name']); ?>
            </a>
        <?php endforeach; ?>
    </section>

    <section id="products" class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <?php foreach ($products as $product): ?>
            <?php $old = (float) $product['old_price']; $new = (float) $product['new_price']; $off = $old > 0 ? (int) round((($old - $new) / $old) * 100) : 0; ?>
            <article class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur-xl transition-all hover:scale-[1.02] hover:shadow-[0_0_25px_rgba(236,72,153,0.45)]">
                <div class="relative">
                    <img src="<?php echo e((string) $product['image1']); ?>" alt="<?php echo e((string) $product['title']); ?>" class="h-52 w-full rounded-t-2xl object-cover">
                    <?php if ($off > 0): ?><span class="absolute right-2 top-2 rounded-full bg-cyan-300 px-2 py-1 text-xs font-bold text-slate-900"><?php echo $off; ?>% OFF</span><?php endif; ?>
                    <?php if ((int) $product['is_hot_deal'] === 1): ?><span class="animate-pulse absolute left-2 top-2 rounded-full bg-fuchsia-400 px-2 py-1 text-xs">Limited</span><?php endif; ?>
                </div>
                <div class="p-4">
                    <h2 class="line-clamp-2 min-h-[48px] font-semibold"><?php echo e((string) $product['title']); ?></h2>
                    <p class="mt-2 text-amber-300"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i></p>
                    <p class="mt-2"><span class="text-slate-400 line-through"><?php echo e($currency_symbol) . number_format($old, 2); ?></span> <span class="text-lg font-bold text-emerald-300"><?php echo e($currency_symbol) . number_format($new, 2); ?></span></p>
                    <p class="text-xs text-fuchsia-300">Price Drop</p>
                    <a href="product.php?id=<?php echo (int) $product['id']; ?>" class="mt-3 inline-block w-full rounded-lg bg-cyan-300 px-4 py-2 text-center text-sm font-semibold text-slate-900">View Product</a>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</main>
<?php require_once __DIR__ . '/common/bottom.php'; ?>
