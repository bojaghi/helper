<?php

namespace Bojaghi\Helper;

class Helper
{
    /**
     * Commonly used loadConfig method
     *
     * @param string|array $config If $config is string, it is treated as a path to config file.
     *                             If #config is array, it is configuration itself.
     *
     * @return array
     */
    public static function loadConfig(string|array $config): array
    {
        $output = [];

        if (is_string($config) && file_exists($config) && is_readable($config)) {
            $output = include $config;
        } elseif (is_array($config)) {
            $output = $config;
        }

        return $output;
    }
}