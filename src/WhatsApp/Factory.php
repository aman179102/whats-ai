<?php

namespace App\WhatsApp;

class Factory
{
    public static function create(string $provider, array $config): ProviderInterface
    {
        return match ($provider) {
            'meta' => new MetaCloudAPI($config['meta']),
            'twilio' => new TwilioAPI($config['twilio']),
            'webscraper' => new WebScraper($config['webscraper']),
            default => throw new \InvalidArgumentException("Unknown WhatsApp provider: $provider"),
        };
    }

    public static function getAvailableProviders(): array
    {
        return [
            'meta' => [
                'name' => 'Meta Cloud API',
                'official' => true,
                'warning' => null,
                'description' => 'Official WhatsApp Business API by Meta. Free tier available.',
            ],
            'twilio' => [
                'name' => 'Twilio API',
                'official' => true,
                'warning' => null,
                'description' => 'Twilio WhatsApp API. Paid service, per-message charges.',
            ],
            'webscraper' => [
                'name' => 'Web Scraper (Unofficial)',
                'official' => false,
                'warning' => '⚠️ WARNING: This method is UNOFFICIAL and violates WhatsApp\'s Terms of Service. Your phone number can be PERMANENTLY BANNED. WhatsApp may also block your device. Use at your own risk. This method relies on reverse-engineering WhatsApp Web and may break at any time without notice.',
                'description' => 'Unofficial method using WhatsApp Web automation. Free but risky.',
            ],
        ];
    }
}
