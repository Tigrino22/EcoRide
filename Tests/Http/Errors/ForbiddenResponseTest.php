<?php

namespace Http\Errors;

use PHPUnit\Framework\TestCase;
use Tigrino\Http\Errors\ForbiddenResponse;

class ForbiddenResponseTest extends TestCase
{
    public function testCreateResponse(): void
    {
        $response = ForbiddenResponse::create(
            body: 'message test'
        );

        $this->assertInstanceOf(ForbiddenResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals('message test', $response->getBody());
    }
}