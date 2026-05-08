<?php

namespace App\WhatsApp;

interface ProviderInterface
{
    public function send(string $to, string $message): array;
    public function getName(): string;
    public function isAvailable(): bool;
    public function getWarning(): ?string;
}
