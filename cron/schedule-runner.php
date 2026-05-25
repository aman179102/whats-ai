<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\App;
use App\Scheduler\Scheduler;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$config = require __DIR__ . '/../config/config.php';

try {
    $app = App::init($config);
    $app->getDb()->getPdo(); // ensure DB connection

    Scheduler::process();

    echo "[" . date('Y-m-d H:i:s') . "] Scheduler executed successfully.\n";
} catch (\Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage() . "\n";
    exit(1);
}
