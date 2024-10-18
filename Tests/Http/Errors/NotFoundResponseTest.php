<?php

namespace Http\Errors;

use PHPUnit\Framework\TestCase;
use Tigrino\Http\Errors\NotFoundResponse;

class NotFoundResponseTest extends TestCase
{
    public function testCreateResponse(): void
    {
        $response = NotFoundResponse::create(
            body: 'message test'
        );

        $this->assertInstanceOf(NotFoundResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('message test', $response->getBody());
    }
}