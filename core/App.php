<?php

namespace App\core;

use Exception;

class App
{
    protected static $registry = [];

    public static function bind($key, $value): void
    {
        static::$registry[$key] = $value;
    }

    /**
     * @throws Exception
     */
    public static function get($key)
    {
        if (!array_key_exists($key, self::$registry)) {
            throw new Exception("No registry key exists");
        }
        return self::$registry[$key];
    }

    /**
     * @throws Exception
     */
    public static function DB()
    {
        return self::get('database');
    }

    /**
     * @throws Exception
     */
    public function Config()
    {
        return self::get('config');
    }
}