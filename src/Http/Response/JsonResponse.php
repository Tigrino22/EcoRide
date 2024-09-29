<?php

namespace Tigrino\Http\Response;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Classe permettant de renvoyer facilement
 * une Response sous le format JSON
 * Cette Classe étend de la classe Response de GuzzleHttp
 * qui implément la ResponseInterface du PSR-7
 *
 */
class JsonResponse extends Response
{
    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     */
    public function __construct(int $status = 200, array $headers = [], array $data = [])
    {
        $headers = array_merge(['Content-Type' => 'application/json'], $headers);
        $body = json_encode($data);

        parent::__construct($status, $headers, $body);
    }

    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return ResponseInterface
     */
    public static function create(int $status = 200, array $headers = [], array $data = []): ResponseInterface
    {
        return new self($status, $headers, $data);
    }
}
