<?php
/** @var string $site_name */
$page_title = $page_title ?? $site_name;
$current_search = isset($_GET['search']) ? (string) $_GET['search'] : '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-950 text-white">
<div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_20%_20%,_#06b6d435,_transparent_30%),radial-gradient(circle_at_80%_30%,_#a855f735,_transparent_35%),radial-gradient(circle_at_50%_90%,_#22d3ee20,_transparent_45%)]"></div>

<header class="sticky top-0 z-50 border-b border-white/20 bg-slate-900/60 backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3">
        <a href="/stylesheet_pro/index.php" class="shrink-0 text-lg font-bold text-cyan-300"><?php echo e($site_name); ?></a>
        <form method="get" action="/stylesheet_pro/index.php" class="flex w-full items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-3 py-2">
            <i class="fa-solid fa-bolt text-fuchsia-300 animate-pulse"></i>
            <input type="text" name="search" placeholder="Live Search products..." value="<?php echo e($current_search); ?>" class="w-full bg-transparent text-sm placeholder-slate-300 outline-none">
        </form>
    </div>
</header>
