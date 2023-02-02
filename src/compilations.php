<?php

declare (strict_types=1);

/**
 * Läs av rutt-information och anropa funktion baserat på angiven rutt
 * @param Route $route Rutt-information
 * @param array $postData Indata för behandling i angiven rutt
 * @return Response
 */
function compilations(Route $route): Response {
    try {
        if (count($route->getParams()) === 2 && $route->getMethod() === RequestMethod::GET) {
            return hamtaSammanstallning(new DateTimeImmutable($route->getParams()[0]), new DateTimeImmutable($route->getParams()[1]));
        }
    } catch (Exception $exc) {
        return new Response($exc->getMessage(), 400);
    }

    return new Response("Okänt anrop", 400);
}

/**
 * Hämtar en sammanställning av uppgiftsposter i ett angivet datumintervall
 * @param DateTimeInterface $from
 * @param DateTimeInterface $tom
 * @return Response
 */
function hamtaSammanstallning(DateTimeInterface $from, DateTimeInterface $tom): Response {
    // Kontrollera indata
    if ($from > $tom) {
        $out = new stdClass();
        $out->error = ["Hämta sammanställning misslyckades", "Till-datum måste vara efter från-datum"];
        return new Response($out, 400);
    }

    // Koppla databas
    $db = connectDb();

    // Förbered och exekvera SQL
    $stmt = $db->prepare("SELECT kategori, kategoriID, "
            . "SEC_TO_TIME(SUM(TIME_TO_SEC(tid))) as tid "
	    . "FROM uppgifter u "
	    . "INNER JOIN kategorier k ON k.id=u.kategoriID "
	    . "WHERE datum BETWEEN :fran AND :till "
	    . "GROUP BY kategoriID ");
    $stmt->execute(["fran" => $from->format('Y-m-d'), "till" => $tom->format('Y-m-d')]);

    // Kontrollera svar och generera utdata
    $poster = [];
    while ($row = $stmt->fetch()) {
        $rec = new stdClass();
        $rec->activityId = $row["kategoriID"];
        $rec->activity = $row["kategori"];
        $rec->time = substr($row["tid"], 0, 5);
        $poster[] = $rec;
    }

    $out = new stdClass();
    $out->tasks = $poster;
    return new Response($out);
}