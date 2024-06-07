<?php

$usuario = 'estufa';
$senha = 'TgF@da3';
$database = 'estufa';
$host = '177.153.63.45';
try{
    $conn = new PDO("mysql:host=$host;dbname=$database",$usuario,$senha);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
}
catch(Exception $e){
    echo $e->getMessage();
    exit;
}