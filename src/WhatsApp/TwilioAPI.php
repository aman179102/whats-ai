<?php

namespace App\WhatsApp;

use Twilio\Rest\Client;

class TwilioAPI implements ProviderInterface
{
    private Client $client;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        if (!empty($config['account_sid']) && !empty($config['auth_token'])) {
            $this->client = new Client($config['account_sid'], $config['auth_token']);
        }
    }

    public function send(string $to, string $message): array
    {
        try {
            $from = "whatsapp:{$this->config['from_number']}";
            $to = "whatsapp:$to";

            $msg = $this->client->messages->create($to, [
                'from' => $from,
                'body' => $message,
            ]);

            return [
                'success' => true,
                'external_id' => $msg->sid,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getName(): string
    {
        return 'Twilio API';
    }

    public function isAvailable(): bool
    {
        return !empty($this->config['account_sid'])
            && !empty($this->config['auth_token'])
            && !empty($this->config['from_number']);
    }

    public function getWarning(): ?string
    {
        return null;
    }
}
