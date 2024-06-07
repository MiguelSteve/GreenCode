<?php

$usuario = 'estufa';
$senha = 'TgF@da3';
$database = 'estufa';
$host = '177.153.63.45';

$mysqli = new mysqli($host, $usuario, $senha, $database);

if($mysqli->error) {
    die("Falha ao conectar ao banco de dados: " . $mysqli->error);
}

