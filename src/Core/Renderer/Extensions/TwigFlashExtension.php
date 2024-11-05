<?php

namespace Tigrino\Core\Renderer\Extensions;

use Tigrino\Services\FlashService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigFlashExtension extends AbstractExtension
{

    private FlashService $flashService;

    public function __construct(FlashService $flashService)
    {
        $this->flashService = $flashService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('flash', [$this, 'getFlashMessages'])
        ];
    }

    public function getFlashMessages(string $type = null): array
    {
        $messagesFlash = $this->flashService->getMessages();

        return $type ? ($messagesFlash[$type] ?? []) : $messagesFlash;
    }
}