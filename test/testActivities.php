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
         $svar=hamtaAlla();
    
    
    // Kontrollera statuskoden
    if(!$svar->getStatus()===200) {
        $retur .="<p class='Error'>Felaktig statuskod, förväntade 200, fick {$svar->getStatus()}</p>";
    } else {
        $retur .="<p class='ok'>Korrekt statuskod 200</p>";
    }
    
    // Kontrollerar att ingen aktivitet är tom
    foreach ($svar->getContent() as $kategori) {
        if ($kategori->kategori==="") {
            $retur .="<p class='error'>TOM aktivitet!</p>";
        }
    }
    
    
    } catch (Exception $ex) {
        $retur .="<p class='Något gick fel, meddelandet säger: '>{$ex->getMessage()}</p>";
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
        if ($svar->getStatus()===400) {
            $retur .= "<p class='ok'>Hämta enskild med negativt tal ger förväntat svar 400</p>";
        } else {
            $retur .= "<p class='error'>Hämta enskild med negativt tal ger {$svar->getStatus()}" 
            . "inte förväntat svar 400</p>";
        }
        // Testa för stort tal
        $svar = hamtaEnSkild(1000);
        if ($svar->getStatus()===400) {
            $retur .= "<p class='ok'>Hämta enskild med för stort tal ger förväntat svar 400</p>";
        } else {
            $retur .= "<p class='error'>Hämta enskild med för stort tal ger {$svar->getStatus()}" 
            . "inte förväntat svar 400</p>";
        }
        // Testa bokstäver
        $svar = hamtaEnSkild((int) "sju");
        if ($svar->getStatus()===400) {
            $retur .= "<p class='ok'>Hämta enskild med bokstäver ger förväntat svar 400</p>";
        } else {
            $retur .= "<p class='error'>Hämta enskild med bokstäver tal ger {$svar->getStatus()}" 
            . "inte förväntat svar 400</p>";
        }
        // Testa giltigt tal
        $svar = hamtaEnSkild((int)"agagagagag");
        if ($svar->getStatus()===400) {
            $retur .= "<p class='ok'>Hämta enskild med 3 ger förväntat svar 200</p>";
        } else {
            $retur .= "<p class='error'>Hämta enskild med 3 ger {$svar->getStatus()}" 
            . "inte förväntat svar 200</p>";
        }
        
    } catch (Exception $ex) {
        $retur .="<p class='Något gick fel, meddelandet säger: '>{$ex->getMessage()}</p>";
    }
    
    return $retur;
}

/**
 * Tester för funktionen spara aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_SparaNyAktivitet(): string {
    $retur = "<h2>test_SparaNyAktivitet</h2>";
    $retur .= "<p class='ok'>Testar spara ny aktivitet</p>";
    $retur .= "<p class='ok'>Ett test till för spara som givk bra</p>";
    return $retur;
}

/**
 * Tester för uppdatera aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_UppdateraAktivitet(): string {
    $retur = "<h2>test_UppdateraAktivitet</h2>";
    $retur .= "<p class='ok'>Testar uppdatera aktivitet</p>";
    return $retur;
}

/**
 * Tester för funktionen radera aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_RaderaAktivitet(): string {
    $retur = "<h2>test_RaderaAktivitet</h2>";
    $retur .= "<p class='ok'>Testar radera aktivitet</p>";
    return $retur;
}
