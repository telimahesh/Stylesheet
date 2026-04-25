<?php
declare(strict_types=1);
require_once __DIR__ . '/../common/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo instanceof PDO) {
    $site = trim((string) ($_POST['site_name'] ?? 'StyleSheet Affiliate Pro'));
    $amazon = trim((string) ($_POST['amazon_id'] ?? 'stylesheet-20'));
    $email = trim((string) ($_POST['contact_email'] ?? 'support@example.com'));
    $footer = trim((string) ($_POST['footer_text'] ?? ''));
    $theme = ($_POST['theme_mode'] ?? 'dark') === 'light' ? 'light' : 'dark';

    $check = $pdo->prepare('SELECT id FROM site_settings LIMIT 1');
    $check->execute();
    $row = $check->fetch();
    if ($row) {
        $u = $pdo->prepare('UPDATE site_settings SET site_name=?, amazon_id=?, contact_email=?, footer_text=?, theme_mode=? WHERE id=?');
        $u->execute([$site,$amazon,$email,$footer,$theme,(int) $row['id']]);
    } else {
        $i = $pdo->prepare('INSERT INTO site_settings (site_name, amazon_id, contact_email, footer_text, theme_mode) VALUES (?, ?, ?, ?, ?)');
        $i->execute([$site,$amazon,$email,$footer,$theme]);
    }
    $msg = 'Settings updated.';
}

$current = ['site_name'=>'StyleSheet Affiliate Pro','amazon_id'=>'stylesheet-20','contact_email'=>'support@example.com','footer_text'=>'As an Amazon Associate, we may earn from qualifying purchases.','theme_mode'=>'dark'];
if ($pdo instanceof PDO) {
    $s = $pdo->prepare('SELECT site_name, amazon_id, contact_email, footer_text, theme_mode FROM site_settings LIMIT 1');
    $s->execute();
    $r = $s->fetch();
    if ($r) { $current = $r; }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Settings</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-950 p-4 text-white"><div class="mx-auto max-w-2xl rounded-xl border border-white/20 bg-white/10 p-4">
<h1 class="text-xl font-bold">Site Settings</h1>
<?php if ($msg): ?><p class="mt-2 rounded bg-emerald-300/20 p-2 text-sm"><?php echo e($msg); ?></p><?php endif; ?>
<form method="post" class="mt-4 grid gap-3">
<input name="site_name" value="<?php echo e((string) $current['site_name']); ?>" class="rounded bg-slate-900/70 p-2" placeholder="Site Name">
<input name="amazon_id" value="<?php echo e((string) $current['amazon_id']); ?>" class="rounded bg-slate-900/70 p-2" placeholder="Amazon Tracking ID">
<input type="email" name="contact_email" value="<?php echo e((string) $current['contact_email']); ?>" class="rounded bg-slate-900/70 p-2" placeholder="Contact Email">
<input name="footer_text" value="<?php echo e((string) $current['footer_text']); ?>" class="rounded bg-slate-900/70 p-2" placeholder="Footer/Disclaimer Text">
<select name="theme_mode" class="rounded bg-slate-900/70 p-2">
<option value="dark" <?php echo (string)$current['theme_mode']==='dark'?'selected':''; ?>>Cyberpunk Dark</option>
<option value="light" <?php echo (string)$current['theme_mode']==='light'?'selected':''; ?>>Minimalist Light</option>
</select>
<button class="rounded bg-cyan-300 p-2 font-semibold text-slate-900">Save Settings</button>
</form></div></body></html>
