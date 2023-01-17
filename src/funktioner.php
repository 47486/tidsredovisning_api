<?php

declare (strict_types=1);

function connectDb():PDO {
    $dsn = 'mysql:dbname=tidsredovisning;host=localhost;port=3307';
    $dbUser='root';
    $dbPassword="";
    $db = new PDO($dsn, $dbUser, $dbPassword);
    
    return $db;
}