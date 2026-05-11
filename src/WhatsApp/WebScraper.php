<?php

namespace App\WhatsApp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class WebScraper implements ProviderInterface
{
    private Client $client;
    private array $config;

    private const WARNING = '⚠️ WARNING: This method is UNOFFICIAL and violates WhatsApp\'s Terms of Service. Your phone number can be PERMANENTLY BANNED. WhatsApp may also block your device. Use at your own risk. This method relies on reverse-engineering WhatsApp Web and may break at any time without notice.';

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'timeout' => 30,
        ]);
    }

    public function send(string $to, string $message): array
    {
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
        return 'Web Scraper (Unofficial)';
    }

    public function isAvailable(): bool
    {
        return !empty($this->config['api_url']) && !empty($this->config['api_key']);
    }

    public function getWarning(): ?string
    {
        return self::WARNING;
    }
}
