<?php

namespace Tigrino\Core\Errors;

use Throwable;

class ErrorHandler
{
    private static string $logDir;

    public function __construct()
    {
        $baseDir = dirname(__DIR__, 3);
        self::$logDir = $baseDir . DIRECTORY_SEPARATOR . "Logs" . DIRECTORY_SEPARATOR;
    }

    public function register(): void
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    public function handleError(int $level, string $message, string $file, int $line): void
    {
        $this->logError($level, $message, $file, $line);
        http_response_code(500);
    }

    public function handleException(Throwable $exception): void
    {
        $this->logException($exception);
        http_response_code(500);
    }

    public static function logMessage(string $message, string $level = 'INFO'): void
    {
        $logFile = self::$logDir . "app.log";
        self::checkDir(self::$logDir, $logFile);

        $logMessage = sprintf(
            "[%s] %s: %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message
        );
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    private function logError(int $level, string $message, string $file, int $line): void
    {
        $logFile = self::$logDir . "errors.log";
        self::checkDir(self::$logDir, $logFile);

        $logMessage = sprintf(
            "[%s] ERROR: Level: %d | Message: %s | File: %s | Line: %d\n",
            date('Y-m-d H:i:s'),
            $level,
            $message,
            $file,
            $line
        );
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    private function logException(Throwable $exception): void
    {
        $logFile = self::$logDir . "errors.log";
        self::checkDir(self::$logDir, $logFile);

        $exceptionMessage = sprintf(
            "[%s] EXCEPTION: %s | File: %s | Line: %d\n",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
        file_put_contents($logFile, $exceptionMessage, FILE_APPEND);
    }

    /**
     * Vérifie si le dossier de Logs existe, sinon le génère
     *
     * @param string $logDir
     * @param string $logFile
     * @return void
     */
    private static function checkDir(string $logDir, string $logFile): void
    {
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        if (!file_exists($logFile)) {
            touch($logFile);
        }
    }
}
