<?php

namespace App\Scheduler;

use App\Core\App;
use App\WhatsApp\Factory as WhatsAppFactory;
use App\AI\Factory as AIFactory;

class Scheduler
{
    public static function process(): void
    {
        $app = App::getInstance();
        $config = $app->getConfig();
        $db = $app->getDb()->getPdo();
        $logger = $app->getLogger();

        $logger->info('Scheduler: Checking for due tasks...');

        $now = date('Y-m-d H:i:00');

        $stmt = $db->prepare("
            SELECT s.*, sc.id as sc_id, sc.contact_id, sc.status as sc_status, c.phone, c.name as contact_name
            FROM schedules s
            JOIN schedule_contacts sc ON sc.schedule_id = s.id
            JOIN contacts c ON c.id = sc.contact_id
            WHERE s.status = 'active'
            AND sc.status = 'pending'
            AND s.scheduled_at <= :now
        ");
        $stmt->execute(['now' => $now]);
        $tasks = $stmt->fetchAll();

        if (empty($tasks)) {
            $logger->info('Scheduler: No due tasks found.');
            return;
        }

        $whatsapp = WhatsAppFactory::create($config['whatsapp']['provider'], $config['whatsapp']);
        $ai = null;
        if ($config['ai']['provider']) {
            $ai = AIFactory::create($config['ai']['provider'], $config['ai']);
        }

        foreach ($tasks as $task) {
            $logger->info("Scheduler: Processing task #{$task['sc_id']}", [
                'schedule' => $task['name'],
                'contact' => $task['contact_name'],
                'phone' => $task['phone'],
            ]);

            $message = $task['message_template'];

            // AI generate message if enabled
            if ($task['ai_generated'] && $ai) {
                $prompt = $task['ai_prompt'] ?: "Write a WhatsApp message for {$task['contact_name']}";
                $result = $ai->generate($prompt);

                if ($result['success']) {
                    $message = $result['content'];

                    $db->prepare("UPDATE schedule_contacts SET error = :error WHERE id = :id")->execute([
                        'error' => json_encode(['ai_model' => $result['model'] ?? '']),
                        'id' => $task['sc_id'],
                    ]);
                } else {
                    $db->prepare("UPDATE schedule_contacts SET status = 'failed', error = :error WHERE id = :id")->execute([
                        'error' => 'AI generation failed: ' . ($result['error'] ?? ''),
                        'id' => $task['sc_id'],
                    ]);
                    continue;
                }
            }

            if (empty($message)) {
                $db->prepare("UPDATE schedule_contacts SET status = 'failed', error = 'Empty message' WHERE id = :id")->execute([
                    'id' => $task['sc_id'],
                ]);
                continue;
            }

            // Send message
            $sendResult = $whatsapp->send($task['phone'], $message);

            if ($sendResult['success']) {
                $db->prepare("UPDATE schedule_contacts SET status = 'sent', sent_at = CURRENT_TIMESTAMP WHERE id = :id")->execute([
                    'id' => $task['sc_id'],
                ]);

                // Save message record
                $stmt2 = $db->prepare("INSERT INTO messages (contact_id, direction, content, ai_provider, ai_model, external_id, status) VALUES (:contact_id, 'out', :content, :ai_provider, :ai_model, :external_id, 'sent')");
                $stmt2->execute([
                    'contact_id' => $task['contact_id'],
                    'content' => $message,
                    'ai_provider' => $task['ai_generated'] ? $config['ai']['provider'] : null,
                    'ai_model' => $task['ai_generated'] ? ($config['ai']['model'] ?? null) : null,
                    'external_id' => $sendResult['external_id'] ?? '',
                ]);

                $logger->info("Scheduler: Message sent to {$task['contact_name']}");
            } else {
                $db->prepare("UPDATE schedule_contacts SET status = 'failed', error = :error WHERE id = :id")->execute([
                    'error' => $sendResult['error'] ?? 'Send failed',
                    'id' => $task['sc_id'],
                ]);

                $logger->error("Scheduler: Failed to send to {$task['contact_name']}", [
                    'error' => $sendResult['error'] ?? 'Unknown',
                ]);
            }
        }

        // Handle recurring schedules
        $stmt = $db->prepare("
            SELECT DISTINCT s.id, s.repeat_type, s.scheduled_at
            FROM schedules s
            JOIN schedule_contacts sc ON sc.schedule_id = s.id
            WHERE s.status = 'active'
            AND s.repeat_type != 'once'
            AND sc.status = 'sent'
        ");
        $stmt->execute();
        $recurring = $stmt->fetchAll();

        foreach ($recurring as $schedule) {
            $nextDate = self::getNextRun($schedule['scheduled_at'], $schedule['repeat_type']);
            if ($nextDate) {
                $db->prepare("UPDATE schedule_contacts SET status = 'pending', sent_at = NULL WHERE schedule_id = :id AND status = 'sent'")->execute([
                    'id' => $schedule['id'],
                ]);
            }
        }

        $logger->info('Scheduler: Completed.');
    }

    private static function getNextRun(string $currentDate, string $repeatType): ?string
    {
        $date = new \DateTime($currentDate);

        return match ($repeatType) {
            'daily' => $date->modify('+1 day')->format('Y-m-d H:i:s'),
            'weekly' => $date->modify('+1 week')->format('Y-m-d H:i:s'),
            'monthly' => $date->modify('+1 month')->format('Y-m-d H:i:s'),
            default => null,
        };
    }
}
