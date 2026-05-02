<?php

return [
    'app' => [
        'name' => $_ENV['APP_NAME'] ?? 'WhatsApp AI Automation',
        'env' => $_ENV['APP_ENV'] ?? 'development',
        'debug' => (bool)($_ENV['APP_DEBUG'] ?? true),
    ],
    'db' => [
        'path' => __DIR__ . '/../' . ($_ENV['DB_PATH'] ?? 'storage/database.sqlite'),
    ],
    'whatsapp' => [
        'provider' => $_ENV['WHATSAPP_PROVIDER'] ?? 'meta',
        'meta' => [
            'phone_number_id' => $_ENV['META_PHONE_NUMBER_ID'] ?? '',
            'access_token' => $_ENV['META_ACCESS_TOKEN'] ?? '',
            'app_secret' => $_ENV['META_APP_SECRET'] ?? '',
            'verify_token' => $_ENV['META_VERIFY_TOKEN'] ?? '',
            'api_version' => $_ENV['META_API_VERSION'] ?? 'v21.0',
        ],
        'twilio' => [
            'account_sid' => $_ENV['TWILIO_ACCOUNT_SID'] ?? '',
            'auth_token' => $_ENV['TWILIO_AUTH_TOKEN'] ?? '',
            'from_number' => $_ENV['TWILIO_FROM_NUMBER'] ?? '',
        ],
        'webscraper' => [
            'session_path' => $_ENV['WEB_SCRAPER_SESSION_PATH'] ?? '',
            'api_url' => $_ENV['WEB_SCRAPER_API_URL'] ?? '',
            'api_key' => $_ENV['WEB_SCRAPER_API_KEY'] ?? '',
        ],
    ],
    'ai' => [
        'provider' => $_ENV['AI_PROVIDER'] ?? 'openrouter',
        'model' => $_ENV['AI_MODEL'] ?? 'gpt-3.5-turbo',
        'temperature' => (float)($_ENV['AI_TEMPERATURE'] ?? 0.7),
        'max_tokens' => (int)($_ENV['AI_MAX_TOKENS'] ?? 1024),
        'system_prompt' => $_ENV['AI_SYSTEM_PROMPT'] ?? 'You are a helpful WhatsApp assistant.',
        'openrouter' => [
            'api_key' => $_ENV['OPENROUTER_API_KEY'] ?? '',
            'base_url' => $_ENV['OPENROUTER_BASE_URL'] ?? 'https://openrouter.ai/api/v1',
        ],
        'groq' => [
            'api_key' => $_ENV['GROQ_API_KEY'] ?? '',
            'base_url' => $_ENV['GROQ_BASE_URL'] ?? 'https://api.groq.com/openai/v1',
        ],
        'gemini' => [
            'api_key' => $_ENV['GEMINI_API_KEY'] ?? '',
            'base_url' => $_ENV['GEMINI_BASE_URL'] ?? 'https://generativelanguage.googleapis.com/v1beta',
        ],
        'custom' => [
            'api_key' => $_ENV['CUSTOM_API_KEY'] ?? '',
            'base_url' => $_ENV['CUSTOM_BASE_URL'] ?? '',
            'model' => $_ENV['CUSTOM_MODEL'] ?? '',
        ],
    ],
    'scheduler' => [
        'check_interval' => (int)($_ENV['SCHEDULER_CHECK_INTERVAL'] ?? 1),
    ],
];
