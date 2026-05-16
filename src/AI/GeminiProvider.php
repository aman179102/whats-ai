<?php

namespace App\AI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GeminiProvider implements ProviderInterface
{
    private Client $client;
    private string $apiKey;
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
            $model = $params['model'] ?? $this->model;

            $contents = [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ];

            $requestBody = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => $params['temperature'] ?? $this->temperature,
                    'maxOutputTokens' => $params['max_tokens'] ?? $this->maxTokens,
                ],
            ];

            $sp = $systemPrompt ?: $this->systemPrompt;
            if (!empty($sp)) {
                $requestBody['systemInstruction'] = [
                    'parts' => [['text' => $sp]]
                ];
            }

            $response = $this->client->post("models/{$model}:generateContent", [
                'query' => ['key' => $this->apiKey],
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $requestBody,
            ]);

            $body = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'content' => $body['candidates'][0]['content']['parts'][0]['text'] ?? '',
                'model' => $model,
                'usage' => $body['usageMetadata'] ?? [],
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
        return 'Google Gemini';
    }

    public function getAvailableModels(): array
    {
        return [
            'gemini-2.0-flash',
            'gemini-2.0-flash-lite',
            'gemini-1.5-flash',
            'gemini-1.5-flash-8b',
            'gemini-1.5-pro',
        ];
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
        return !empty($this->apiKey);
    }
}
