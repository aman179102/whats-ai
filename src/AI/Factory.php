<?php

namespace App\AI;

class Factory
{
    public static function create(string $provider, array $config): ProviderInterface
    {
        $model = $config['model'] ?? 'gpt-3.5-turbo';
        $temperature = $config['temperature'] ?? 0.7;
        $maxTokens = $config['max_tokens'] ?? 1024;
        $systemPrompt = $config['system_prompt'] ?? '';

        return match ($provider) {
            'openrouter' => new OpenRouterProvider(
                $config['openrouter']['api_key'] ?? '',
                $config['openrouter']['base_url'] ?? 'https://openrouter.ai/api/v1',
                $model,
                $temperature,
                $maxTokens,
                $systemPrompt
            ),
            'groq' => new GroqProvider(
                $config['groq']['api_key'] ?? '',
                $config['groq']['base_url'] ?? 'https://api.groq.com/openai/v1',
                $model,
                $temperature,
                $maxTokens,
                $systemPrompt
            ),
            'gemini' => new GeminiProvider(
                $config['gemini']['api_key'] ?? '',
                $config['gemini']['base_url'] ?? 'https://generativelanguage.googleapis.com/v1beta',
                $model,
                $temperature,
                $maxTokens,
                $systemPrompt
            ),
            'custom' => new CustomProvider(
                $config['custom']['api_key'] ?? '',
                $config['custom']['base_url'] ?? '',
                $config['custom']['model'] ?: $model,
                $temperature,
                $maxTokens,
                $systemPrompt
            ),
            default => throw new \InvalidArgumentException("Unknown AI provider: $provider"),
        };
    }

    public static function getAvailableProviders(): array
    {
        return [
            'openrouter' => [
                'name' => 'OpenRouter',
                'description' => '200+ models including GPT-4, Claude, Llama, Mistral. Pay-as-you-go.',
                'website' => 'https://openrouter.ai',
                'models' => ['gpt-4o', 'gpt-4o-mini', 'claude-3.5-sonnet', 'llama-3.1-70b', 'mistral-large'],
            ],
            'groq' => [
                'name' => 'Groq',
                'description' => 'Fast inference with Llama 3, Mixtral, Gemma. Free tier available.',
                'website' => 'https://console.groq.com',
                'models' => ['llama-3.3-70b-versatile', 'llama-3.1-8b-instant', 'mixtral-8x7b-32768', 'gemma2-9b-it'],
            ],
            'gemini' => [
                'name' => 'Google Gemini',
                'description' => 'Google\'s Gemini models. Free tier available via API key.',
                'website' => 'https://aistudio.google.com',
                'models' => ['gemini-2.0-flash', 'gemini-1.5-flash', 'gemini-1.5-pro'],
            ],
            'custom' => [
                'name' => 'Custom API',
                'description' => 'Any OpenAI-compatible API endpoint. Bring your own.',
                'website' => null,
                'models' => ['custom'],
            ],
        ];
    }
}
