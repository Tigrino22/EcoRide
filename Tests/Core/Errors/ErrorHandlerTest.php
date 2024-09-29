<?php

namespace Tests\Core\Errors;

use Tigrino\Core\Errors\ErrorHandler;
use PHPUnit\Framework\TestCase;

class ErrorHandlerTest extends TestCase
{
    private ErrorHandler $errorHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->errorHandler = new ErrorHandler();
        $this->errorHandler->register();
    }

    protected function tearDown(): void
    {
        restore_error_handler();
        restore_exception_handler();
        parent::tearDown();
    }

    public function testHandleError()
    {
        $level = E_USER_NOTICE;
        $message = 'Test error';
        $file = __FILE__;
        $line = __LINE__;

        $this->errorHandler->handleError($level, $message, $file, $line);

        $logContent = file_get_contents(dirname(__DIR__, 3) . "/Logs/errors.log");
        $this->assertStringContainsString('ERROR: Level: ' . $level, $logContent);
        $this->assertStringContainsString('Message: ' . $message, $logContent);
        $this->assertStringContainsString('File: ' . $file, $logContent);
        $this->assertStringContainsString('Line: ' . $line, $logContent);
    }

    public function testHandleException()
    {
        $exception = new \Exception('Test exception');

        $this->errorHandler->handleException($exception);

        $logContent = file_get_contents(dirname(__DIR__, 3) . "/Logs/errors.log");
        $this->assertStringContainsString('EXCEPTION: ' . $exception->getMessage(), $logContent);
        $this->assertStringContainsString('File: ' . $exception->getFile(), $logContent);
        $this->assertStringContainsString('Line: ' . $exception->getLine(), $logContent);
    }
}
