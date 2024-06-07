<?php
$servername = "177.153.63.45";
$username = "estufa";
$password = "TgF@da3";
$dbname = "estufa";

// Cria conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT temperatura, umidade FROM estufa WHERE id_estufa = 12";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(array("temperatura" => null, "umidade" => null));
}

$conn->close();
?>
