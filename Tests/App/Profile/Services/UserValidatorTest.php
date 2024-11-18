<?php

namespace Tests\App\Profile\Services;

use PHPUnit\Framework\TestCase;
use Tigrino\App\Profile\Services\UserValidator;

class UserValidatorTest extends TestCase
{
    public function testValidateReturnsErrorsForMissingFields()
    {
        $data = [
            'name' => '',
            'firstname' => '',
            'email' => '',
            'telephone' => 'test',
            'birthday' => 'test'
        ];

        $result = UserValidator::validate($data);

        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('name', $result['errors']);
        $this->assertArrayHasKey('firstname', $result['errors']);
        $this->assertArrayHasKey('email', $result['errors']);
        $this->assertArrayHasKey('telephone', $result['errors']);
        $this->assertArrayHasKey('birthday', $result['errors']);
    }

    public function testValidateReturnsErrorForInvalidEmail()
    {
        $data = [
            'name' => 'John',
            'firstname' => 'Doe',
            'email' => 'invalid-email',
        ];

        $result = UserValidator::validate($data);

        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('email', $result['errors']);
        $this->assertEquals('L’email est invalide.', $result['errors']['email']);
    }

    public function testValidateValidDataReturnsDataWithoutErrors()
    {
        $data = [
            'name' => 'John',
            'firstname' => 'Doe',
            'email' => 'john.doe@example.com',
            'birthday' => '1990-01-01',
            'telephone' => '123-456-7890',
            'is_driver' => true,
            'is_passenger' => false,
        ];

        $result = UserValidator::validate($data);

        $this->assertArrayNotHasKey('errors', $result);
        $this->assertEquals($data['name'], $result['name']);
        $this->assertEquals($data['firstname'], $result['firstname']);
        $this->assertEquals($data['email'], $result['email']);
        $this->assertEquals($data['birthday'], $result['birthday']);
        $this->assertEquals($data['telephone'], $result['telephone']);
        $this->assertTrue($result['is_driver']);
        $this->assertFalse($result['is_passenger']);
    }
}
