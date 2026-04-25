<?php
declare(strict_types=1);
require_once __DIR__ . '/../common/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$editing = $id > 0;

$data = ['title'=>'','description'=>'','image1'=>'','image2'=>'','old_price'=>'','new_price'=>'','affiliate_link'=>'','category_id'=>'','is_hot_deal'=>0];

if ($editing && $pdo instanceof PDO) {
    $st = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $st->execute([$id]);
    $row = $st->fetch();
    if ($row) { $data = $row; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo instanceof PDO) {
    $data['title'] = trim((string) ($_POST['title'] ?? ''));
    $data['description'] = trim((string) ($_POST['description'] ?? ''));
    $data['image1'] = trim((string) ($_POST['image1'] ?? ''));
    $data['image2'] = trim((string) ($_POST['image2'] ?? ''));
    $data['old_price'] = (float) ($_POST['old_price'] ?? 0);
    $data['new_price'] = (float) ($_POST['new_price'] ?? 0);
    $data['affiliate_link'] = trim((string) ($_POST['affiliate_link'] ?? ''));
    $data['category_id'] = (int) ($_POST['category_id'] ?? 0);
    $data['is_hot_deal'] = isset($_POST['is_hot_deal']) ? 1 : 0;

    if ($editing) {
        $u = $pdo->prepare('UPDATE products SET title=?, description=?, image1=?, image2=?, old_price=?, new_price=?, affiliate_link=?, category_id=?, is_hot_deal=? WHERE id=?');
        $u->execute([$data['title'],$data['description'],$data['image1'],$data['image2'],$data['old_price'],$data['new_price'],$data['affiliate_link'],$data['category_id'],$data['is_hot_deal'],$id]);
    } else {
        $i = $pdo->prepare('INSERT INTO products (title, description, image1, image2, old_price, new_price, affiliate_link, category_id, is_hot_deal, clicks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)');
        $i->execute([$data['title'],$data['description'],$data['image1'],$data['image2'],$data['old_price'],$data['new_price'],$data['affiliate_link'],$data['category_id'],$data['is_hot_deal']]);
    }
    header('Location: manage-products.php');
    exit;
}

$cats = [];
if ($pdo instanceof PDO) { $cs = $pdo->prepare('SELECT id, cat_name FROM categories ORDER BY cat_name'); $cs->execute(); $cats = $cs->fetchAll(); }
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?php echo $editing ? 'Edit' : 'Add'; ?> Product</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-950 p-4 text-white"><div class="mx-auto max-w-2xl rounded-xl border border-white/20 bg-white/10 p-4">
<h1 class="text-xl font-bold"><?php echo $editing ? 'Edit' : 'Add'; ?> Product</h1>
<form method="post" class="mt-4 grid grid-cols-1 gap-3">
<input name="title" value="<?php echo e((string) $data['title']); ?>" placeholder="Title" class="rounded bg-slate-900/70 p-2" required>
<textarea name="description" placeholder="Description" class="rounded bg-slate-900/70 p-2" required><?php echo e((string) $data['description']); ?></textarea>
<input name="image1" value="<?php echo e((string) $data['image1']); ?>" placeholder="Image 1 URL" class="rounded bg-slate-900/70 p-2" required>
<input name="image2" value="<?php echo e((string) $data['image2']); ?>" placeholder="Image 2 URL" class="rounded bg-slate-900/70 p-2">
<input type="number" step="0.01" name="old_price" value="<?php echo e((string) $data['old_price']); ?>" placeholder="Old Price" class="rounded bg-slate-900/70 p-2" required>
<input type="number" step="0.01" name="new_price" value="<?php echo e((string) $data['new_price']); ?>" placeholder="New Price" class="rounded bg-slate-900/70 p-2" required>
<input name="affiliate_link" value="<?php echo e((string) $data['affiliate_link']); ?>" placeholder="Affiliate Link" class="rounded bg-slate-900/70 p-2" required>
<select name="category_id" class="rounded bg-slate-900/70 p-2" required>
    <option value="">Select Category</option>
    <?php foreach ($cats as $cat): ?><option value="<?php echo (int) $cat['id']; ?>" <?php echo (int)$data['category_id']===(int)$cat['id']?'selected':''; ?>><?php echo e((string) $cat['cat_name']); ?></option><?php endforeach; ?>
</select>
<label><input type="checkbox" name="is_hot_deal" <?php echo (int)$data['is_hot_deal']===1?'checked':''; ?>> Hot Deal</label>
<button class="rounded bg-cyan-300 p-2 font-semibold text-slate-900">Save Product</button>
</form></div></body></html>
