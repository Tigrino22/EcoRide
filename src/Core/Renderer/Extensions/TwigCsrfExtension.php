<?php

namespace Tigrino\Core\Renderer\Extensions;

use Tigrino\Services\CSRFService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigCsrfExtension extends AbstractExtension
{
    private CSRFService $CSRFService;

    public function __construct(CSRFService $CSRFService)
    {
        $this->CSRFService = $CSRFService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('csrf_token', [$this, 'getCsrfToken']),
        ];
    }

    /**
     * Génère un champ CSRF caché pour un formulaire.
     *
     * @param string $name Le nom unique du formulaire.
     * @return string Un champ input caché avec le token CSRF.
     */
    public function getCsrfToken(string $name): string
    {
        $token = $this->CSRFService->generateToken($name);

        return sprintf(
            '<input type="hidden" name="csrf_name" value="%s"/>
                    <input type="hidden" name="csrf_token" value="%s"/>',
            htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
        );
    }
}
