<?php
declare(strict_types=1);
require_once __DIR__ . '/common/config.php';

$search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
$catId = isset($_GET['category']) ? (int) $_GET['category'] : 0;

$params = [];
$products = [];

if ($pdo instanceof PDO) {
    $sql = 'SELECT p.id, p.title, p.image1, p.new_price, c.cat_name FROM products p INNER JOIN categories c ON c.id = p.category_id WHERE 1=1';

    if ($catId > 0) {
        $sql .= ' AND p.category_id = ?';
        $params[] = $catId;
    }
    if ($search !== '') {
        $sql .= ' AND p.title LIKE ?';
        $params[] = '%' . $search . '%';
    }

    $sql .= ' ORDER BY p.id DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    $catStmt = $pdo->prepare('SELECT id, cat_name FROM categories ORDER BY cat_name ASC');
    $catStmt->execute();
    $cats = $catStmt->fetchAll();
}

$page_title = 'Categories';
require_once __DIR__ . '/common/header.php';
?>
<main class="mx-auto max-w-6xl px-4 pb-24 pt-6">
    <form method="get" class="grid grid-cols-1 gap-3 rounded-xl border border-white/20 bg-white/10 p-4 md:grid-cols-3">
        <select name="category" class="rounded-lg bg-slate-900/70 p-2">
            <option value="0">All Categories</option>
            <?php foreach (($cats ?? []) as $cat): ?>
                <option value="<?php echo (int) $cat['id']; ?>" <?php echo $catId === (int) $cat['id'] ? 'selected' : ''; ?>><?php echo e((string) $cat['cat_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Search in category" class="rounded-lg bg-slate-900/70 p-2">
        <button class="rounded-lg bg-cyan-300 p-2 font-semibold text-slate-900">Filter</button>
    </form>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <?php foreach ($products as $p): ?>
            <a href="product.php?id=<?php echo (int) $p['id']; ?>" class="rounded-xl border border-white/20 bg-white/10 p-3 transition-all hover:scale-[1.02]">
                <img src="<?php echo e((string) $p['image1']); ?>" class="h-40 w-full rounded-lg object-cover">
                <h2 class="mt-2 font-semibold"><?php echo e((string) $p['title']); ?></h2>
                <p class="text-sm text-cyan-200"><?php echo e((string) $p['cat_name']); ?></p>
                <p class="font-bold text-emerald-300"><?php echo e($currency_symbol) . number_format((float) $p['new_price'], 2); ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</main>
<?php require_once __DIR__ . '/common/bottom.php'; ?>
