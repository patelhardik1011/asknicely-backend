<?php

header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

require 'vendor/autoload.php';
require 'core/bootstrap.php';

use App\core\Router;
use App\core\Request;
use App\core\App;

//If we are not in production mode, we will display errors to the web browser.
if (!App::get('config')['options']['production']) {
    display_errors();
}

//This is where we load the routes from the routes file.
try {
    Router::load('app/routes.php')->direct(Request::uri(), Request::method());
} catch (Exception $e) {
}