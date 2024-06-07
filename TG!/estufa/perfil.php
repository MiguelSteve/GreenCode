<?php
include('protect.php');
# Fazer conexão com BD
try
{
    # Conexão com MySQL usando PDO
    $conectaBD = new PDO("mysql:host=177.153.63;port=3306;dbname=estufa", "estufa", "TgF@da3");
    $conectaBD->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    # Preparar a consulta SQL
    $sql = "SELECT * FROM usuario";

    # Preparar e executar a consulta
    $stmt = $conectaBD->query($sql);

}
catch(PDOException $erro)
{
    # Informar que houve erro ao fazer a conexão com BD
    echo "Houve erro ao fazer a conexão com o banco de dados!";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Meu Perfil</title>
</head>
<body class = "body-perfil" style="background-image: url(fundoLogin/perfil2.png);">

<center>
        <div class = "div-painel-perfil">
            <?php
                    $id_usuario = $_SESSION['id_usuario'];
                    $sql = "SELECT * FROM usuario WHERE id_usuario = :id_usuario";

                    # Preparar e executar a consulta com a cláusula WHERE
                    $stmt = $conectaBD->prepare($sql);
                    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                    $stmt->execute();

                    while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $foto_usuario = $dados["imagem"];
                    echo "<img src='usuario/$id_usuario/$foto_usuario'>","<br>";
                    }
            ?>
            
                        <?php
                    $id_usuario = $_SESSION['id_usuario'];
                    $sql = "SELECT * FROM usuario WHERE id_usuario = :id_usuario";

                    # Preparar e executar a consulta com a cláusula WHERE
                    $stmt = $conectaBD->prepare($sql);
                    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                    $stmt->execute();

                    while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<h1>",$dados["nome"],"</h1>"; 
                    echo "<h5>",$dados["email"],"</h5>"; 
                    }
            ?>

                <form action="" method="post">
                    <div class="senha">
                        
                <br>EFETUAR ALTERAÇÃO DA SENHA:
                    </div>
                    <!--alterar senha-->
                    <?php
                    if (isset($_SESSION['id_usuario'])) {
                        if (isset($_POST['campoSenhaAntiga']) && isset($_POST['campoNovaSenha'])) {
                            $id_usuario = $_SESSION['id_usuario'];
                            $oldPassword = $_POST['campoSenhaAntiga'];
                            $newPassword = $_POST['campoNovaSenha'];
                            $servidor = "localhost";
                            $usuario = "root";
                            $senha = "";
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
                                    echo "<dialog id='msgSucessoPerfil' open>
                                    <center><img src=fundoLogin/sucesso.png></center>
                                    <br>Senha alterada com sucesso!
                                    <a href=telaPrincipal.php><input type=button value=VOLTAR name=btnVoltar></a>
                                </dialog>";
                            } else {
                                echo "<div class=div-senha> Campos ainda não preenchidos ou senha incorreta! </div>";
                            }
                        } else {
                            echo "Erro ao buscar a senha atual do usuário.";
                        }
                    }
                    } else {
                        echo "ID de usuário não definido na sessão.";
                    }

                    ?>

                <br>Senha atual: <input type="password" name="campoSenhaAntiga" placeholder="Senha">
                <br>Nova senha: <input type="password" name="campoNovaSenha" placeholder="Nova senha">
                <div class="btn-senha">
                <br><input class = "btn-mudar-senha" type="submit" value="MUDAR SENHA">
                </div>
                </form>                    

                <a href="telaPrincipal.php"><button class = "voltar">Voltar</buttom></a>


    </div>
</center>
</body>
</html>
