<?php

namespace Tests\Http\Response;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Tigrino\Http\Response\JsonResponse;

class JsonResponseTest extends TestCase
{
    public function testJsonResponseData()
    {
        // Réponse JSON avec des données
        $data = ['status' => 'success', 'message' => 'Test passed'];
        $response = JsonResponse::create(200, [], $data);

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $body = (string) $response->getBody();
        $this->assertEquals(json_encode($data), $body);
    }

    public function testJsonResponseStatusCode()
    {
        $data = ['status' => 'error', 'message' => 'Test failed'];
        $response = new JsonResponse(400, headers: [], data: $data);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testEmptyJsonResponse()
    {
        // Réponse JSON vide
        $response = JsonResponse::create();

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $body = (string) $response->getBody();
        $this->assertEquals(json_encode([]), $body);
    }
}
