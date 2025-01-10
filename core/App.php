<?php

namespace Core;

use Core\Config;

class App {
    public static function env(string $key, mixed $default = null): mixed
    {
        return Config::get($key, $default);
    }
}