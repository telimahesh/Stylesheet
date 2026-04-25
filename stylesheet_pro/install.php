<?php
/**
 * StyleSheet Affiliate Pro - Installer
 *
 * Creates database + schema and seeds initial data.
 * Run once, then delete or protect this file in production.
 */

declare(strict_types=1);

$host = '127.0.0.1';
$user = 'root';
$pass = 'root';
$dbName = 'stylesheet_db';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_install'])) {
    try {
        // 1) Connect without database first.
        $pdoRoot = new PDO("mysql:host={$host};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        // 2) Create database if it does not exist.
        $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // 3) Connect directly to the application database.
        $pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        $pdo->beginTransaction();

        // 4) Create required tables.
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS users (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user VARCHAR(100) NOT NULL UNIQUE,
                pass VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS categories (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                cat_name VARCHAR(120) NOT NULL,
                cat_icon VARCHAR(120) DEFAULT 'fa-tag'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS products (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                image1 VARCHAR(255) NOT NULL,
                image2 VARCHAR(255) DEFAULT '',
                old_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                new_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                affiliate_link VARCHAR(500) NOT NULL,
                category_id INT UNSIGNED NOT NULL,
                is_hot_deal TINYINT(1) NOT NULL DEFAULT 0,
                clicks INT UNSIGNED NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_products_category
                    FOREIGN KEY (category_id) REFERENCES categories(id)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS site_settings (
                site_name VARCHAR(150) NOT NULL,
                amazon_id VARCHAR(120) NOT NULL,
                contact_email VARCHAR(150) NOT NULL,
                footer_text VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        // 5) Seed admin user if missing.
        $checkUserStmt = $pdo->prepare('SELECT id FROM users WHERE user = ? LIMIT 1');
        $checkUserStmt->execute(['admin']);
        if (!$checkUserStmt->fetch()) {
            $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $insertUserStmt = $pdo->prepare('INSERT INTO users (user, pass) VALUES (?, ?)');
            $insertUserStmt->execute(['admin', $hashedPassword]);
        }

        // 6) Seed default categories if table is empty.
        $countCategoryStmt = $pdo->query('SELECT COUNT(*) AS total FROM categories');
        $totalCategories = (int) $countCategoryStmt->fetch()['total'];

        if ($totalCategories === 0) {
            $categoryRows = [
                ['Smartphones', 'fa-mobile-screen-button'],
                ['Laptops', 'fa-laptop'],
                ['Audio', 'fa-headphones'],
                ['Wearables', 'fa-watch-smart'],
                ['Gaming', 'fa-gamepad'],
            ];

            $insertCategoryStmt = $pdo->prepare('INSERT INTO categories (cat_name, cat_icon) VALUES (?, ?)');
            foreach ($categoryRows as $row) {
                $insertCategoryStmt->execute([$row[0], $row[1]]);
            }
        }

        // 7) Seed site settings if table is empty.
        $countSettingsStmt = $pdo->query('SELECT COUNT(*) AS total FROM site_settings');
        $totalSettingsRows = (int) $countSettingsStmt->fetch()['total'];

        if ($totalSettingsRows === 0) {
            $insertSettingsStmt = $pdo->prepare(
                'INSERT INTO site_settings (site_name, amazon_id, contact_email, footer_text) VALUES (?, ?, ?, ?)'
            );
            $insertSettingsStmt->execute([
                'StyleSheet Affiliate Pro',
                'stylesheet-20',
                'hello@example.com',
                '© ' . date('Y') . ' StyleSheet Affiliate Pro. All rights reserved.',
            ]);
        }

        // 8) Seed 10 sample products if products table is empty.
        $countProductsStmt = $pdo->query('SELECT COUNT(*) AS total FROM products');
        $totalProducts = (int) $countProductsStmt->fetch()['total'];

        if ($totalProducts === 0) {
            $sampleProducts = [
                ['Nova X1 5G Phone', 'Flagship phone with OLED display and all-day battery.', 'https://picsum.photos/seed/p1/800/800', 'https://picsum.photos/seed/p1b/800/800', 899.99, 749.99, 'https://www.amazon.com/dp/B000000001?tag=stylesheet-20', 1, 1],
                ['PixelBeam Pro Laptop', 'Thin and fast laptop for creators and developers.', 'https://picsum.photos/seed/p2/800/800', 'https://picsum.photos/seed/p2b/800/800', 1499.99, 1299.99, 'https://www.amazon.com/dp/B000000002?tag=stylesheet-20', 2, 0],
                ['EchoPulse Wireless Earbuds', 'Immersive audio with active noise cancellation.', 'https://picsum.photos/seed/p3/800/800', 'https://picsum.photos/seed/p3b/800/800', 199.99, 119.99, 'https://www.amazon.com/dp/B000000003?tag=stylesheet-20', 3, 1],
                ['AeroFit Smartwatch', 'Fitness and health tracking with AMOLED screen.', 'https://picsum.photos/seed/p4/800/800', 'https://picsum.photos/seed/p4b/800/800', 299.99, 229.99, 'https://www.amazon.com/dp/B000000004?tag=stylesheet-20', 4, 0],
                ['Quantum RGB Gaming Mouse', 'Ultra-light gaming mouse with precision sensor.', 'https://picsum.photos/seed/p5/800/800', 'https://picsum.photos/seed/p5b/800/800', 79.99, 49.99, 'https://www.amazon.com/dp/B000000005?tag=stylesheet-20', 5, 1],
                ['VoltCharge 65W GaN Charger', 'Compact multi-port fast charger for travel.', 'https://picsum.photos/seed/p6/800/800', 'https://picsum.photos/seed/p6b/800/800', 59.99, 34.99, 'https://www.amazon.com/dp/B000000006?tag=stylesheet-20', 1, 0],
                ['SpectraView 27" Monitor', '2K IPS monitor with 165Hz refresh rate.', 'https://picsum.photos/seed/p7/800/800', 'https://picsum.photos/seed/p7b/800/800', 379.99, 299.99, 'https://www.amazon.com/dp/B000000007?tag=stylesheet-20', 2, 1],
                ['BassForge Soundbar', 'Room-filling surround sound with deep bass.', 'https://picsum.photos/seed/p8/800/800', 'https://picsum.photos/seed/p8b/800/800', 249.99, 179.99, 'https://www.amazon.com/dp/B000000008?tag=stylesheet-20', 3, 0],
                ['ZenPad Mechanical Keyboard', 'Hot-swappable keys with neon backlight.', 'https://picsum.photos/seed/p9/800/800', 'https://picsum.photos/seed/p9b/800/800', 139.99, 99.99, 'https://www.amazon.com/dp/B000000009?tag=stylesheet-20', 5, 1],
                ['SkyDrone Mini 4K', 'Pocket-size drone with stabilized 4K video.', 'https://picsum.photos/seed/p10/800/800', 'https://picsum.photos/seed/p10b/800/800', 499.99, 429.99, 'https://www.amazon.com/dp/B000000010?tag=stylesheet-20', 4, 0],
            ];

            $insertProductStmt = $pdo->prepare(
                'INSERT INTO products (title, description, image1, image2, old_price, new_price, affiliate_link, category_id, is_hot_deal, clicks)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)'
            );

            foreach ($sampleProducts as $product) {
                $insertProductStmt->execute($product);
            }
        }

        $pdo->commit();
        $message = 'Installation completed successfully! Admin login: admin / admin123';
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - StyleSheet Affiliate Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_#22d3ee33,_transparent_45%),radial-gradient(circle_at_bottom,_#a855f733,_transparent_40%)]"></div>

    <main class="relative z-10 mx-auto max-w-2xl p-4 sm:p-8">
        <section class="rounded-3xl border border-white/20 bg-white/10 p-6 backdrop-blur-xl shadow-[0_0_40px_rgba(34,211,238,0.25)]">
            <h1 class="text-2xl font-bold sm:text-3xl">
                <i class="fa-solid fa-bolt text-cyan-300"></i>
                StyleSheet Affiliate Pro Installer
            </h1>
            <p class="mt-3 text-sm text-slate-200">This will create <strong>stylesheet_db</strong>, required tables, and sample data.</p>

            <?php if ($message !== ''): ?>
                <div class="mt-4 rounded-xl border border-emerald-300/50 bg-emerald-300/10 p-3 text-emerald-200">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="mt-4 rounded-xl border border-rose-300/50 bg-rose-300/10 p-3 text-rose-200">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="mt-6">
                <input type="hidden" name="run_install" value="1">
                <button type="submit" class="w-full rounded-xl bg-cyan-400/90 px-5 py-3 font-semibold text-slate-900 transition-all hover:scale-[1.01] hover:shadow-[0_0_24px_rgba(34,211,238,0.65)]">
                    <i class="fa-solid fa-database"></i> Run Installation
                </button>
            </form>

            <p class="mt-4 text-xs text-slate-300">Security note: delete or restrict this file after successful setup.</p>
        </section>
    </main>
</body>
</html>
