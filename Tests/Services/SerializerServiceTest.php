<?php

namespace Tests\Services;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Tigrino\Services\SerializerService;

class SerializerServiceTest extends TestCase
{
    public function testObjectToArray()
    {
        $object = new FakeClass();
        $object->setBar('foo');
        $object->setFoo('bar');

        $serializer = new SerializerService();
        $array = $serializer->objectToArray($object);

        $this->assertEquals('bar', $array['foo']);
        $this->assertEquals('foo', $array['bar']);
    }

    public function testArrayToObject()
    {
        $array = [
            'foo' => 'bar',
            'bar' => 'foo'
        ];

        $serializer = new SerializerService();
        $object = $serializer->arrayToObject($array, FakeClass::class);

        $this->assertEquals('bar', $object->getFoo());
        $this->assertEquals('foo', $object->getBar());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("La classe InvalidClass n'existe pas.");

        $serializer->arrayToObject($array, "InvalidClass");

    }

}
