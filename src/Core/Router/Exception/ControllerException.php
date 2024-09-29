<?php

namespace Tigrino\Core\Router\Exception;

use Exception;

class ControllerException extends Exception
{
    // Redéfini l'exception pour que le message ne soit pas facultatif.
    public function __construct($message, $code = 0, \Throwable $previous = null)
    {
        // s'assurer que tout est correctement assigné
        parent::__construct($message, $code, $previous);
    }
}
