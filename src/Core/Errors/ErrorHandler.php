<?php

namespace Tigrino\Core\Errors;

use Throwable;

class ErrorHandler
{
    private string $logDir;

    public function __construct()
    {
        $baseDir = dirname(__DIR__, 3);
        $this->logDir = $baseDir . DIRECTORY_SEPARATOR . "Logs" . DIRECTORY_SEPARATOR;
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

    private function logError(int $level, string $message, string $file, int $line): void
    {
        $this->checkDir($this->logDir, $this->logDir . "errors.log");

        $logMessage = sprintf(
            "[%s] ERROR: %s\n",
            date('Y-m-d H:i:s'),
            "Level: $level | Message: $message | File: $file | Line: $line"
        );
        file_put_contents($this->logDir . "errors.log", $logMessage, FILE_APPEND);
    }

    private function logException(Throwable $exception): void
    {
        $this->checkDir($this->logDir, $this->logDir . "errors.log");

        $exceptionMessage = sprintf(
            "[%s] EXCEPTION: %s\n",
            date('Y-m-d H:i:s'),
            "{$exception->getMessage()} | File: {$exception->getFile()} | Line: {$exception->getLine()}"
        );
        file_put_contents($this->logDir . "errors.log", $exceptionMessage, FILE_APPEND);
    }

    /**
     * Vérifie si le dossier de Logs existe, sinon le génère
     *
     * @param string $logDir
     * @param string $logFile
     * @return void
     */
    private function checkDir(string $logDir, string $logFile): void
    {
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        if (!file_exists($logFile)) {
            touch($logFile);
        }
    }
}
