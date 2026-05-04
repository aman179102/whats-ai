<?php

namespace App\Core;

class App
{
    private static ?App $instance = null;
    private array $config;
    private Database $db;
    private Logger $logger;
    private array $providers = [];

    private function __construct(array $config)
    {
        $this->config = $config;
        $this->logger = new Logger(
            __DIR__ . '/../../storage/logs',
            $config['app']['debug'] ?? true
        );
        Database::init($config['db']);
        $this->db = Database::getInstance();
    }

    public static function init(array $config): self
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            throw new \RuntimeException('App not initialized.');
        }
        return self::$instance;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getDb(): Database
    {
        return $this->db;
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    public function run(): void
    {
        $this->db->getPdo(); // ensure connection
        $this->logger->info('App started');
    }
}
