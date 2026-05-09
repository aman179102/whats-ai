<?php

namespace App\WhatsApp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class MetaCloudAPI implements ProviderInterface
{
    private Client $client;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => "https://graph.facebook.com/{$config['api_version']}/",
            'timeout' => 30,
        ]);
    }

    public function send(string $to, string $message): array
    {
        try {
            $response = $this->client->post("{$this->config['phone_number_id']}/messages", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config['access_token'],
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'text',
                    'text' => ['body' => $message],
                ],
            ]);

            $body = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'external_id' => $body['messages'][0]['id'] ?? null,
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
        return 'Meta Cloud API';
    }

    public function isAvailable(): bool
    {
        return !empty($this->config['phone_number_id']) && !empty($this->config['access_token']);
    }

    public function getWarning(): ?string
    {
        return null;
    }
}
