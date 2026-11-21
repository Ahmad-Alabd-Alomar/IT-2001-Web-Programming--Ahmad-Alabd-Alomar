<?php

class Logger
{
    private static string $infoLogPath = __DIR__ . '/../logs/info.log';
    private static string $errorLogPath = __DIR__ . '/../logs/error.log';

    public static function info(string $message): void
    {
        self::write(self::$infoLogPath, 'INFO', $message);
    }

    public static function error(string $message): void
    {
        self::write(self::$errorLogPath, 'ERROR', $message);
    }

    private static function write(string $filePath, string $level, string $message): void
    {
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $cleanMessage = trim(preg_replace('/\s+/', ' ', $message));
        $line = "[{$timestamp}] {$level} {$cleanMessage}" . PHP_EOL;

        file_put_contents($filePath, $line, FILE_APPEND | LOCK_EX);
    }
}