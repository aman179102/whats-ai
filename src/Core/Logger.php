<?php

namespace App\Core;

class Logger
{
    private string $logDir;
    private bool $debug;

    public function __construct(string $logDir = null, bool $debug = true)
    {
        $this->logDir = $logDir ?? __DIR__ . '/../../storage/logs';
        $this->debug = $debug;

        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0777, true);
        }
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        if ($this->debug) {
            $this->log('DEBUG', $message, $context);
        }
    }

    private function log(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logLine = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;

        $filename = $this->logDir . '/app-' . date('Y-m-d') . '.log';
        file_put_contents($filename, $logLine, FILE_APPEND | LOCK_EX);
    }
}
