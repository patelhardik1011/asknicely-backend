<?php

namespace App\core\database;

use PDO;
use PDOException;

class Connection
{
    public static function makeConnection($config)
    {
        try {
            return new PDO(
                $config['connection'] . ';dbname=' . $config['name'],
                $config['username'],
                $config['password'],
            );
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'PDO exception: ' . $e->getMessage()
            ];
        }
    }
}