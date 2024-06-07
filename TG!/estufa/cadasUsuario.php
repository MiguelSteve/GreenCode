<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/cadasUsuario.js"></script>
    <!--icon-->
    <link rel="shortcut icon" type="imagex/png" href="./images/icon.ico">
</head>
<body class="body-usuario" style="background-image: url(fundoLogin/cadas-planta.png);">

<div class="div-cadas-usuario">
    
    
    <form class="form-cadas-usuario" method="POST" action="" enctype="multipart/form-data">
        <!-- CARREGAR IMAGEM -->

        <h1>CRIAR CONTA</h1>
        <div class = "div-cadas-usuario-aviso">

        <?php
include_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_usuario = "";
    $nome = $_POST['campoNome'];
    $senha = $_POST['campoSenha'];
    $confirmSenha = $_POST['campoConfirmSenha'];
    $arquivo = $_FILES['foto_usuario'];
    $email = $_POST['campoEmail'];

    try {
        if($senha == $confirmSenha) {
            // Criar uma conexão PDO
            $conn = new PDO("mysql:host=177.153.63.45; dbname=estufa", "estufa", "TgF@da3");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // Verificar se o usuário já está cadastrado
            $query = "SELECT COUNT(*) FROM usuario WHERE email = :email";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetchColumn();
    
            if($row > 0) {
                echo "Usuário já cadastrado!";
            } else {
                // Inserir dados do usuário
                $query_usuario = "INSERT INTO usuario (nome, email, senha, imagem) VALUES (:nome, :email, :senha, :imagem)";
                $cad_usuario = $conn->prepare($query_usuario);
                $cad_usuario->bindParam(':nome', $nome, PDO::PARAM_STR);
                $cad_usuario->bindParam(':email', $email, PDO::PARAM_STR);
                $cad_usuario->bindParam(':senha', $senha, PDO::PARAM_STR);
                $cad_usuario->bindParam(':imagem', $arquivo['name'], PDO::PARAM_STR);
    
                if ($cad_usuario->execute()) {
                    // Se a execução deu certo
                    if ((isset($arquivo['name'])) && !empty($arquivo['name'])) {
                        $ultimo_id = $conn->lastInsertId();
                        $diretorio = "usuario/$ultimo_id/";
                        mkdir($diretorio, 0755);
                        $nome_arquivo = $arquivo['name'];
                        move_uploaded_file($arquivo['tmp_name'], $diretorio . $nome_arquivo);
                        echo "<dialog id='msgSucesso' open>
                                  <center><img src='fundoLogin/sucesso.png'></center>
                                  <p>Cadastro realizado com sucesso!</p>
                                  <a href='login.php'><input type='button' value='VOLTAR' name='btnVoltar'></a>
                              </dialog>";
                    } else {
                        echo "Cadastro realizado com sucesso.";
                    }
                } else {
                    echo "Erro ao cadastrar o usuário.";
                }
            }
        } else {
            echo "*Erro: Senhas diferentes!";
        }
    } catch (PDOException $erro) {
        echo "Erro na conexão com o banco de dados: " . $erro->getMessage();
}
}
?>
        </div>


        <label for="foto_usuario">Foto: </label>
        <input type="file" name="foto_usuario" id="foto_usuario" required><br><br>
        
        <div class="full-box">
            E-mail: <input type="text" name="campoEmail" id="email" placeholder="Digite o seu e-mail" data-min-length="3" data-required data-email-validate required>
        </div>

        <!--ocupa metade do formulario (half-box)-->
        <div class="full-box spacing">
            Nome: <input type="text" name="campoNome" id="name" placeholder="Digite o seu nome" data-max-length="16" required data-only-letters>
        </div>

        <div class="full-box spacing">
            Senha: <input type="password" name="campoSenha" id="password" placeholder="Digite a sua senha" required data-required data-password-validate>
        </div>

        <div class="full-box">
            Confirmação senha: <input type="password" name="campoConfirmSenha" id="passconfirmation" placeholder="Confirme a sua senha" data-equal="password" required data-required>
        </div>

        <div class="full-box">
            <input type="submit" id="btn-submit" value="CADASTRAR">
        </div>

        <div class="div-voltar">
                <img src="fundoLogin/voltar.png" alt="Ícone de saída">
                <a href="login.php">Voltar</a>
            </div>    </form>
</div>

</body>
</html>
