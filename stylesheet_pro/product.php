<?php
declare(strict_types=1);
require_once __DIR__ . '/common/config.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = null;

if ($pdo instanceof PDO && $id > 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['go_amazon'])) {
        $up = $pdo->prepare('UPDATE products SET clicks = clicks + 1 WHERE id = ?');
        $up->execute([$id]);

        $linkStmt = $pdo->prepare('SELECT affiliate_link FROM products WHERE id = ? LIMIT 1');
        $linkStmt->execute([$id]);
        $row = $linkStmt->fetch();
        if ($row && !empty($row['affiliate_link'])) {
            header('Location: ' . $row['affiliate_link']);
            exit;
        }
    }

    $stmt = $pdo->prepare('SELECT p.*, c.cat_name FROM products p INNER JOIN categories c ON c.id = p.category_id WHERE p.id = ? LIMIT 1');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}

$page_title = $product ? (string) $product['title'] : 'Product';
require_once __DIR__ . '/common/header.php';
?>
<main class="mx-auto max-w-5xl px-4 pb-36 pt-6">
    <?php if (!$product): ?>
        <div class="rounded-xl border border-rose-300/40 bg-rose-300/10 p-4">Product not found.</div>
    <?php else: ?>
        <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="grid grid-cols-3 gap-2">
                <img src="<?php echo e((string) $product['image1']); ?>" class="h-28 w-full rounded-xl object-cover">
                <img src="<?php echo e((string) $product['image2']); ?>" class="h-28 w-full rounded-xl object-cover">
                <img src="<?php echo e((string) ($product['image2'] ?: $product['image1'])); ?>" class="h-28 w-full rounded-xl object-cover">
            </div>
            <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-xl">
                <h1 class="text-2xl font-bold"><?php echo e((string) $product['title']); ?></h1>
                <p class="mt-2 text-slate-300"><?php echo e((string) $product['description']); ?></p>
                <p class="mt-4"><span class="line-through text-slate-400"><?php echo e($currency_symbol) . number_format((float) $product['old_price'], 2); ?></span> <span class="ml-2 text-2xl font-bold text-emerald-300"><?php echo e($currency_symbol) . number_format((float) $product['new_price'], 2); ?></span></p>
            </div>
        </section>

        <form method="post" class="fixed bottom-16 left-0 right-0 z-40 px-4 lg:static lg:mt-6 lg:px-0">
            <button name="go_amazon" value="1" class="w-full rounded-xl bg-cyan-300 px-4 py-3 font-semibold text-slate-900 shadow-[0_0_20px_rgba(34,211,238,0.8)]">Check Price on Amazon</button>
            <p class="mt-2 text-center text-xs text-slate-300"><?php echo e($global_disclaimer); ?></p>
        </form>

        <p class="mt-4 text-sm text-cyan-200">External link attribute preview: <a href="<?php echo e((string) $product['affiliate_link']); ?>" rel="nofollow sponsored noopener" target="_blank" class="underline">Affiliate Link</a></p>
    <?php endif; ?>
</main>
<?php require_once __DIR__ . '/common/bottom.php'; ?>
