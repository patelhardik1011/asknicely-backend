<?php
/*
 * This function redirects the user to a page.
 */
function redirect($path)
{
    header("Location: /{$path}");
}

/*
 * This function is used for dying and dumping.
 */
function dd($value)
{
    echo "<pre>";
    print_r($value);
    echo "</pre>";
}

function returnJsonResponse(array $data = [])
{
    if (ob_get_contents()) ob_end_clean();
    // Set the content type to JSON and charset
    // (charset can be set to something else)
    header("Content-type: application/json");
    echo json_encode($data);
    // making sure nothing is added
    exit();
}

/*
 * This function enables displaying of errors in the web browser.
 */
function display_errors()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}