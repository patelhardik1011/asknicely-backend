<?php

/**
 * Load routes
 */

$router->post('list', 'EmployeeController@list');

$router->post('employee/update/{id}', 'EmployeeController@update');

$router->post('import', 'ImportController@import');

$router->post('salary', 'EmployeeController@salary');