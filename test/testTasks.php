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
    $svar = hamtaDatum($datum1, $datum2);
     if($svar->getStatus()===200) {
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
    $svar = hamtaDatum($datum1, $datum2);
    if($svar->getStatus()===400) {
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
    /*
    $db= connectDb();
    $db->beginTransaction();
    hamtaEnskildUppgift(2);
    $db->rollBack();
    return $retur;
    */
    try {
        // Testa negativt tal
        $svar = hamtaEnskildUppgift(-1);
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Hämta enskild med negativt tal ger förväntat svar 400</p>";
        } else {
            $retur .= "<p class='error'>Hämta enskild med negativt tal ger {$svar->getStatus()}"
                    . "inte förväntat svar 400</p>";
        }
        // Testa för stort tal
        $svar = hamtaEnskildUppgift(1000);
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Hämta enskild med för stort tal ger förväntat svar 400</p>";
        } else {
            $retur .= "<p class='error'>Hämta enskild med för stort tal ger {$svar->getStatus()}"
                    . "inte förväntat svar 400</p>";
        }
        // Testa bokstäver
        $svar = hamtaEnskildUppgift((int) "sju");
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Hämta enskild med bokstäver ger förväntat svar 400</p>";
        } else {
            $retur .= "<p class='error'>Hämta enskild med bokstäver tal ger {$svar->getStatus()}"
                    . "inte förväntat svar 400</p>";
        }
        // Testa giltigt tal
        $svar = hamtaEnskildUppgift(3);
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
 * Test för funktionen spara uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_SparaUppgift(): string {
    $retur = "<h2>test_SparaUppgift</h2>";
    try {
    // Testa allt OK
    $igar=new DateTimeImmutable("yesterday");
    $imorgon=new DateTimeImmutable("tomorrow");
    
    $db= connectDb();
    $db->beginTransaction();
    $postdata=["date"=>$igar->format('Y-m-d'),
        "time"=>"05:00",
        "activityId"=>1,
        "description"=>"Hurra vad bra"];
    
    $svar= sparaNyUppgift($postdata);
    if($svar->getStatus()===200) {
        $retur .="<p class='ok'>Spara ny uppgift lyckades</p>";
    } else {
        $retur .= "<p class='error'>Spara ny uppgift misslyckades {$svar->getStatus()} <br> returnerades istället för förväntat 200</p>";
    }
    $db->rollBack();
    
    } catch (Exception $ex) {
        $retur .=$ex->getMessage();
    }
    
    return $retur;
}

/**
 * Test för funktionen uppdatera befintlig uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_UppdateraUppgift(): string {
    $retur = "<h2>test_UppdateraUppgift</h2>";
    try {
    // Testa allt ok
        
    $db= connectDb();
    $db->beginTransaction();
    $postdata=["date"=>date('Y-m-d'),
        "time"=>"05:00",
        "activityId"=>1,
        "description"=>"Hurra vad bra"];
    $id = 1;
    
    $svar= uppdateraUppgift($id, $postdata);
    if($svar->getStatus()===200) {
        $retur .="<p class='ok'>Uppdatera uppgift lyckades</p>";
    } else {
        $retur .= "<p class='error'>Uppdatera uppgift misslyckades {$svar->getStatus()} <br> returnerades istället för förväntat 200</p>";
    }
    $db->rollBack();
        
    } catch (Exception $ex) {
        $retur .=$ex->getMessage();
    }
    return $retur;
}

function test_KontrolleraIndata(): string {
    $retur = "<h2>test_KontrolleraIndata</h2>";
   try {
       
    $igar=new DateTimeImmutable("yesterday");
    $imorgon=new DateTimeImmutable("tomorrow");
    
    $postdata=["date"=>$igar->format('Y-m-d'),
        "time"=>"05:00",
        "activityId"=>1,
        "description"=>"Hurra vad bra"];
    $db= connectDb();
    
    // Testa felaktigt datum (i morgon) => 400
    $postdata["date"]=$imorgon->format("Y-m-d");
    $db->beginTransaction();
    $svar= sparaNyUppgift($postdata);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift med datum imorgon misslyckades som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Spara ny uppgift med datum imorgon returnerarde {$svar->getStatus()} <br> returnerades istället för förväntat 400</p>";
    }
    $db->rollBack();
    
    // Testa felaktigt datumformat => 400
    $postdata["date"]=$igar->format("d.m.Y");
    $db->beginTransaction();
    $svar= sparaNyUppgift($postdata);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift med felaktigt datumformat misslyckades som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Spara ny uppgift med felaktigt datumformat returnerarde {$svar->getStatus()} <br> returnerades istället för förväntat 400</p>";
    }
    $db->rollBack();
    // Testa datum saknas => 400
    unset($postdata["date"]);
    $db->beginTransaction();
    $svar= sparaNyUppgift($postdata);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift utan datum misslyckades som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Spara ny uppgift utan datum returnerarde {$svar->getStatus()} <br> returnerades istället för förväntat 400</p>";
    }
    $db->rollBack();
    
    // Testa felaktig tid (12 timmar) => 400
    $db->beginTransaction();
    $postdata["date"]=$igar->format("Y-m-d");
    $postdata["time"]="12:00";
    $svar= sparaNyUppgift($postdata);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift med felaktig tid (12:00) misslyckades som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Spara ny uppgift med felaktig tid (12:00) returnerarde {$svar->getStatus()} <br> returnerades istället för förväntat 400</p>";
    }
    $db->rollBack();
    
    // Testa felaktigt tidsformat => 400
    $postdata["time"]="5_30";
    $db->beginTransaction();
    $svar= sparaNyUppgift($postdata);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift med felaktigt tidsformat misslyckades som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Spara ny uppgift med felaktigt tidsformat returnerarde {$svar->getStatus()} <br> istället för förväntat 400</p>";
    }
    $db->rollBack();
    
    // Testa tid saknas => 400
    $db->beginTransaction();
    unset($postdata["time"]);
    $svar= sparaNyUppgift($postdata);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift utan tid misslyckades som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Spara ny uppgift utan tid returnerarde {$svar->getStatus()} <br> istället för förväntat 400</p>";
    }
    $db->rollBack();
    // Testa description saknas => 200
    unset($postdata["description"]);
    $postdata["time"]="3:15";
    $db->beginTransaction();
    $svar= sparaNyUppgift($postdata);
    if($svar->getStatus()===200) {
        $retur .="<p class='ok'>Spara ny uppgift utan beskrivning lyckades som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Spara ny uppgift utan beskrivning returnerarde {$svar->getStatus()} <br> istället för förväntat 200</p>";
    }
    $db->rollBack();
    
    // Testa aktivitetsid felaktigt (-1) => 400
    
    $postdata["activityId"]=-1;
    $db->beginTransaction();
    $svar= sparaNyUppgift($postdata);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift med felaktigt id misslyckades som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Spara ny uppgift med felaktigt id returnerarde {$svar->getStatus()} <br> istället för förväntat 400</p>";
    }
    $db->rollBack();
    
    // Testa aktivitetsid som saknas (100) => 400
    $postdata["activityId"]=100;
    $db->beginTransaction();
    $svar= sparaNyUppgift($postdata);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift med id som saknas misslyckades som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Spara ny uppgift med id som saknas returnerarde {$svar->getStatus()} <br> istället för förväntat 400</p>";
    }
    $db->rollBack();
    
    } catch (Exception $ex) {
        $retur .=$ex->getMessage();
    }
    
    return $retur;
    
}
/**
 * Test för funktionen radera uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_RaderaUppgift(): string {
    $retur = "<h2>test_RaderaUppgift</h2>";
    try {
    // Testa ogiltigt tal (-1)
    $svar = raderaUppgift(-1);
    if ($svar->getStatus()===400) {
        $retur .="<p class='ok'>Radera uppgift med ogiltigt tal returenare 400 som förväntat</p>";
    } else {
        $retur .="<p class='error'>Radera uppgift returnerade {$svar->getStatus()}<br> Istället för förväntat 400</p>";
    }
        
    
    // Testa ta bort post som finns
    $db = connectDb();
    $db->beginTransaction();
    $postData=["date"=>date('Y-m-d'),
        "time"=>"05:00",
        "activityId"=>1,
        "description"=>"Hurra vad bra"];
    $svar=sparaNyUppgift($postData);
    if($svar->getStatus()!==200){
        throw new Exception("Kunde inte skapa ny post, testerna avbryts!");
    }
    $nyttId=(int)$svar->getContent()->id;
    $svar= raderaUppgift($nyttId);
    if($svar->getStatus()===200){
        if($svar->getContent()->result===true) {
            $retur .="<p class='ok'>Radera uppgift lyckades</p>";
        } else {
            $retur .="<p class='error'>Radera uppgift returnerade false istället för "
                    . "förväntat true </p>";
        }
    } else {
        $retur .="<p class='error'>Radera uppgift returnerade {$svar->getStatus()} istället"
        . " för förväntat 200";
    }
    
    // Testa ta bort post som inte finns
    $svar= raderaUppgift($nyttId);
    if($svar->getStatus()===200){
        if($svar->getContent()->result===false) {
            $retur .="<p class='ok'>Radera uppgift som inte finns misslyckades</p>";
        } else {
            $retur .="<p class='error'>Radera uppgift returnerade true istället för "
                    . "förväntat false </p>";
        }
    } else {
        $retur .="<p class='error'>Radera uppgift returnerade {$svar->getStatus()} istället"
        . " för förväntat 200";
    }
    
    } catch (\Exception $ex) {
        $retur .="<p class='error'>Något gick fel: {$ex->getMessage()}</p>";
    }
    return $retur;
}
