<?php

declare (strict_types=1);
require_once '../src/activities.php';

/**
 * Funktion för att testa alla aktiviteter
 * @return string html-sträng med resultatet av alla tester
 */
function allaActivityTester(): string {
    // Kom ihåg att lägga till alla funktioner i filen!
    $retur = "";
    $retur .= test_HamtaAllaAktiviteter();
    $retur .= test_HamtaEnAktivitet();
    $retur .= test_SparaNyAktivitet();
    $retur .= test_UppdateraAktivitet();
    $retur .= test_RaderaAktivitet();

    return $retur;
}

/**
 * Funktion för att testa en enskild funktion
 * @param string $funktion namnet (utan test_) på funktionen som ska testas
 * @return string html-sträng med information om resultatet av testen eller att testet inte fanns
 */
function testActivityFunction(string $funktion): string {
    if (function_exists("test_$funktion")) {
        return call_user_func("test_$funktion");
    } else {
        return "<p class='error'>Funktionen test_$funktion finns inte.</p>";
    }
}

/**
 * Tester för funktionen hämta alla aktiviteter
 * @return string html-sträng med alla resultat för testerna 
 */
function test_HamtaAllaAktiviteter(): string {
    $retur = "<h2>test_HamtaAllaAktiviteter</h2>";
    try {
        $svar = hamtaAlla();

        // Kontrollera statuskoden
        if (!$svar->getStatus() === 200) {
            $retur .= "<p class='Error'>Felaktig statuskod, förväntade 200, fick {$svar->getStatus()}</p>";
        } else {
            $retur .= "<p class='ok'>Korrekt statuskod 200</p>";
        }

        // Kontrollerar att ingen aktivitet är tom
        foreach ($svar->getContent() as $kategori) {
            if ($kategori->kategori === "") {
                $retur .= "<p class='error'>TOM aktivitet!</p>";
            }
        }
    } catch (Exception $ex) {
        $retur .= "<p class='Något gick fel, meddelandet säger: '>{$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Tester för funktionen hämta enskild aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_HamtaEnAktivitet(): string {
    $retur = "<h2>test_HamtaEnAktivitet</h2>";

    try {
        // Testa negativt tal
        $svar = hamtaEnSkild(-1);
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Hämta enskild med negativt tal ger förväntat svar 400</p>";
        } else {
            $retur .= "<p class='error'>Hämta enskild med negativt tal ger {$svar->getStatus()}"
                    . "inte förväntat svar 400</p>";
        }
        // Testa för stort tal
        $svar = hamtaEnSkild(1000);
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Hämta enskild med för stort tal ger förväntat svar 400</p>";
        } else {
            $retur .= "<p class='error'>Hämta enskild med för stort tal ger {$svar->getStatus()}"
                    . "inte förväntat svar 400</p>";
        }
        // Testa bokstäver
        $svar = hamtaEnSkild((int) "sju");
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Hämta enskild med bokstäver ger förväntat svar 400</p>";
        } else {
            $retur .= "<p class='error'>Hämta enskild med bokstäver tal ger {$svar->getStatus()}"
                    . "inte förväntat svar 400</p>";
        }
        // Testa giltigt tal
        $svar = hamtaEnSkild(3);
        if ($svar->getStatus() === 200) {
            $retur .= "<p class='ok'>Hämta enskild med 3 ger förväntat svar 200</p>";
        } else {
            $retur .= "<p class='error'>Hämta enskild med 3 ger {$svar->getStatus()}"
                    . "inte förväntat svar 200</p>";
        }
    } catch (Exception $ex) {
        $retur .= "<p class='Något gick fel, meddelandet säger: '>{$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Tester för funktionen spara aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_SparaNyAktivitet(): string {
    $retur = "<h2>test_SparaNyAktivitet</h2>";

    // Testa tom aktivitet

    $aktivitet = "";
    $svar = sparaNy($aktivitet);
    if ($svar->getStatus() === 400) {
        $retur .= "<p class='ok'>Spara tom aktivitet misslyckades som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Spara tom aktivitet returnerade {$svar->getStatus()} förväntades 400</p>";
    }

    // Testa lägg till
    $db = connectDb();
    $db->beginTransaction();
    $aktivitet = "Nissan";
    $svar = sparaNy($aktivitet);
    if ($svar->getStatus() === 200) {
        $retur .= "<p class='ok'>Spara ny aktivitet lyckades som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Spara ny aktivitet returnerade {$svar->getStatus()} förväntades 200</p>";
    }
    $db->rollBack();

    // Testa lägg till samma
    $db->beginTransaction();
    $aktivitet = "Nissan";
    $svar = sparaNy($aktivitet); // Spara första gången, borde lyckas
    $svar = sparaNy($aktivitet); // Faktiskt test, funkar det andra gången
    if ($svar->getStatus() === 400) {
        $retur .= "<p class='ok'>Spara ny aktivitet två gånger misslyckades som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Spara ny aktivitet två gånger returnerade {$svar->getStatus()} förväntades 400</p>";
    }
    $db->rollBack();

    return $retur;
}

/**
 * Tester för uppdatera aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_UppdateraAktivitet(): string {
    $retur = "<h2>test_UppdateraAktivitet</h2>";

    try {
        // Testa uppdatera med en ny text i aktivitet
        $db = connectDb();
        $db->beginTransaction();
        $nyPost = sparaNy("Nizze");
        if ($nyPost->getStatus() !== 200) {
            throw new Exception("Skapa ny post misslyckades", 10001);
        }
        $uppdateringsId = (int) $nyPost->getContent()->id;
        $svar = uppdatera($uppdateringsId, "Pelle");
        if ($svar->getStatus() === 200 && $svar->getContent()->result === true) {
            $retur .= "<p class='ok'>Uppdatera aktivitet lyckades</p>";
        } else {
            $retur .= "<p class='error'>Uppdatera aktivitet misslyckades "
                    . "{$svar->getStatus()} returnerades istället för förväntat 200";
            if (isset($svar->getContent()->result)) {
                $retur .= var_export($svar->getContent()->result) . " returnerades istället för förväntat 'true'";
            } else {
                $retur .= "{$svar->getStatus()} returnerades istället för förväntat 200";
            }
            $retur .= "</p>";
        }

        $db->rollBack();

        // Testa uppdatera med samma text i aktivitet
        $db->beginTransaction();
        $nyPost = sparaNy("Nizze");
        if ($nyPost->getStatus() !== 200) {
            throw new Exception("Skapa ny post misslyckades", 10001);
        }
        $uppdateringsId = (int) $nyPost->getContent()->id; // Den nya postens id
        $svar = uppdatera($uppdateringsId, "Nizze");       // Prova att uppdatera
        if ($svar->getStatus() === 200 && $svar->getContent()->result === false) {
            $retur .= "<p class='ok'>Uppdatera aktivitet med samma text lyckades</p>";
        } else {
            $retur .= "<p class='error'>Uppdatera aktivitet med samma text misslyckades "
                    . "{$svar->getStatus()} returnerades istället för förväntat 200";
            if (isset($svar->getContent()->result)) {
                $retur .= var_export($svar->getContent()->result) . " returnerades istället för förväntat 'false'";
            } else {
                $retur .= "{$svar->getStatus()} returnerades istället för förväntat 200";
            }
            $retur .= "</p>";
        }
        $db->rollBack();
        
        // Testa med tom aktivitet
        $db->beginTransaction();
        $nyPost = sparaNy("Nizz");
        if ($nyPost->getStatus() !== 200) {
            throw new Exception("Skapa ny post misslyckades", 10001);
        }
        $uppdateringsId = (int) $nyPost->getContent()->id; // Den nya postens id
        $svar = uppdatera($uppdateringsId, "");       // Prova att uppdatera
        if ($svar->getStatus() === 400 ) {
            $retur .= "<p class='ok'>Uppdatera aktivitet med tom text misslyckades som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Uppdatera aktivitet med tom text returnerade {$svar->getStatus()} istället för förväntat 400, "
                    . "{$svar->getStatus()} returnerades istället för förväntat 200";
            
        }
        $db->rollBack();
        
        // Testa med ogiltigt id (-1)
        $db->beginTransaction();
        $svar = uppdatera(-1, "boleno");       // Prova att uppdatera
        if ($svar->getStatus() === 400 ) {
            $retur .= "<p class='ok'>Uppdatera aktivitet med ogiltigt id (-1) misslyckades som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Uppdatera aktivitet med ogiltigt id (-1) returnerade {$svar->getStatus()} istället för förväntat 400";
            
        }
        $db->rollBack();
        
        
        // Testa med obefintligt id (100)
        $db->beginTransaction();
       
        
        $svar = uppdatera(100, "bolenos");       // Prova att uppdatera
        if ($svar->getStatus() === 200 && $svar->getContent()->result===false) {
            $retur .= "<p class='ok'>Uppdatera aktivitet med ogiltigt id (100) misslyckades som förväntat</p>";
        } else { 
            $retur .= "<p class='error'>Uppdatera aktivitet med obefintligt id (100) lyckades ";
            if (isset($svar->getContent()->result)) {
                $retur .= var_export($svar->getContent()->result) . " returnerades istället för förväntat 'false'";
            } else {
                $retur .= "{$svar->getStatus()} returnerades istället för förväntat 200";
            }
            $retur .= "</p>";
        }
        $db->rollBack();
        
        // Cipis bugg - Testa med mellanslag som aktivitet
        
        $db->beginTransaction();
        $nyPost = sparaNy("Nizze");
        if ($nyPost->getStatus() !== 200) {
            throw new Exception("Skapa ny post misslyckades", 10001);
        }
        $uppdateringsId = (int) $nyPost->getContent()->id;
        $svar = uppdatera($uppdateringsId, " ");
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Uppdatera aktivitet med mellanslag misslyckades som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Uppdatera aktivitet med mellanslag returnerade "
                    . "{$svar->getStatus()} istället för förväntat 400";
        }

        $db->rollBack();
        
    } catch (Exception $ex) {
        $db->rollBack();
        if ($ex->getCode() === 10001) {
            $retur .= "<p class='error'>Spara ny post misslyckades, uppdatera går inte att testa!!!</p>";
        } else {
            $retur .= "<p class='error'>Fel inträffade:<br>{$ex->getMessage()}</p>";
        }
    }

    return $retur;
}

/**
 * Tester för funktionen radera aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_RaderaAktivitet(): string {
    $retur = "<h2>test_RaderaAktivitet</h2>";
try {
    // Testa felaktigt ID (-1)
     $db = connectDb();
    $db->beginTransaction();
        $svar = radera(-1);       // Prova att uppdatera
        if ($svar->getStatus() === 400 ) {
            $retur .= "<p class='ok'>Radera aktivitet med ogiltigt id (-1) misslyckades som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Radera aktivitet med ogiltigt id (-1) returnerade {$svar->getStatus()} istället för förväntat 400";
        }
    $db->rollBack();
    
    // Testa felaktigt ID (fem)
    $db->beginTransaction();
        $svar = radera((int)"fem");       // Prova att uppdatera
        if ($svar->getStatus() === 400 ) {
            $retur .= "<p class='ok'>Radera aktivitet med ogiltigt id (fem) misslyckades som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Radera aktivitet med ogiltigt id (fem) returnerade {$svar->getStatus()} istället för förväntat 400";
        }
    $db->rollBack();
    
    // Testa ID som inte finns (9001)
    $db->beginTransaction();
        $svar = radera(9001);       // Prova att uppdatera
        if ($svar->getStatus() === 200 && $svar->getContent()->result===false) {
            $retur .= "<p class='ok'>Radera aktivitet med ogiltigt id (9001) ger förväntat svar 200 och result=false</p>";
        } else {
            $retur .= "<p class='error'>Radera aktivitet med ogiltigt id (9001) returnerade {$svar->getStatus()} istället för förväntat 400";
        }
    $db->rollBack();
    
    // Testa nyskapat ID
    $db->beginTransaction();

    $svar = sparaNy("Nizze");
    $uppdateringsId = (int) $svar->getContent()->id; // Den nya postens id
        $svar = radera($uppdateringsId);       
        if ($svar->getStatus() === 200 ) {
            $retur .= "<p class='ok'>Radera aktivitet med nyskapat id lyckades som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Radera aktivitet med nyskapat id returnerade {$svar->getStatus()} istället för förväntat 200";
        }
    $db->rollBack();
    
} catch (Exception $ex) {
    $db->rollBack();
        if ($ex->getCode() === 10001) {
            $retur .= "<p class='error'>Spara ny post misslyckades, uppdatera går inte att testa!!!</p>";
        } else {
            $retur .= "<p class='error'>Fel inträffade:<br>{$ex->getMessage()}</p>";
        }
    }

    return $retur;
}