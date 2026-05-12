<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\App;
use App\Webhook\Handler;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$config = require __DIR__ . '/config/config.php';
$app = App::init($config);

$provider = $_GET['provider'] ?? $config['whatsapp']['provider'];

try {
    match ($provider) {
        'meta' => Handler::handleMeta(),
        'twilio' => Handler::handleTwilio(),
        default => throw new \InvalidArgumentException("Unknown webhook provider: $provider"),
    };
} catch (\Exception $e) {
    $app->getLogger()->error('Webhook error', ['error' => $e->getMessage()]);
    http_response_code(500);
    echo 'Error';
}
