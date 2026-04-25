<?php
declare(strict_types=1);
require_once __DIR__ . '/../common/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && $pdo instanceof PDO) {
    $del = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $del->execute([(int) $_POST['delete_id']]);
}

$rows = [];
if ($pdo instanceof PDO) {
    $st = $pdo->prepare('SELECT p.id, p.title, p.new_price, p.clicks, c.cat_name FROM products p INNER JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC');
    $st->execute();
    $rows = $st->fetchAll();
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Manage Products</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-950 p-4 text-white"><div class="mx-auto max-w-6xl">
<div class="mb-4 flex items-center justify-between"><h1 class="text-2xl font-bold">Manage Products</h1><a class="rounded bg-cyan-300 px-3 py-2 text-slate-900" href="add-product.php">+ Add</a></div>
<div class="overflow-x-auto rounded-xl border border-white/20 bg-white/10">
<table class="min-w-full text-sm"><thead><tr class="border-b border-white/20"><th class="p-3 text-left">Title</th><th class="p-3">Category</th><th class="p-3">Price</th><th class="p-3">Clicks</th><th class="p-3">Actions</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
<tr class="border-b border-white/10"><td class="p-3"><?php echo e((string) $r['title']); ?></td><td class="p-3 text-center"><?php echo e((string) $r['cat_name']); ?></td><td class="p-3 text-center"><?php echo number_format((float) $r['new_price'],2); ?></td><td class="p-3 text-center"><?php echo (int) $r['clicks']; ?></td><td class="p-3 text-center"><a class="mr-2 rounded bg-fuchsia-300 px-2 py-1 text-slate-900" href="add-product.php?id=<?php echo (int) $r['id']; ?>">Edit</a><form method="post" class="inline"><input type="hidden" name="delete_id" value="<?php echo (int) $r['id']; ?>"><button class="rounded bg-rose-300 px-2 py-1 text-slate-900" onclick="return confirm('Delete this product?')">Delete</button></form></td></tr>
<?php endforeach; ?>
</tbody></table></div></div></body></html>
