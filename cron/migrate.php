<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\App;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$config = require __DIR__ . '/../config/config.php';

try {
    $app = App::init($config);
    $db = $app->getDb();

    // Run migrations
    $db->migrate();

    echo "[" . date('Y-m-d H:i:s') . "] Database migrated successfully.\n";
} catch (\Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage() . "\n";
    exit(1);
}
