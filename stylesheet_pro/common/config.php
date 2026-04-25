<?php
/**
 * Global configuration + DB connection helper.
 */
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'stylesheet_db';

$amazon_tracking_id = 'stylesheet-20';
$currency_symbol = '$';
$global_disclaimer = 'As an Amazon Associate, we may earn from qualifying purchases.';

/** @var PDO|null $pdo */
$pdo = null;

try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $settingsStmt = $pdo->prepare('SELECT site_name, amazon_id, footer_text FROM site_settings LIMIT 1');
    $settingsStmt->execute();
    $settings = $settingsStmt->fetch();

    if ($settings) {
        if (!empty($settings['amazon_id'])) {
            $amazon_tracking_id = (string) $settings['amazon_id'];
        }
        if (!empty($settings['footer_text'])) {
            $global_disclaimer = (string) $settings['footer_text'];
        }
        if (!empty($settings['site_name'])) {
            $site_name = (string) $settings['site_name'];
        }
    }
} catch (Throwable $e) {
    $pdo = null;
}

if (!isset($site_name)) {
    $site_name = 'StyleSheet Affiliate Pro';
}

/**
 * Escape helper.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
