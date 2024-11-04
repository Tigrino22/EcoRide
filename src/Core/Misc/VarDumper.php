<?php

namespace Tigrino\Core\Misc;

class VarDumper
{
    public static function dump($var)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}
