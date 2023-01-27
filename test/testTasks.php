<?php

declare (strict_types=1);
require_once __DIR__ . '/../src/tasks.php';

/**
 * Funktion för att testa alla aktiviteter
 * @return string html-sträng med resultatet av alla tester
 */
function allaTaskTester(): string {
// Kom ihåg att lägga till alla testfunktioner
    $retur = "<h1>Testar alla uppgiftsfunktioner</h1>";
    $retur .= test_HamtaEnUppgift();
    $retur .= test_HamtaUppgifterSida();
    $retur .= test_RaderaUppgift();
    $retur .= test_SparaUppgift();
    $retur .= test_UppdateraUppgifter();
    return $retur;
}

/**
 * Funktion för att testa en enskild funktion
 * @param string $funktion namnet (utan test_) på funktionen som ska testas
 * @return string html-sträng med information om resultatet av testen eller att testet inte fanns
 */
function testTaskFunction(string $funktion): string {
    if (function_exists("test_$funktion")) {
        return call_user_func("test_$funktion");
    } else {
        return "<p class='error'>Funktionen $funktion kan inte testas.</p>";
    }
}

/**
 * Tester för funktionen hämta uppgifter för ett angivet sidnummer
 * @return string html-sträng med alla resultat för testerna 
 */
function test_HamtaUppgifterSida(): string {
    $retur = "<h2>test_HamtaUppgifterSida</h2>";
    try {
    // Testa hämta felaktigt sidnummer (-1)=> 400
        $svar = hamtaSida(-1);
        if($svar->getStatus()===400) {
            $retur .= "<p class='ok'>Hämta felaktigt sidnummer (-1) gav förväntat svar 400</p>";
        } else {
            $retur .= "<p class='error'>Hämta felaktigt sidnummer (-1) gav oförväntat svar {$svar->getStatus()}<br> istället för förväntat svar 400</p>";
        }
    
    // Testa hämta giltigt sidnummer (1) => 200 + rätt egenskaper
        $svar = hamtaSida(1);
        if($svar->getStatus()!==200) {
            $retur .= "<p class='error'>Hämta rätt sidnummer (1) gav {$svar->getStatus()} <br> istället för förväntat svar 200 </p>";
        } else {
            $retur .= "<p class='ok'>Hämta giltigt sidnummer (1) gav förväntat svar 200</p>";
            //var_dump($svar->getContent());
            $result=$svar->getContent()->tasks;
            foreach ($result as $task) {
                if(!isset($task->id)) {
                    $retur .="<p class='error'>Egenskapen id saknas</p>";
                    break;
                }
                if(!isset($task->activityId)) {
                    $retur .="<p class='error'>Egenskapen activityId saknas</p>";
                    break;
                }
                if(!isset($task->activity)) {
                    $retur .="<p class='error'>Egenskapen activity saknas</p>";
                    break;
                }
                if(!isset($task->date)) {
                    $retur .="<p class='error'>Egenskapen date saknas</p>";
                    break;
                }
                if(!isset($task->time)) {
                    $retur .="<p class='error'>Egenskapen time saknas</p>";
                    break;
                }
            }
        }
    
    // Testa hämta för stor sidnr => 200 + tom array
        
        $svar = hamtaSida(200);
        if($svar->getStatus()===200) {
            $retur .= "<p class='ok'>Hämta för stort sidnummer (200) gav förväntat svar 200</p>";
            $resultat = $svar->getContent()->tasks;
            if(!$resultat===[]) {
                $retur .= "<p class='error'>Hämta för stort sidnummer ska innehålla en tom array för tasks<br>" . print_r($resultat, true) . " <br>returnerades</p>";
            }
        } else {
            $retur .= "<p class='error'>Hämta felaktigt sidnummer (-1) gav oförväntat svar {$svar->getStatus()}<br> istället för förväntat svar 400</p>";
        }
    
    } catch (Exception $ex) {
        $retur .= "<p class='Något gick fel, meddelandet säger: '>{$ex->getMessage()}</p>";
    }
    return $retur;
}

/**
 * Test för funktionen hämta uppgifter mellan angivna datum
 * @return string html-sträng med alla resultat för testerna
 */
function test_HamtaAllaUppgifterDatum(): string {
    $retur = "<h2>test_HamtaAllaUppgifterDatum</h2>";
    // Testa fel ordning på datum => 400
    $datum1=new dateTimeImmutable();
    $datum2=new dateTime("yesterday");
    $svar = hamtaDatum($datum1, $datum2);
    if($svar->getStatus()===400) {
        $retur .= "<p class='ok'>Hämta fel ordning på datum gav förväntat svar 400</p>";
    } else {
        $retur .= "<p class='error'>Hämta fel ordning på datum gav {$svar->getStatus()} <br> förväntat svar 400</p>";
    }
    
    // Testa datum utan poster => 200 och tom array för tasks
    $datum1=new dateTimeImmutable("1970-01-01");
    $datum2=new dateTimeImmutable("1970-01-01");
     if($svar->getStatus()===200) {
         var_dump($svar);
            $retur .= "<p class='ok'>Hämta för stort sidnummer (200) gav förväntat svar 200</p>";
            $resultat = $svar->getContent()->tasks;
            if(!$resultat===[]) {
                $retur .= "<p class='error'>Hämta för stort sidnummer ska innehålla en tom array för tasks<br>" . print_r($resultat, true) . " <br>returnerades</p>";
            }
        } else {
            $retur .= "<p class='error'>Hämta datum utan poster gav oförväntat svar {$svar->getStatus()}<br> istället för förväntat svar 200</p>";
        }
    // Testa giltigt datum med poster => 200 och giltiga egenskaper
    $datum1=new dateTimeImmutable("1970-01-01");
    $datum2=new dateTimeImmutable();
    if($svar->getStatus()===400) {
            var_dump($svar);
            $retur .= "<p class='error'>Hämta giltigt datum gav {$svar->getStatus()} <br> istället för förväntat svar 200 </p>";
        } else {
            $retur .= "<p class='ok'>Hämta giltigt datum gav förväntat svar 200</p>";
            $result=$svar->getContent()->tasks;
            foreach ($result as $task) {
                if(!isset($task->id)) {
                    $retur .="<p class='error'>Egenskapen id saknas</p>";
                    break;
                }
                if(!isset($task->activityId)) {
                    $retur .="<p class='error'>Egenskapen activityId saknas</p>";
                    break;
                }
                if(!isset($task->activity)) {
                    $retur .="<p class='error'>Egenskapen activity saknas</p>";
                    break;
                }
                if(!isset($task->date)) {
                    $retur .="<p class='error'>Egenskapen date saknas</p>";
                    break;
                }
                if(!isset($task->time)) {
                    $retur .="<p class='error'>Egenskapen time saknas</p>";
                    break;
                }
            }
        }
    
    return $retur;
}

/**
 * Test av funktionen hämta enskild uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_HamtaEnUppgift(): string {
    $retur = "<h2>test_HamtaEnUppgift</h2>";
    $retur .= "<p class='ok'>Testar hämta en uppgift</p>";
    return $retur;
}

/**
 * Test för funktionen spara uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_SparaUppgift(): string {
    $retur = "<h2>test_SparaUppgift</h2>";
    $retur .= "<p class='ok'>Testar spara uppgift</p>";
    return $retur;
}

/**
 * Test för funktionen uppdatera befintlig uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_UppdateraUppgifter(): string {
    $retur = "<h2>test_UppdateraUppgifter</h2>";
    $retur .= "<p class='ok'>Testar uppdatera uppgift</p>";
    return $retur;
}

/**
 * Test för funktionen radera uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_RaderaUppgift(): string {
    $retur = "<h2>test_RaderaUppgift</h2>";
    $retur .= "<p class='ok'>Testar radera uppgift</p>";
    return $retur;
}
