<?php

namespace Tigrino\Tests\Core\Controllers;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Tests\Core\Controllers\TestController;

class AbstractControllerTest extends TestCase
{
    public function testExecuteMethodExists()
    {
        // Instanciation de la classe concrète
        $controller = new TestController();

        // Création d'une fausse requête ServerRequest
        $request = new ServerRequest('GET', '/test');

        // Appel de la méthode 'execute' pour tester 'testAction'
        $response = $controller->execute('testAction', [], $request);

        // Vérification du type de réponse
        $this->assertInstanceOf(ResponseInterface::class, $response);

        // Vérification du statut de la réponse
        $this->assertEquals(200, $response->getStatusCode());

        // Vérification du corps de la réponse
        $this->assertEquals('Test réussi', (string) $response->getBody());
    }

    public function testExecuteMethodNotFound()
    {
        // Instanciation de la classe concrète
        $controller = new TestController();

        // Création d'une fausse requête ServerRequest
        $request = new ServerRequest('GET', '/test');

        // On s'attend à une exception car la méthode n'existe pas
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Méthode nonExistente non trouvée dans le contrôleur.");

        // Appel de la méthode 'execute' avec une méthode non existante
        $controller->execute('nonExistente', [], $request);
    }
}
