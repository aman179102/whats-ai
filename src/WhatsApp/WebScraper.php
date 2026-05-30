<?php

namespace App\WhatsApp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Core\Bridge;

class WebScraper implements ProviderInterface
{
    private Client $client;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'timeout' => 30,
        ]);
    }

    public function send(string $to, string $message): array
    {
        // Try local bridge first (WhatsApp Web via Node.js)
        if (Bridge::isRunning()) {
            return Bridge::send($to, $message);
        }

        // Fallback to WAHA/remote API
        try {
            $response = $this->client->post($this->config['api_url'] . '/send', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config['api_key'],
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'to' => $to,
                    'message' => $message,
                    'session' => $this->config['session_path'],
                ],
            ]);

            $body = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'external_id' => $body['id'] ?? null,
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getName(): string
    {
        return 'WhatsApp Web (Bridge)';
    }

    public function isAvailable(): bool
    {
        return Bridge::isRunning() || (!empty($this->config['api_url']) && !empty($this->config['api_key']));
    }

    public function getWarning(): ?string
    {
        return '⚠️ Unofficial method. WhatsApp may ban your account. Use at your own risk.';
    }
}
