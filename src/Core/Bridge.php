<?php

namespace App\Core;

class Bridge
{
    private static string $bridgeUrl = 'http://localhost:3001';

    public static function isRunning(): bool
    {
        try {
            $ch = curl_init(self::$bridgeUrl . '/health');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 3,
                CURLOPT_CONNECTTIMEOUT => 2,
            ]);
            $res = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return $httpCode === 200;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getStatus(): array
    {
        try {
            $ch = curl_init(self::$bridgeUrl . '/status');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_CONNECTTIMEOUT => 2,
            ]);
            $res = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || !$res) {
                return ['running' => false, 'connected' => false, 'qr' => null];
            }

            $data = json_decode($res, true) ?: [];
            return [
                'running' => true,
                'connected' => !empty($data['connected']),
                'qr' => $data['qr'] ?? null,
                'error' => $data['error'] ?? null,
            ];
        } catch (\Exception $e) {
            return ['running' => false, 'connected' => false, 'qr' => null, 'error' => $e->getMessage()];
        }
    }

    public static function send(string $to, string $message): array
    {
        try {
            $ch = curl_init(self::$bridgeUrl . '/send');
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => json_encode(['to' => $to, 'message' => $message]),
            ]);
            $res = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                return ['success' => false, 'error' => 'Bridge returned HTTP ' . $httpCode];
            }

            $data = json_decode($res, true) ?: [];
            return [
                'success' => !empty($data['success']),
                'external_id' => $data['id'] ?? null,
                'error' => $data['error'] ?? null,
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
