<?php

require_once './src/routes.php';
require_once './src/funktioner.php';
require_once './src/Route.php';
require_once './src/Response.php';
require_once './src/RequestMethod.php';

// Hämta begärd resurs
$uri = filter_var($_SERVER['REQUEST_URI'], FILTER_UNSAFE_RAW);

// Läs in eventuell POST-data
if (count($_POST) > 0) {
    $postData = $_POST;
    $metod = RequestMethod::POST;
} else {
    $postData = [];
    $metod = RequestMethod::GET;
}
// Om postparameter, lägg in post data med post. Annars är postdata tom med get.

// Hämta ruttinformation
$route = getRoute($uri, $metod);
//var_dump($route->getRoute());
// Hantera ruttinformationen
switch ($route->getRoute()) {
    case "/activity/":
        require_once './src/activities.php';
        $retur = activities($route, $postData);
        break;
    case "/tasklist/":
        require_once './src/tasks.php';
        $retur = tasklists($route);
        break;
    case "/task/":
        require_once './src/tasks.php';
        $retur = tasks($route, $postData);
        break;
    case "/compilation/":
        require_once './src/compilations.php';
        $retur = compilations($route);
        break;
    default:
        $retur = new Response("Okänt anrop", 400);
        break;
}

// Skicka svar som JSON-data
$retur->skickaJSON();