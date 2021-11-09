<?php

namespace App\Service;

use App\Exception\Container\ContainerException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private static Container $container;

    private function __construct(){}

    public static function getInstance(): Container
    {
       if(!isset(self::$container))
       {
           self::$container = new self;
       }
       return self::$container;
    }

    private array $entries = [];

    public function get(string $id)
    {
        if ($this->has($id)) {
            $entry = $this->entries[$id];
            return $entry($this);
        }
        return $this->resolve($id);
    }

    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    public function set(string $id, callable $concrete): void
    {
        $this->entries[$id] = $concrete;
    }

    /**
     * @throws \ReflectionException
     * @throws ContainerException
     */
    public function resolve(string $id)
    {
        $reflectionClass = new \ReflectionClass($id);
        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException(sprintf('Class %s is not instantiable', $id));
        }

        $constructor = $reflectionClass->getConstructor();

        if (!$constructor) {
            return new $id;
        }

        $parameters = $constructor->getParameters();

        if (!$parameters) {
            return new $id;
        }
        return $this->resolveDependencies($id, $reflectionClass, $parameters);
    }

    public function resolveDependencies($id, $reflectionClass, $parameters)
    {
        $dependencies = array_map(function (\ReflectionParameter $param) use ($id) {
            $name = $param->getName();
            $type = $param->getType();

            if (!$type) {
                throw new ContainerException(sprintf(
                    'Failed to resolve class %s because param %s is missing a type hint',
                    $id,
                    $name
                ));
            }
            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                return $this->get($type->getName());
            }
            throw new ContainerException(sprintf(
                'Failed to resolve class %s because of invalid parameter %s.',
                $type,
                $name
            ));
        }, $parameters);
        return $reflectionClass->newInstanceArgs($dependencies);
    }
}