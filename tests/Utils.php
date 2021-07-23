<?php


function callMethod($object, string $method , array $parameters = [])
{
    try {
        $className = get_class($object);
        $reflection = new \ReflectionClass($className);
    } catch (\ReflectionException $e) {
       throw new \Exception($e->getMessage());
    }

    $method = $reflection->getMethod($method);
    $method->setAccessible(true);

    return $method->invokeArgs($object, $parameters);
}