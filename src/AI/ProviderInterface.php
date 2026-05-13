<?php

namespace App\AI;

interface ProviderInterface
{
    public function generate(string $prompt, string $systemPrompt = null, array $params = []): array;
    public function getName(): string;
    public function getAvailableModels(): array;
    public function setModel(string $model): void;
    public function getModel(): string;
    public function isAvailable(): bool;
}
