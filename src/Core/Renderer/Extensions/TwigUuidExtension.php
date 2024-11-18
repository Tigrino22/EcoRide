<?php

namespace Tigrino\Core\Renderer\Extensions;

use Ramsey\Uuid\UuidInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigUuidExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
        new TwigFilter('uuid_to_string', [$this, 'uuidToString']),
        ];
    }

    public function uuidToString(UuidInterface $uuid): string
    {
        return $uuid->toString();
    }
}
