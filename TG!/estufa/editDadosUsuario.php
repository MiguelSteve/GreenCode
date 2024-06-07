<?php
session_start();

if (isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $oldPassword = $_POST['campoSenhaAntiga'];
    $newPassword = $_POST['campoNovaSenha'];
    $servidor = "177.153.63";
    $usuario = "estufa";
    $senha = "TgF@da3";
    $dbname = "estufa";

    $conn = mysqli_connect($servidor, $usuario, $senha, $dbname);

    $sql = "SELECT senha FROM usuario WHERE id_usuario = $id_usuario";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $senhaAtual = $row['senha'];

        if ($oldPassword == $senhaAtual) {

            $result_usuario = "UPDATE usuario SET senha = '$newPassword' WHERE id_usuario = $id_usuario";
            #consultar o resultado para ver se foi alterado com sucesso
            $result_usuario = mysqli_query($conn, $result_usuario);
            echo "Senha alterada com sucesso!";
        } else {
            echo "Senha incorreta!";
        }
    } else {
        echo "Erro ao buscar a senha atual do usuário.";
    }
} else {
    echo "ID de usuário não definido na sessão.";
}

?>