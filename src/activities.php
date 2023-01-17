<?php

declare (strict_types=1);
require_once __DIR__ .'./funktioner.php';
/**
 * Läs av rutt-information och anropa funktion baserat på angiven rutt
 * @param Route $route Rutt-information
 * @param array $postData Indata för behandling i angiven rutt
 * @return Response
 */
function activities(Route $route, array $postData): Response {
    try {
        if (count($route->getParams()) === 0 && $route->getMethod() === RequestMethod::GET) {
            return hamtaAlla();
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::GET) {
            return hamtaEnskild((int) $route->getParams()[0]);
        }
        if (count($route->getParams()) === 0 && $route->getMethod() === RequestMethod::POST) {
            return sparaNy((string) $postData["activity"]);
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::PUT) {
            return uppdatera((int) $route->getParams()[0], (string) $postData["activity"]);
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::DELETE) {
            return radera((int) $route->getParams()[0]);
        }
    } catch (Exception $exc) {
        return new Response($exc->getMessage(), 400);
    }

    return new Response("Okänt anrop", 400);
}

/**
 * Returnerar alla aktiviteter som finns i databasen
 * @return Response
 */
function hamtaAlla(): Response {
    //Koppla mot databasen
    $db=connectDb();
    
    //Hämta alla poster från tabellen
    
    $result=$db->query("SELECT ID, kategori from kategorier ORDER BY ID");
    
    //Lägga in posterna i en array
    $retur=[];
    while($row=$result->fetch()){
        $post=new stdClass();
        $post->id=$row['ID'];
        $post->kategori=$row['kategori'];
        //Man kan lägga till fler kolumner här
        $retur[]=$post;
    }
    
    //Returnera svaret
    return new Response($retur, 200);
}

/**
 * Returnerar en enskild aktivitet som finns i databasen
 * @param int $id Id för aktiviteten
 * @return Response
 */
function hamtaEnskild(int $id): Response {
    return new Response("Hämta aktivitet $id", 200);
}

/**
 * Lagrar en ny aktivitet i databasen
 * @param string $aktivitet Aktivitet som ska sparas
 * @return Response
 */
function sparaNy(string $aktivitet): Response {
    return new Response("Sparar ny aktivitet:$aktivitet", 200);
}

/**
 * Uppdaterar angivet id med ny text
 * @param int $id Id för posten som ska uppdateras
 * @param string $aktivitet Ny text
 * @return Response
 */
function uppdatera(int $id, string $aktivitet): Response {
    return new Response("Uppdaterar aktivetet $id -> $aktivitet", 200);
}

/**
 * Raderar en aktivitet med angivet id
 * @param int $id Id för posten som ska raderas
 * @return Response
 */
function radera(int $id): Response {
    return new Response("Raderar aktivitet $id", 200);
}
