<?php

use App\core\App;
use App\core\database\QueryBuilder;
use App\core\database\Connection;


require 'helpers.php';

App::bind('config', require 'app/config.php');

App::bind('database', new QueryBuilder(
    Connection::makeConnection(App::get('config')['database'])
));