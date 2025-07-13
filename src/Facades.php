<?php

namespace Bojaghi\Helper;

use Bojaghi\Continy\Continy;
use Bojaghi\Continy\ContinyException;
use Bojaghi\Continy\ContinyFactory;
use Bojaghi\Continy\ContinyNotFoundException;
use Psr\Container\ContainerExceptionInterface;

/**
 * These static methods are very frequently used if you are using continy as your container.
 */
class Facades
{
    public static function container(array|string $config = ''): Continy
    {
        static $continy = null;

        if (is_null($continy)) {
            try {
                $continy = ContinyFactory::create($config);
            } catch (ContinyException $e) {
                wp_die($e->getMessage());
            }
        }

        return $continy;
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @param bool            $constructorCall
     *
     * @return T|object|null
     */
    public static function get(string $id, bool $constructorCall = false)
    {
        try {
            $instance = self::container()->get($id, $constructorCall);
        } catch (ContinyException $_) {
            return null;
        }

        return $instance;
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @param string          $method
     * @param array|false     $args
     *
     * @return mixed
     */
    public static function call(string $id, string $method, array|false $args = false): mixed
    {
        try {
            $container = self::container();
            $instance  = $container->get($id);
            if (!$instance) {
                throw new ContinyNotFoundException("Instance $id not found");
            }
            return $container->call([$instance, $method], $args);
        } catch (ContinyException $e) {
            wp_die($e->getMessage());
        }
    }

    public static function parseCallback(string|array|callable $callback): callable|null
    {
        if (is_callable($callback)) {
            return $callback;
        }

        if (is_string($callback) && str_contains($callback, '@')) {
            $split = explode('@', $callback, 2);
        } else {
            // array.
            $split = $callback;
        }

        if (2 === count($split)) {
            // 'foo@bar' style.
            $cls    = $split[0];
            $method = $split[1];

            if (class_exists($cls) && method_exists($cls, $method)) {
                if (is_callable([$cls, $method])) {
                    // Static methods.
                    return [$cls, $method];
                }
                // Common methods, the class needs to be instantiated,
                // Or $cls may be an alias for the container.
                $instance = static::get($cls);
                if (is_callable([$instance, $method])) {
                    return [$instance, $method];
                }
            }
        } elseif (1 === count($split)) {
            // It may be a class name, a container alias.
            $instance = static::get($split[0]);
            if (is_callable($instance)) {
                return $instance;
            }
        }

        return null;
    }
}
