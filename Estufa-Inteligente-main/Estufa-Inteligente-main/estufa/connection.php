<?php

$usuario = 'root';
$senha = '';
$database = 'estufa';
$host = 'localhost';
try{
    $conn = new PDO("mysql:host=$host;dbname=$database",$usuario,$senha);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
}
catch(Exception $e){
    echo $e->getMessage();
    exit;
}