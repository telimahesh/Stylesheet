<?php
declare(strict_types=1);

$host = '127.0.0.1';
$user = 'root';
$pass = 'root';
$dbName = 'stylesheet_db';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $rootPdo = new PDO("mysql:host={$host};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        $pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        $pdo->beginTransaction();

        $pdo->exec('CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user VARCHAR(100) NOT NULL UNIQUE,
            pass VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec('CREATE TABLE IF NOT EXISTS categories (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            cat_name VARCHAR(120) NOT NULL,
            cat_icon VARCHAR(120) DEFAULT "fa-tag"
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec('CREATE TABLE IF NOT EXISTS products (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            image1 VARCHAR(255) NOT NULL,
            image2 VARCHAR(255) DEFAULT "",
            old_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            new_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            affiliate_link VARCHAR(500) NOT NULL,
            category_id INT UNSIGNED NOT NULL,
            is_hot_deal TINYINT(1) NOT NULL DEFAULT 0,
            clicks INT UNSIGNED NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_products_categories FOREIGN KEY (category_id)
                REFERENCES categories(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec('CREATE TABLE IF NOT EXISTS site_settings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            site_name VARCHAR(150) NOT NULL,
            amazon_id VARCHAR(120) NOT NULL,
            contact_email VARCHAR(150) NOT NULL,
            footer_text VARCHAR(255) NOT NULL,
            theme_mode VARCHAR(20) NOT NULL DEFAULT "dark"
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $userCheck = $pdo->prepare('SELECT id FROM users WHERE user = ? LIMIT 1');
        $userCheck->execute(['admin']);
        if (!$userCheck->fetch()) {
            $insertUser = $pdo->prepare('INSERT INTO users (user, pass) VALUES (?, ?)');
            $insertUser->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
        }

        $catCountStmt = $pdo->prepare('SELECT COUNT(*) AS total FROM categories');
        $catCountStmt->execute();
        $catCount = (int) $catCountStmt->fetch()['total'];
        if ($catCount === 0) {
            $insertCat = $pdo->prepare('INSERT INTO categories (cat_name, cat_icon) VALUES (?, ?)');
            $cats = [
                ['Phones', 'fa-mobile-screen-button'],
                ['Laptops', 'fa-laptop'],
                ['Gaming', 'fa-gamepad'],
                ['Audio', 'fa-headphones'],
                ['Accessories', 'fa-plug'],
            ];
            foreach ($cats as $c) {
                $insertCat->execute($c);
            }
        }

        $settingsCountStmt = $pdo->prepare('SELECT COUNT(*) AS total FROM site_settings');
        $settingsCountStmt->execute();
        $settingsCount = (int) $settingsCountStmt->fetch()['total'];
        if ($settingsCount === 0) {
            $insertSettings = $pdo->prepare('INSERT INTO site_settings (site_name, amazon_id, contact_email, footer_text, theme_mode) VALUES (?, ?, ?, ?, ?)');
            $insertSettings->execute([
                'StyleSheet Affiliate Pro',
                'stylesheet-20',
                'support@example.com',
                'As an Amazon Associate, we may earn from qualifying purchases.',
                'dark',
            ]);
        }

        $productsCountStmt = $pdo->prepare('SELECT COUNT(*) AS total FROM products');
        $productsCountStmt->execute();
        $productsCount = (int) $productsCountStmt->fetch()['total'];

        if ($productsCount === 0) {
            $insertProduct = $pdo->prepare('INSERT INTO products (title, description, image1, image2, old_price, new_price, affiliate_link, category_id, is_hot_deal, clicks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)');
            $samples = [
                ['CyberPhone Z', 'Fast 5G phone with bright OLED display.', 'https://picsum.photos/seed/c1/600/600', 'https://picsum.photos/seed/c1b/600/600', 899, 699, 'https://www.amazon.com/dp/B000000111?tag=stylesheet-20', 1, 1],
                ['UltraBook Air', 'Slim laptop for work and travel.', 'https://picsum.photos/seed/c2/600/600', 'https://picsum.photos/seed/c2b/600/600', 1299, 999, 'https://www.amazon.com/dp/B000000112?tag=stylesheet-20', 2, 0],
                ['Pro Gaming Headset', 'Spatial audio and clear mic quality.', 'https://picsum.photos/seed/c3/600/600', 'https://picsum.photos/seed/c3b/600/600', 199, 129, 'https://www.amazon.com/dp/B000000113?tag=stylesheet-20', 4, 1],
                ['RGB Mechanical Keyboard', 'Hot-swappable switches for customization.', 'https://picsum.photos/seed/c4/600/600', 'https://picsum.photos/seed/c4b/600/600', 149, 99, 'https://www.amazon.com/dp/B000000114?tag=stylesheet-20', 3, 1],
                ['4K Streaming Stick', 'Turn any TV into smart entertainment hub.', 'https://picsum.photos/seed/c5/600/600', 'https://picsum.photos/seed/c5b/600/600', 69, 49, 'https://www.amazon.com/dp/B000000115?tag=stylesheet-20', 5, 0],
                ['Wireless Mouse Pro', 'Lightweight ergonomic productivity mouse.', 'https://picsum.photos/seed/c6/600/600', 'https://picsum.photos/seed/c6b/600/600', 79, 39, 'https://www.amazon.com/dp/B000000116?tag=stylesheet-20', 5, 0],
                ['27-inch 2K Monitor', 'High refresh IPS monitor for gaming.', 'https://picsum.photos/seed/c7/600/600', 'https://picsum.photos/seed/c7b/600/600', 369, 279, 'https://www.amazon.com/dp/B000000117?tag=stylesheet-20', 3, 1],
                ['Bluetooth Speaker Max', 'Powerful bass with all-day battery.', 'https://picsum.photos/seed/c8/600/600', 'https://picsum.photos/seed/c8b/600/600', 119, 79, 'https://www.amazon.com/dp/B000000118?tag=stylesheet-20', 4, 0],
                ['USB-C Hub 9-in-1', 'Expand ports for laptop workflows.', 'https://picsum.photos/seed/c9/600/600', 'https://picsum.photos/seed/c9b/600/600', 89, 55, 'https://www.amazon.com/dp/B000000119?tag=stylesheet-20', 5, 0],
                ['Smartwatch Fit 2', 'Health tracking and long battery life.', 'https://picsum.photos/seed/c10/600/600', 'https://picsum.photos/seed/c10b/600/600', 249, 179, 'https://www.amazon.com/dp/B000000120?tag=stylesheet-20', 1, 1],
            ];

            foreach ($samples as $sample) {
                $insertProduct->execute($sample);
            }
        }

        $pdo->commit();
        $message = 'Installation successful! Admin user: admin | Password: admin123';
    } catch (Throwable $e) {
        if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = 'Installation failed: ' . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Install | StyleSheet Affiliate Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-950 text-white p-4">
<div class="mx-auto max-w-2xl rounded-3xl border border-white/20 bg-white/10 p-6 backdrop-blur-xl shadow-[0_0_35px_rgba(34,211,238,0.35)]">
    <h1 class="text-2xl font-bold"><i class="fa-solid fa-database text-cyan-300"></i> StyleSheet Affiliate Pro Installer</h1>
    <p class="mt-2 text-slate-300">Creates database and seeds starter content in one click.</p>

    <?php if ($message !== ''): ?>
        <div class="mt-4 rounded-lg border border-emerald-300/40 bg-emerald-300/10 p-3 text-emerald-200"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <div class="mt-4 rounded-lg border border-rose-300/40 bg-rose-300/10 p-3 text-rose-200"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="post" class="mt-6">
        <button type="submit" class="w-full rounded-xl bg-cyan-300 px-4 py-3 font-semibold text-slate-900 transition-all hover:shadow-[0_0_24px_rgba(34,211,238,0.75)]">Run Install</button>
    </form>
</div>
</body>
</html>
