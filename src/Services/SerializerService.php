<?php

namespace Tigrino\Services;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;

class SerializerService
{
    public function objectToArray($object)
    {
        $reflectionClass = new ReflectionClass(get_class($object));
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $array = [];

        foreach ($methods as $method) {
            $methodName = $method->getName();

            // Vérifie si la méthode est un getter
            if (str_starts_with($methodName, 'get') && $method->getNumberOfParameters() === 0) {
                // Convertit le nom de la méthode en une clé de tableau
                $property = lcfirst(str_replace('get', '', $methodName));
                $array[$property] = $method->invoke($object);
            }
        }

        return $array;
    }

    function arrayToObject(array $data, string $className)
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("La classe $className n'existe pas.");
        }

        $object = new $className();
        $reflectionClass = new ReflectionClass($className);

        foreach ($data as $key => $value) {
            $setter = 'set' . ucfirst($key);


            if ($reflectionClass->hasMethod($setter)) {
                $method = $reflectionClass->getMethod($setter);
                if ($method->isPublic()) {
                    // Appelle le setter avec la valeur correspondante
                    $method->invoke($object, $value);
                }
            }
        }

        return $object;
    }
}
