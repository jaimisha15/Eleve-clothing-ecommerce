<?php
// Database Seed Script for ELEVÉ
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'eleve_ecommerce';

try {
    echo "Connecting to MySQL at $host...\n";
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    $sqlFile = __DIR__ . '/database.sql';
    if (!file_exists($sqlFile)) {
        die("Error: database.sql not found at $sqlFile\n");
    }

    echo "Reading database.sql...\n";
    $sql = file_get_contents($sqlFile);

    echo "Executing database.sql...\n";
    $pdo->exec($sql);

    echo "Verifying migration...\n";
    $prodCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $catCount  = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    $ordCount  = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $itemCount = $pdo->query("SELECT COUNT(*) FROM order_items")->fetchColumn();
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    echo "--- MIGRATION SUMMARY ---\n";
    echo "Products: $prodCount (Target: 29)\n";
    echo "Categories: $catCount (Target: 7)\n";
    echo "Orders: $ordCount (Target: 6)\n";
    echo "Order Items: $itemCount (Target: 13)\n";
    echo "Users: $userCount (Target: 1 Admin)\n";

    if ($prodCount == 29 && $ordCount == 6) {
        echo "SUCCESS: Database seeded perfectly!\n";
    } else {
        echo "WARNING: Counts did not match expected values.\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
