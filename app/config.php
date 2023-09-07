<?php
/*
 * This is where configuration information is stored about the framework. We can add extra options such as the PDO error mode,
 * PDO timeout, or any other attributes that may be useful.
 */
return [
    'database' => [
        'name' => 'sample-db',
        'username' => 'root',
        'password' => 'test',
        'connection' => 'mysql:host=php-mysql-container'
    ],
    'options' => [
        'debug' => true,
        'production' => false
    ]
];