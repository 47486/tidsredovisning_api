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
    // Kontrollera indata
    $kollatID= filter_var($id, FILTER_VALIDATE_INT);
    if(!$kollatID || $kollatID < 1) {
        $out=new stdClass();
        $out->error=["Felaktig indata", "$id är inget giltigt heltal"];
        return new Response($out, 400);
    }
    
    // Koppla till databas och hämta post
    $db = connectDb();
    $stmt=$db->prepare("SELECT id, kategori from kategorier where id=:id");
    if (!$stmt->execute(["id"=>$kollatID])) {
        $out=new stdClass();
        $out->error=["Fel vid läsning från databasen", implode(",", $db->errorInfo()  )   ];
        return new Response($out, 400);
    }
    
    // Sätt utdata och returnera utdata
    if($row=$stmt->fetch()) {
        $out=new stdClass();
        $out->id=$row["id"];
        $out->activity=$row["kategori"];
        return new Response($out);
    } else {
        $out=new stdClass();
        $out->error=["Hittade ingen post med id=$kollatID"];
        return new Response($out, 400);
    }
    
    
   
    
    
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
