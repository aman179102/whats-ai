<?php

namespace App\Webhook;

use App\Core\App;
use App\AI\Factory as AIFactory;

class Handler
{
    public static function handleMeta(): void
    {
        $app = App::getInstance();
        $config = $app->getConfig();
        $db = $app->getDb()->getPdo();

        // Webhook verification (GET request from Meta)
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $mode = $_GET['hub_mode'] ?? '';
            $token = $_GET['hub_verify_token'] ?? '';
            $challenge = $_GET['hub_challenge'] ?? '';

            if ($mode === 'subscribe' && $token === $config['whatsapp']['meta']['verify_token']) {
                echo $challenge;
                return;
            }

            http_response_code(403);
            echo 'Verification failed';
            return;
        }

        // Incoming message (POST)
        $input = json_decode(file_get_contents('php://input'), true);
        $app->getLogger()->info('Meta webhook received', ['payload' => $input]);

        // Verify signature
        $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
        if (!self::verifySignature($input, $signature, $config['whatsapp']['meta']['app_secret'])) {
            $app->getLogger()->warning('Invalid webhook signature');
            http_response_code(200);
            echo 'OK';
            return;
        }

        $entry = $input['entry'][0] ?? [];
        $change = $entry['changes'][0] ?? [];
        $value = $change['value'] ?? [];
        $messages = $value['messages'] ?? [];
        $contacts = $value['contacts'] ?? [];

        foreach ($messages as $msg) {
            if (($msg['type'] ?? '') !== 'text') {
                continue;
            }

            $from = $msg['from'] ?? '';
            $text = $msg['text']['body'] ?? '';
            $msgId = $msg['id'] ?? '';

            if (empty($from) || empty($text)) {
                continue;
            }

            // Save or get contact
            $contactName = $contacts[0]['profile']['name'] ?? 'Unknown';
            $stmt = $db->prepare("INSERT INTO contacts (name, phone, provider) VALUES (:name, :phone, 'meta') ON CONFLICT(phone) DO UPDATE SET name=:name2, updated_at=CURRENT_TIMESTAMP");
            $stmt->execute([
                'name' => $contactName,
                'phone' => $from,
                'name2' => $contactName,
            ]);

            $contactId = $db->lastInsertId() ?: $db->query("SELECT id FROM contacts WHERE phone='$from'")->fetchColumn();

            // Save incoming message
            $stmt = $db->prepare("INSERT INTO messages (contact_id, direction, content, external_id, status) VALUES (:contact_id, 'in', :content, :external_id, 'received')");
            $stmt->execute([
                'contact_id' => $contactId,
                'content' => $text,
                'external_id' => $msgId,
            ]);

            // Auto-reply with AI
            if ($config['ai']['provider'] && !empty($config['ai']['model'])) {
                try {
                    $ai = AIFactory::create($config['ai']['provider'], $config['ai']);
                    $result = $ai->generate($text);

                    if ($result['success']) {
                        $whatsapp = \App\WhatsApp\Factory::create($config['whatsapp']['provider'], $config['whatsapp']);
                        $sendResult = $whatsapp->send($from, $result['content']);

                        $stmt = $db->prepare("INSERT INTO messages (contact_id, direction, content, ai_provider, ai_model, external_id, status) VALUES (:contact_id, 'out', :content, :ai_provider, :ai_model, :external_id, :status)");
                        $stmt->execute([
                            'contact_id' => $contactId,
                            'content' => $result['content'],
                            'ai_provider' => $config['ai']['provider'],
                            'ai_model' => $result['model'] ?? $config['ai']['model'],
                            'external_id' => $sendResult['external_id'] ?? '',
                            'status' => $sendResult['success'] ? 'sent' : 'failed',
                        ]);
                    }
                } catch (\Exception $e) {
                    $app->getLogger()->error('Auto-reply failed', ['error' => $e->getMessage()]);
                }
            }
        }

        http_response_code(200);
        echo 'OK';
    }

    public static function handleTwilio(): void
    {
        $app = App::getInstance();
        $config = $app->getConfig();
        $db = $app->getDb()->getPdo();

        $from = $_POST['From'] ?? '';
        $body = $_POST['Body'] ?? '';
        $msgId = $_POST['SmsSid'] ?? '';

        $from = str_replace('whatsapp:', '', $from);

        if (empty($from) || empty($body)) {
            http_response_code(200);
            echo 'OK';
            return;
        }

        $stmt = $db->prepare("INSERT INTO contacts (name, phone, provider) VALUES (:name, :phone, 'twilio') ON CONFLICT(phone) DO UPDATE SET name=:name2, updated_at=CURRENT_TIMESTAMP");
        $stmt->execute([
            'name' => $from,
            'phone' => $from,
            'name2' => $from,
        ]);

        $contactId = $db->lastInsertId() ?: $db->query("SELECT id FROM contacts WHERE phone='$from'")->fetchColumn();

        $stmt = $db->prepare("INSERT INTO messages (contact_id, direction, content, external_id, status) VALUES (:contact_id, 'in', :content, :external_id, 'received')");
        $stmt->execute([
            'contact_id' => $contactId,
            'content' => $body,
            'external_id' => $msgId,
        ]);

        if ($config['ai']['provider'] && !empty($config['ai']['model'])) {
            try {
                $ai = AIFactory::create($config['ai']['provider'], $config['ai']);
                $result = $ai->generate($body);

                if ($result['success']) {
                    $whatsapp = \App\WhatsApp\Factory::create($config['whatsapp']['provider'], $config['whatsapp']);
                    $sendResult = $whatsapp->send($from, $result['content']);

                    $stmt = $db->prepare("INSERT INTO messages (contact_id, direction, content, ai_provider, ai_model, external_id, status) VALUES (:contact_id, 'out', :content, :ai_provider, :ai_model, :external_id, :status)");
                    $stmt->execute([
                        'contact_id' => $contactId,
                        'content' => $result['content'],
                        'ai_provider' => $config['ai']['provider'],
                        'ai_model' => $result['model'] ?? $config['ai']['model'],
                        'external_id' => $sendResult['external_id'] ?? '',
                        'status' => $sendResult['success'] ? 'sent' : 'failed',
                    ]);
                }
            } catch (\Exception $e) {
                $app->getLogger()->error('Auto-reply failed', ['error' => $e->getMessage()]);
            }
        }

        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?><Response></Response>';
    }

    private static function verifySignature(array $payload, string $signature, string $appSecret): bool
    {
        if (empty($appSecret) || empty($signature)) {
            return true; // Skip verification if not configured
        }

        $expected = 'sha256=' . hash_hmac('sha256', json_encode($payload), $appSecret);
        return hash_equals($expected, $signature);
    }
}
