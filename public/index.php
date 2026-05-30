<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\App;
use App\Core\Router;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$config = require __DIR__ . '/../config/config.php';
$app = App::init($config);
$db = $app->getDb();

$db->migrate();

// Load DB settings on top of env (env always wins)
use App\Core\Settings;
$config = Settings::loadIntoConfig($config);

$router = new Router();

function render(string $template, array $data = []): void
{
    extract($data);
    require __DIR__ . '/../templates/' . $template . '.php';
}

$router->get('/', function () use ($config) {
    $stats = [
        'total_messages' => 0,
        'total_contacts' => 0,
        'active_schedules' => 0,
    ];

    try {
        $db = \App\Core\App::getInstance()->getDb()->getPdo();
        $stats['total_messages'] = $db->query('SELECT COUNT(*) FROM messages')->fetchColumn();
        $stats['total_contacts'] = $db->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
        $stats['active_schedules'] = $db->query("SELECT COUNT(*) FROM schedules WHERE status='active'")->fetchColumn();
    } catch (\Exception $e) {
        // DB not ready yet
    }

    render('dashboard', [
        'config' => $config,
        'stats' => $stats,
    ]);
});

$router->get('/settings', function () use ($config) {
    render('settings', ['config' => $config]);
});

$router->post('/settings/save', function () {
    $config = \App\Core\App::getInstance()->getConfig();

    $fields = [
        'whatsapp_provider', 'meta_phone_number_id', 'meta_access_token', 'meta_verify_token', 'meta_app_secret',
        'twilio_account_sid', 'twilio_auth_token', 'twilio_from_number',
        'web_scraper_api_url', 'web_scraper_api_key',
        'ai_provider', 'ai_model', 'ai_temperature', 'ai_max_tokens', 'ai_system_prompt',
        'openrouter_api_key', 'groq_api_key', 'gemini_api_key',
        'custom_api_key', 'custom_base_url', 'custom_model',
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            \App\Core\Settings::set($field, $_POST[$field]);
        }
    }

    header('Location: /settings?success=1');
    exit;
});

$router->get('/scheduler', function () {
    $db = \App\Core\App::getInstance()->getDb()->getPdo();
    $schedules = $db->query('SELECT * FROM schedules ORDER BY scheduled_at DESC')->fetchAll();
    $contacts = $db->query('SELECT id, name, phone FROM contacts ORDER BY name')->fetchAll();
    render('scheduler', ['schedules' => $schedules, 'contacts' => $contacts]);
});

$router->post('/scheduler/create', function () {
    $db = \App\Core\App::getInstance()->getDb()->getPdo();

    $stmt = $db->prepare("
        INSERT INTO schedules (name, message_template, ai_generated, ai_prompt, scheduled_at, repeat_type, status)
        VALUES (:name, :message_template, :ai_generated, :ai_prompt, :scheduled_at, :repeat_type, 'active')
    ");

    $stmt->execute([
        'name' => $_POST['name'] ?? 'Untitled Schedule',
        'message_template' => $_POST['message_template'] ?? '',
        'ai_generated' => (int)($_POST['ai_generated'] ?? 0),
        'ai_prompt' => $_POST['ai_prompt'] ?? null,
        'scheduled_at' => $_POST['scheduled_at'] ?? date('Y-m-d H:i:s'),
        'repeat_type' => $_POST['repeat_type'] ?? 'once',
    ]);

    $scheduleId = $db->lastInsertId();

    if (!empty($_POST['contact_ids'])) {
        $stmt = $db->prepare("INSERT INTO schedule_contacts (schedule_id, contact_id) VALUES (?, ?)");
        foreach ($_POST['contact_ids'] as $contactId) {
            $stmt->execute([$scheduleId, $contactId]);
        }
    }

    header('Location: /scheduler?created=1');
    exit;
});

$router->get('/messages', function () {
    $db = \App\Core\App::getInstance()->getDb()->getPdo();
    $messages = $db->query("
        SELECT m.*, c.name as contact_name, c.phone as contact_phone
        FROM messages m
        LEFT JOIN contacts c ON m.contact_id = c.id
        ORDER BY m.created_at DESC
        LIMIT 100
    ")->fetchAll();
    render('messages', ['messages' => $messages]);
});

$router->get('/contacts', function () {
    $db = \App\Core\App::getInstance()->getDb()->getPdo();
    $contacts = $db->query('SELECT * FROM contacts ORDER BY name')->fetchAll();
    render('contacts', ['contacts' => $contacts]);
});

$router->post('/contacts/add', function () {
    $db = \App\Core\App::getInstance()->getDb()->getPdo();
    $stmt = $db->prepare("INSERT INTO contacts (name, phone, provider) VALUES (:name, :phone, :provider)");
    $stmt->execute([
        'name' => $_POST['name'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'provider' => $_POST['provider'] ?? 'meta',
    ]);
    header('Location: /contacts?added=1');
    exit;
});

$router->get('/setup', function () use ($config) {
    render('setup', ['config' => $config]);
});

// API: Test AI connection
$router->post('/api/test-ai', function () {
    header('Content-Type: application/json');
    $app = \App\Core\App::getInstance();
    $config = $app->getConfig();

    $provider = $_POST['provider'] ?? $config['ai']['provider'];
    $model = $_POST['model'] ?? $config['ai']['model'];

    try {
        $ai = \App\AI\Factory::create($provider, array_merge($config['ai'], ['model' => $model]));
        if (!$ai->isAvailable()) {
            echo json_encode(['success' => false, 'error' => 'API key not configured for ' . $ai->getName()]);
            return;
        }
        $result = $ai->generate('Say "Hello! AI connection is working." in exactly 5 words.');
        echo json_encode($result);
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

// API: Test WhatsApp connection
$router->post('/api/test-whatsapp', function () {
    header('Content-Type: application/json');
    $app = \App\Core\App::getInstance();
    $config = $app->getConfig();
    $db = $app->getDb()->getPdo();

    $provider = $_POST['provider'] ?? $config['whatsapp']['provider'];
    $phone = $_POST['phone'] ?? '';

    if (empty($phone)) {
        echo json_encode(['success' => false, 'error' => 'Phone number is required']);
        return;
    }

    try {
        $whatsapp = \App\WhatsApp\Factory::create($provider, $config['whatsapp']);
        if (!$whatsapp->isAvailable()) {
            echo json_encode(['success' => false, 'error' => 'WhatsApp provider not configured']);
            return;
        }
        $result = $whatsapp->send($phone, '🧪 Test message from WhatsApp AI Automation. Connection working!');

        // Save test message
        if ($result['success']) {
            $stmt = $db->prepare("INSERT INTO contacts (name, phone, provider) VALUES (:name, :phone, :provider) ON CONFLICT(phone) DO UPDATE SET name=:name2");
            $stmt->execute(['name' => 'Test Contact', 'phone' => $phone, 'provider' => $provider, 'name2' => 'Test Contact']);
            $contactId = $db->lastInsertId() ?: $db->query("SELECT id FROM contacts WHERE phone='$phone'")->fetchColumn();

            $stmt = $db->prepare("INSERT INTO messages (contact_id, direction, content, status, external_id) VALUES (:cid, 'out', :content, 'sent', :eid)");
            $stmt->execute(['cid' => $contactId, 'content' => '🧪 Test message from WhatsApp AI Automation. Connection working!', 'eid' => $result['external_id'] ?? '']);
        }

        echo json_encode($result);
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

// API: Send manual message
$router->post('/api/send-message', function () {
    header('Content-Type: application/json');
    $app = \App\Core\App::getInstance();
    $config = $app->getConfig();
    $db = $app->getDb()->getPdo();

    $contactId = (int)($_POST['contact_id'] ?? 0);
    $message = $_POST['message'] ?? '';
    $useAi = isset($_POST['use_ai']);
    $aiPrompt = $_POST['ai_prompt'] ?? '';

    if (empty($message) && !$useAi) {
        echo json_encode(['success' => false, 'error' => 'Message is required']);
        return;
    }

    try {
        // Get contact
        $stmt = $db->prepare("SELECT * FROM contacts WHERE id = ?");
        $stmt->execute([$contactId]);
        $contact = $stmt->fetch();

        if (!$contact) {
            // Allow direct phone number
            $phone = $_POST['phone'] ?? '';
            if (empty($phone)) {
                echo json_encode(['success' => false, 'error' => 'Contact or phone number required']);
                return;
            }
            $stmt = $db->prepare("INSERT INTO contacts (name, phone, provider) VALUES (:name, :phone, :provider) ON CONFLICT(phone) DO UPDATE SET name=:name2");
            $stmt->execute(['name' => $phone, 'phone' => $phone, 'provider' => $config['whatsapp']['provider'], 'name2' => $phone]);
            $contactId = $db->lastInsertId() ?: $db->query("SELECT id FROM contacts WHERE phone='$phone'")->fetchColumn();
            $contact = ['id' => $contactId, 'name' => $phone, 'phone' => $phone];
        }

        // Generate with AI if requested
        if ($useAi) {
            $ai = \App\AI\Factory::create($config['ai']['provider'], $config['ai']);
            if (!$ai->isAvailable()) {
                echo json_encode(['success' => false, 'error' => 'AI provider not configured']);
                return;
            }
            $prompt = $aiPrompt ?: "Write a WhatsApp message for {$contact['name']}";
            $result = $ai->generate($prompt);
            if (!$result['success']) {
                echo json_encode(['success' => false, 'error' => 'AI generation failed: ' . ($result['error'] ?? '')]);
                return;
            }
            $message = $result['content'];
        }

        // Send
        $whatsapp = \App\WhatsApp\Factory::create($config['whatsapp']['provider'], $config['whatsapp']);
        $sendResult = $whatsapp->send($contact['phone'], $message);

        $db->prepare("INSERT INTO messages (contact_id, direction, content, ai_provider, ai_model, external_id, status) VALUES (?, 'out', ?, ?, ?, ?, ?)")->execute([
            $contactId,
            $message,
            $useAi ? $config['ai']['provider'] : null,
            $useAi ? $config['ai']['model'] : null,
            $sendResult['external_id'] ?? '',
            $sendResult['success'] ? 'sent' : 'failed',
        ]);

        echo json_encode([
            'success' => $sendResult['success'],
            'message' => $sendResult['success'] ? 'Message sent successfully!' : ($sendResult['error'] ?? 'Failed'),
            'content' => $message,
        ]);
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

// API: Bridge status
$router->get('/api/bridge/status', function () {
    header('Content-Type: application/json');
    echo json_encode(\App\Core\Bridge::getStatus());
});

// API: Schedule actions (pause/cancel/delete)
$router->post('/api/schedule/action', function () {
    header('Content-Type: application/json');
    $db = \App\Core\App::getInstance()->getDb()->getPdo();

    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if (!$id || !in_array($action, ['pause', 'resume', 'cancel', 'delete'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        return;
    }

    try {
        if ($action === 'delete') {
            $db->prepare("DELETE FROM schedule_contacts WHERE schedule_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM schedules WHERE id = ?")->execute([$id]);
        } else {
            $status = match ($action) {
                'pause' => 'paused',
                'resume' => 'active',
                'cancel' => 'cancelled',
            };
            $db->prepare("UPDATE schedules SET status = ? WHERE id = ?")->execute([$status, $id]);
        }

        echo json_encode(['success' => true, 'message' => 'Schedule ' . $action . 'd']);
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

// Webhook endpoint (works with PHP built-in server)
$router->get('/webhook', function () {
    $_GET['provider'] = $_GET['provider'] ?? 'meta';
    require __DIR__ . '/../webhook.php';
});

$router->post('/webhook', function () {
    $_GET['provider'] = $_GET['provider'] ?? 'meta';
    require __DIR__ . '/../webhook.php';
});

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->dispatch($method, $uri);
