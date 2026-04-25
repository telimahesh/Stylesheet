<?php
declare(strict_types=1);
require_once __DIR__ . '/../common/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$totalProducts = 0;
$totalCategories = 0;
$totalClicks = 0;
if ($pdo instanceof PDO) {
    $s1 = $pdo->prepare('SELECT COUNT(*) AS t FROM products'); $s1->execute(); $totalProducts = (int) $s1->fetch()['t'];
    $s2 = $pdo->prepare('SELECT COUNT(*) AS t FROM categories'); $s2->execute(); $totalCategories = (int) $s2->fetch()['t'];
    $s3 = $pdo->prepare('SELECT COALESCE(SUM(clicks),0) AS t FROM products'); $s3->execute(); $totalClicks = (int) $s3->fetch()['t'];
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin Dashboard</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-950 p-4 text-white">
<div class="mx-auto max-w-6xl">
    <div class="mb-4 flex items-center justify-between"><h1 class="text-2xl font-bold">Dashboard</h1><form method="post"><button name="logout" class="rounded bg-rose-300 px-3 py-1 text-slate-900">Logout</button></form></div>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-white/20 bg-white/10 p-4">Total Products: <strong><?php echo $totalProducts; ?></strong></div>
        <div class="rounded-xl border border-white/20 bg-white/10 p-4">Total Categories: <strong><?php echo $totalCategories; ?></strong></div>
        <div class="rounded-xl border border-white/20 bg-white/10 p-4">Total Clicks: <strong><?php echo $totalClicks; ?></strong></div>
    </div>
    <div class="mt-6 flex flex-wrap gap-2">
        <a class="rounded bg-cyan-300 px-3 py-2 font-semibold text-slate-900" href="add-product.php">Add Product</a>
        <a class="rounded bg-fuchsia-300 px-3 py-2 font-semibold text-slate-900" href="manage-products.php">Manage Products</a>
        <a class="rounded bg-emerald-300 px-3 py-2 font-semibold text-slate-900" href="settings.php">Settings</a>
    </div>
</div>
</body></html>
