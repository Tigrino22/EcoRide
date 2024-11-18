<?php

namespace Services;

use PHPUnit\Framework\TestCase;
use Tigrino\Services\PasswordService;

class PasswordServiceTest extends TestCase
{
    public function testPasswordValidator()
    {
        $password = '12345678Aa!';

        $this->assertTrue(PasswordService::passwordValidator($password));
    }

    public function testPasswordValidatorLess8()
    {
        $password = '1234Aa!';

        $this->assertCount(1, PasswordService::passwordValidator($password));
    }

    public function testPasswordValidatorLassMaj()
    {
        $password = '123456a!';

        $this->assertCount(1, PasswordService::passwordValidator($password));
    }

    public function testPasswordValidatorLassMin()
    {
        $password = '123456A!';

        $this->assertCount(1, PasswordService::passwordValidator($password));
    }

    public function testPasswordValidatorLassSpecChars()
    {
        $password = '123456Aa';

        $this->assertCount(1, PasswordService::passwordValidator($password));
    }

    public function testPasswordValidatorLassNumbers()
    {
        $password = 'AAAAAaaa!';

        $this->assertCount(1, PasswordService::passwordValidator($password));
    }
}