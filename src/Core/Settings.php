<?php

namespace App\Core;

class Settings
{
    public static function get(string $key, $default = null): ?string
    {
        try {
            $db = Database::getInstance()->getPdo();
            $stmt = $db->prepare("SELECT value FROM settings WHERE key = ?");
            $stmt->execute([$key]);
            $row = $stmt->fetch();
            return $row ? $row['value'] : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    public static function set(string $key, string $value): void
    {
        try {
            $db = Database::getInstance()->getPdo();
            $stmt = $db->prepare("INSERT INTO settings (key, value, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP) ON CONFLICT(key) DO UPDATE SET value = ?, updated_at = CURRENT_TIMESTAMP");
            $stmt->execute([$key, $value, $value]);
        } catch (\Exception $e) {
            // Silently fail - DB might not be ready
        }
    }

    public static function getAll(): array
    {
        try {
            $db = Database::getInstance()->getPdo();
            $rows = $db->query("SELECT key, value FROM settings")->fetchAll();
            $result = [];
            foreach ($rows as $row) {
                $result[$row['key']] = $row['value'];
            }
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    // Load settings from DB and merge with env (env takes priority)
    public static function loadIntoConfig(array $config): array
    {
        $dbSettings = self::getAll();

        if (!empty($dbSettings['whatsapp_provider'])) {
            $config['whatsapp']['provider'] = $dbSettings['whatsapp_provider'];
        }
        if (!empty($dbSettings['meta_phone_number_id'])) {
            $config['whatsapp']['meta']['phone_number_id'] = $dbSettings['meta_phone_number_id'];
        }
        if (!empty($dbSettings['meta_access_token'])) {
            $config['whatsapp']['meta']['access_token'] = $dbSettings['meta_access_token'];
        }
        if (!empty($dbSettings['meta_app_secret'])) {
            $config['whatsapp']['meta']['app_secret'] = $dbSettings['meta_app_secret'];
        }
        if (!empty($dbSettings['meta_verify_token'])) {
            $config['whatsapp']['meta']['verify_token'] = $dbSettings['meta_verify_token'];
        }
        if (!empty($dbSettings['twilio_account_sid'])) {
            $config['whatsapp']['twilio']['account_sid'] = $dbSettings['twilio_account_sid'];
        }
        if (!empty($dbSettings['twilio_auth_token'])) {
            $config['whatsapp']['twilio']['auth_token'] = $dbSettings['twilio_auth_token'];
        }
        if (!empty($dbSettings['twilio_from_number'])) {
            $config['whatsapp']['twilio']['from_number'] = $dbSettings['twilio_from_number'];
        }
        if (!empty($dbSettings['web_scraper_api_url'])) {
            $config['whatsapp']['webscraper']['api_url'] = $dbSettings['web_scraper_api_url'];
        }
        if (!empty($dbSettings['web_scraper_api_key'])) {
            $config['whatsapp']['webscraper']['api_key'] = $dbSettings['web_scraper_api_key'];
        }
        if (!empty($dbSettings['ai_provider'])) {
            $config['ai']['provider'] = $dbSettings['ai_provider'];
        }
        if (!empty($dbSettings['ai_model'])) {
            $config['ai']['model'] = $dbSettings['ai_model'];
        }
        if (!empty($dbSettings['ai_temperature'])) {
            $config['ai']['temperature'] = (float)$dbSettings['ai_temperature'];
        }
        if (!empty($dbSettings['ai_max_tokens'])) {
            $config['ai']['max_tokens'] = (int)$dbSettings['ai_max_tokens'];
        }
        if (!empty($dbSettings['ai_system_prompt'])) {
            $config['ai']['system_prompt'] = $dbSettings['ai_system_prompt'];
        }
        if (!empty($dbSettings['openrouter_api_key'])) {
            $config['ai']['openrouter']['api_key'] = $dbSettings['openrouter_api_key'];
        }
        if (!empty($dbSettings['groq_api_key'])) {
            $config['ai']['groq']['api_key'] = $dbSettings['groq_api_key'];
        }
        if (!empty($dbSettings['gemini_api_key'])) {
            $config['ai']['gemini']['api_key'] = $dbSettings['gemini_api_key'];
        }
        if (!empty($dbSettings['custom_api_key'])) {
            $config['ai']['custom']['api_key'] = $dbSettings['custom_api_key'];
        }
        if (!empty($dbSettings['custom_base_url'])) {
            $config['ai']['custom']['base_url'] = $dbSettings['custom_base_url'];
        }
        if (!empty($dbSettings['custom_model'])) {
            $config['ai']['custom']['model'] = $dbSettings['custom_model'];
        }

        return $config;
    }
}
