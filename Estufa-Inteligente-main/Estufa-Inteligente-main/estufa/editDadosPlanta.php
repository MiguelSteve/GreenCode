<?php
    session_start();

    #verificar se a variável de sessão chamada 'id_usuario' está definida
    if (isset($_SESSION['id_usuario'])) {
        $id_usuario = $_SESSION['id_usuario'];
        $umidade = $_POST ['campoUmidade'];
        $temperatura = $_POST ['campoTemperatura'];
        $servidor = "localhost";
        $usuario = "root";
        $senha = "";
        $dbname = "estufa";
    
        $conn = mysqli_connect($servidor,$usuario,$senha,$dbname);
    
        $result_usuario = "UPDATE estufa SET umidade = '$umidade', temperatura = '$temperatura' WHERE id_usuario = $id_usuario";
        $result_usuario = mysqli_query($conn, $result_usuario);
        echo "Dados da planta editada com sucesso!";
    }
    else {
        echo "ID de usuário não definido na sessão.";
    }
    
?>