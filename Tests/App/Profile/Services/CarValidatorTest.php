<?php

namespace Tests\App\Profile\Services;

use PHPUnit\Framework\TestCase;
use Tigrino\App\Profile\Services\CarValidator;

class CarValidatorTest extends TestCase
{
    public function testValidateReturnsErrorsForMissingFields()
    {
        $data = [
            'model' => '',
            'brand' => '',
            'color' => '',
            'plate_of_registration' => '',
            'first_registration_at' => '',
            'places' => 0,
        ];

        $result = CarValidator::validate($data);

        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('model', $result['errors']);
        $this->assertArrayHasKey('brand', $result['errors']);
        $this->assertArrayHasKey('color', $result['errors']);
        $this->assertArrayHasKey('plate_of_registration', $result['errors']);
        $this->assertArrayHasKey('first_registration_at', $result['errors']);
        $this->assertArrayHasKey('places', $result['errors']);
    }

    public function testValidateReturnsErrorForInvalidDate()
    {
        $data = [
            'model' => 'Astra',
            'brand' => 'Opel',
            'color' => 'Blue',
            'plate_of_registration' => 'ABC1234',
            'first_registration_at' => 'invalid-date',
            'places' => 4,
        ];

        $result = CarValidator::validate($data);

        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('first_registration_at', $result['errors']);
        $this->assertEquals('La date de première immatriculation est invalide.', $result['errors']['first_registration_at']);
    }

    public function testValidateValidDataReturnsDataWithoutErrors()
    {
        $data = [
            'model' => 'Astra',
            'brand' => 'Opel',
            'color' => 'Blue',
            'plate_of_registration' => 'ABC1234',
            'first_registration_at' => '2022-01-01',
            'places' => 4,
        ];

        $result = CarValidator::validate($data);

        $this->assertArrayNotHasKey('errors', $result);
        $this->assertEquals($data['model'], $result['model']);
        $this->assertEquals($data['brand'], $result['brand']);
        $this->assertEquals($data['color'], $result['color']);
        $this->assertEquals($data['plate_of_registration'], $result['plate_of_registration']);
        $this->assertEquals($data['first_registration_at'], $result['first_registration_at']);
        $this->assertEquals($data['places'], $result['places']);
    }

    public function testValidateReturnsErrorForInvalidPlaces()
    {
        $data = [
            'model' => 'Astra',
            'brand' => 'Opel',
            'color' => 'Blue',
            'plate_of_registration' => 'ABC1234',
            'first_registration_at' => '2022-01-01',
            'places' => -1,
        ];

        $result = CarValidator::validate($data);

        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('places', $result['errors']);
        $this->assertEquals('Le nombre de places doit être un entier positif.', $result['errors']['places']);
    }
}
