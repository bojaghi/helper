<?php

namespace Bojaghi\Helper;

use Bojaghi\Continy\Continy;
use Bojaghi\Continy\ContinyException;
use Bojaghi\Continy\ContinyFactory;
use Bojaghi\Continy\ContinyNotFoundException;

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
}
