<?php

namespace App\Guide;

class SetupGuide
{
    public static function getWhatsAppGuides(): array
    {
        return [
            'meta' => [
                'title' => 'Meta Cloud API Setup',
                'steps' => [
                    [
                        'step' => 1,
                        'title' => 'Meta Developer Account',
                        'description' => 'Visit developers.facebook.com and create a Meta Business Account if you don\'t have one.',
                        'link' => 'https://developers.facebook.com/',
                    ],
                    [
                        'step' => 2,
                        'title' => 'Create WhatsApp App',
                        'description' => 'Go to Meta Developer Portal → My Apps → Create App → Choose "Business" type → Add "WhatsApp" product.',
                        'link' => 'https://developers.facebook.com/apps/',
                    ],
                    [
                        'step' => 3,
                        'title' => 'Get Access Token',
                        'description' => 'In your app, go to WhatsApp → Configuration → Copy the "Temporary Access Token" (valid for 24 hours). For permanent token, create a System User in Meta Business Settings.',
                        'link' => 'https://business.facebook.com/settings/system-users',
                    ],
                    [
                        'step' => 4,
                        'title' => 'Get Phone Number ID',
                        'description' => 'In WhatsApp → Configuration → From Phone Numbers → Copy the Phone Number ID and also register a WhatsApp Business number.',
                    ],
                    [
                        'step' => 5,
                        'title' => 'Set Verify Token',
                        'description' => 'Choose any random string as your verify token (e.g., "my_verify_token_123"). Add it to your .env file.',
                    ],
                    [
                        'step' => 6,
                        'title' => 'Configure Webhook',
                        'description' => 'In WhatsApp → Configuration → Webhook → Set Callback URL to: https://your-domain.com/webhook.php?provider=meta. Set Verify Token to the one you chose. Subscribe to: messages, message_deliveries, message_reads.',
                    ],
                    [
                        'step' => 7,
                        'title' => 'Test Message',
                        'description' => 'Send a message to your registered WhatsApp number. The system should auto-reply if AI is configured.',
                    ],
                ],
            ],
            'twilio' => [
                'title' => 'Twilio API Setup',
                'steps' => [
                    [
                        'step' => 1,
                        'title' => 'Twilio Account',
                        'description' => 'Sign up at twilio.com and get a free trial account.',
                        'link' => 'https://www.twilio.com/try-twilio',
                    ],
                    [
                        'step' => 2,
                        'title' => 'Get WhatsApp Number',
                        'description' => 'In Twilio Console → Phone Numbers → Get a phone number that supports WhatsApp (or enable WhatsApp Sandbox).',
                        'link' => 'https://console.twilio.com/',
                    ],
                    [
                        'step' => 3,
                        'title' => 'Get Credentials',
                        'description' => 'Go to Console → Account → API Keys & Tokens. Copy Account SID and Auth Token.',
                    ],
                    [
                        'step' => 4,
                        'title' => 'Configure Webhook',
                        'description' => 'In WhatsApp Sandbox → When a message comes in → Set URL to: https://your-domain.com/webhook.php?provider=twilio.',
                    ],
                    [
                        'step' => 5,
                        'title' => 'Sandbox Invite',
                        'description' => 'Send the sandbox invite code to your WhatsApp number to join the sandbox.',
                    ],
                ],
            ],
            'webscraper' => [
                'title' => 'Web Scraper (Unofficial) - ⚠️ WARNING',
                'warning' => true,
                'steps' => [
                    [
                        'step' => 1,
                        'title' => '⚠️ Understand the Risks',
                        'description' => 'This method violates WhatsApp\'s Terms of Service. Your number CAN be permanently banned. This is for educational purposes only.',
                    ],
                    [
                        'step' => 2,
                        'title' => 'Setup WhatsApp HTTP API',
                        'description' => 'You need to run a WhatsApp bridge service like WAHA (WhatsApp HTTP API) or Evolution API on your server. These use the Baileys library (WhatsApp Web reverse-engineered).',
                        'link' => 'https://waha.devlike.pro/',
                    ],
                    [
                        'step' => 3,
                        'title' => 'Scan QR Code',
                        'description' => 'After starting WAHA/Evolution API, visit the dashboard and scan the QR code with your WhatsApp to link your session.',
                    ],
                    [
                        'step' => 4,
                        'title' => 'Get API URL & Key',
                        'description' => 'Copy the API URL (e.g., http://localhost:3000) and API key from the WAHA dashboard.',
                    ],
                    [
                        'step' => 5,
                        'title' => 'Configure in App',
                        'description' => 'Set the API URL, API Key, and Session Path in the settings.',
                    ],
                ],
            ],
        ];
    }

    public static function getAIGuides(): array
    {
        return [
            'openrouter' => [
                'title' => 'OpenRouter Setup',
                'steps' => [
                    [
                        'step' => 1,
                        'title' => 'Create Account',
                        'description' => 'Visit openrouter.ai and sign up for a free account.',
                        'link' => 'https://openrouter.ai/',
                    ],
                    [
                        'step' => 2,
                        'title' => 'Get API Key',
                        'description' => 'Go to Keys section and click "Create Key". Copy the key and add to .env as OPENROUTER_API_KEY.',
                        'link' => 'https://openrouter.ai/keys',
                    ],
                    [
                        'step' => 3,
                        'title' => 'Choose a Model',
                        'description' => 'You can use any model: gpt-4o, claude-3.5-sonnet, llama-3.1-70b, etc. Add some credits (minimum $1) to get started.',
                    ],
                    [
                        'step' => 4,
                        'title' => 'Configure in App',
                        'description' => 'Set AI_PROVIDER=openrouter and AI_MODEL to your chosen model name.',
                    ],
                ],
            ],
            'groq' => [
                'title' => 'Groq Setup',
                'steps' => [
                    [
                        'step' => 1,
                        'title' => 'Create Account',
                        'description' => 'Visit console.groq.com and sign up. Groq offers free API credits.',
                        'link' => 'https://console.groq.com/',
                    ],
                    [
                        'step' => 2,
                        'title' => 'Get API Key',
                        'description' => 'Go to API Keys section and create a new key. Copy it to .env as GROQ_API_KEY.',
                        'link' => 'https://console.groq.com/keys',
                    ],
                    [
                        'step' => 3,
                        'title' => 'Choose Model',
                        'description' => 'Popular free models: llama-3.3-70b-versatile, llama-3.1-8b-instant, mixtral-8x7b-32768.',
                    ],
                    [
                        'step' => 4,
                        'title' => 'Configure in App',
                        'description' => 'Set AI_PROVIDER=groq and AI_MODEL to your chosen model.',
                    ],
                ],
            ],
            'gemini' => [
                'title' => 'Google Gemini Setup',
                'steps' => [
                    [
                        'step' => 1,
                        'title' => 'Get API Key',
                        'description' => 'Visit aistudio.google.com and click "Get API Key". Google provides a generous free tier.',
                        'link' => 'https://aistudio.google.com/',
                    ],
                    [
                        'step' => 2,
                        'title' => 'Copy API Key',
                        'description' => 'Create a new API key and copy it to .env as GEMINI_API_KEY.',
                    ],
                    [
                        'step' => 3,
                        'title' => 'Choose Model',
                        'description' => 'Free models: gemini-2.0-flash (fast), gemini-1.5-flash, gemini-1.5-pro. Model names may update, you can enter any model name.',
                    ],
                    [
                        'step' => 4,
                        'title' => 'Configure in App',
                        'description' => 'Set AI_PROVIDER=gemini and AI_MODEL to your chosen model name.',
                    ],
                ],
            ],
            'custom' => [
                'title' => 'Custom API Setup',
                'steps' => [
                    [
                        'step' => 1,
                        'title' => 'Get API Endpoint',
                        'description' => 'You need an OpenAI-compatible API endpoint (e.g., OpenAI, Together AI, DeepSeek, a local Ollama server, etc.).',
                    ],
                    [
                        'step' => 2,
                        'title' => 'Get Credentials',
                        'description' => 'Copy the API Base URL (e.g., https://api.openai.com/v1) and API Key.',
                    ],
                    [
                        'step' => 3,
                        'title' => 'Choose Model',
                        'description' => 'Enter the model name supported by your API endpoint.',
                    ],
                    [
                        'step' => 4,
                        'title' => 'Configure in App',
                        'description' => 'Set AI_PROVIDER=custom, CUSTOM_BASE_URL, CUSTOM_API_KEY, and CUSTOM_MODEL.',
                    ],
                ],
            ],
        ];
    }

    public static function getHostingGuide(): array
    {
        return [
            'title' => 'Free Hosting Setup',
            'steps' => [
                [
                    'step' => 1,
                    'title' => 'Option A: Render.com (Recommended)',
                    'description' => 'Create account at render.com. Create a new "Web Service". Connect your GitHub repo. Build command: "composer install". Start command: "php -S 0.0.0.0:10000 -t public". Free tier includes SSL and custom domain support.',
                    'link' => 'https://render.com/',
                ],
                [
                    'step' => 2,
                    'title' => 'Option B: Railway.app',
                    'description' => 'Create account at railway.app. Create new project from GitHub repo. Build: "composer install". Start: "php -S 0.0.0.0:$PORT -t public". Includes free SSL.',
                    'link' => 'https://railway.app/',
                ],
                [
                    'step' => 3,
                    'title' => 'Database on Free Hosting',
                    'description' => 'This app uses SQLite (file-based). The database file is stored in storage/database.sqlite. Make sure the storage directory is writable. On Render/Railway, persistent disks are available.',
                ],
                [
                    'step' => 4,
                    'title' => 'Setup Cron Jobs (Free)',
                    'description' => 'Go to cron-job.org and create a free account. Create a new cron job: URL: https://your-app.onrender.com/cron/schedule-runner.php. Set interval: Every 5 minutes. This runs the scheduled message processor.',
                    'link' => 'https://cron-job.org/',
                ],
                [
                    'step' => 5,
                    'title' => 'Webhook Requirements',
                    'description' => 'Meta & Twilio require HTTPS webhooks. Render/Railway provide free SSL automatically. Your webhook URL will be: https://your-app.onrender.com/webhook.php',
                ],
                [
                    'step' => 6,
                    'title' => 'Environment Variables',
                    'description' => 'On Render/Railway, set all .env variables in the Environment section of your service dashboard. Do NOT commit .env file to Git.',
                ],
            ],
        ];
    }
}
