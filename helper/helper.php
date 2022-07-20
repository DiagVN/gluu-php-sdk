<?php


if (! function_exists('env')) {
    function env(string $key, $default = ''): string
    {
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        } else {
            return $default;
        }
    }
}
