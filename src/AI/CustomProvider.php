<?php

namespace App\AI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CustomProvider implements ProviderInterface
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl;
    private string $model;
    private float $temperature;
    private int $maxTokens;
    private string $systemPrompt;

    public function __construct(
        string $apiKey,
        string $baseUrl,
        string $model,
        float $temperature,
        int $maxTokens,
        string $systemPrompt
    ) {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
        $this->model = $model;
        $this->temperature = $temperature;
        $this->maxTokens = $maxTokens;
        $this->systemPrompt = $systemPrompt;

        $this->client = new Client([
            'base_uri' => rtrim($baseUrl, '/') . '/',
            'timeout' => 60,
        ]);
    }

    public function generate(string $prompt, string $systemPrompt = null, array $params = []): array
    {
        try {
            $messages = [];

            $sp = $systemPrompt ?: $this->systemPrompt;
            if (!empty($sp)) {
                $messages[] = ['role' => 'system', 'content' => $sp];
            }

            $messages[] = ['role' => 'user', 'content' => $prompt];

            $response = $this->client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $params['model'] ?? $this->model,
                    'messages' => $messages,
                    'temperature' => $params['temperature'] ?? $this->temperature,
                    'max_tokens' => $params['max_tokens'] ?? $this->maxTokens,
                ],
            ]);

            $body = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'content' => $body['choices'][0]['message']['content'] ?? '',
                'model' => $body['model'] ?? $this->model,
                'usage' => $body['usage'] ?? [],
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
        return 'Custom API';
    }

    public function getAvailableModels(): array
    {
        // Custom provider - user can input any model name
        return [$this->model];
    }

    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey) && !empty($this->baseUrl);
    }
}
