<?php
declare(strict_types=1);
require_once __DIR__ . '/../common/config.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo instanceof PDO) {
    $user = trim((string) ($_POST['user'] ?? ''));
    $pass = (string) ($_POST['pass'] ?? '');

    $stmt = $pdo->prepare('SELECT id, user, pass FROM users WHERE user = ? LIMIT 1');
    $stmt->execute([$user]);
    $row = $stmt->fetch();

    if ($row && password_verify($pass, (string) $row['pass'])) {
        $_SESSION['admin_id'] = (int) $row['id'];
        $_SESSION['admin_user'] = (string) $row['user'];
        header('Location: index.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin Login</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="min-h-screen bg-slate-950 p-4 text-white">
<div class="mx-auto mt-16 max-w-md rounded-2xl border border-white/20 bg-white/10 p-6 backdrop-blur-xl">
    <h1 class="text-2xl font-bold">Admin Login</h1>
    <?php if ($error): ?><p class="mt-3 rounded bg-rose-400/20 p-2 text-sm"><?php echo e($error); ?></p><?php endif; ?>
    <form method="post" class="mt-4 space-y-3">
        <input class="w-full rounded-lg bg-slate-900/70 p-2" name="user" placeholder="Username" required>
        <input class="w-full rounded-lg bg-slate-900/70 p-2" type="password" name="pass" placeholder="Password" required>
        <button class="w-full rounded-lg bg-cyan-300 p-2 font-semibold text-slate-900">Login</button>
    </form>
</div>
</body></html>
