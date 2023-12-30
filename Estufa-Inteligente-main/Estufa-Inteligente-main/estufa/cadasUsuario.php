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
    $nSerie = $_POST['campoSerie'];
    $email = $_POST['campoEmail'];

    try {
        if($senha == $confirmSenha){
        // Criar uma conexão PDO
        $conn = new PDO("mysql:host=localhost;dbname=estufa", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // verificar se o email e n serie existem
        $checkQuery = $conn->prepare("SELECT n_serie, email_produto FROM produto WHERE n_serie = :nSerie AND email_produto = :email");
        $checkQuery->bindParam(':nSerie', $nSerie, PDO::PARAM_STR);
        $checkQuery->bindParam(':email', $email, PDO::PARAM_STR);
        $checkQuery->execute();
        $result = $checkQuery->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // se o email e senha estao corretos inserir dados
            $query_usuario = "INSERT INTO usuario (nome, n_serie, email, senha, imagem) VALUES (:nome, :n_serie, :email, :senha, :imagem)";
            $cad_usuario = $conn->prepare($query_usuario);
            $cad_usuario->bindParam(':nome', $nome, PDO::PARAM_STR);
            $cad_usuario->bindParam(':n_serie', $nSerie, PDO::PARAM_STR);
            $cad_usuario->bindParam(':email', $email, PDO::PARAM_STR);
            $cad_usuario->bindParam(':senha', $senha, PDO::PARAM_STR);
            $cad_usuario->bindParam(':imagem', $arquivo['name'], PDO::PARAM_STR);

            if ($cad_usuario->execute()) {
                // deu certo
                if ((isset($arquivo['name'])) && !empty($arquivo['name'])) {
                    $ultimo_id = $conn->lastInsertId();
                    $diretorio = "usuario/$ultimo_id/";
                    mkdir($diretorio, 0755);
                    $nome_arquivo = $arquivo['name'];
                    move_uploaded_file($arquivo['tmp_name'], $diretorio . $nome_arquivo);
                    echo "<dialog id='msgSucesso' open>
                                <center><img src=fundoLogin/sucesso.png></center>
                                <p>Cadastro realizado com sucesso!</p>
                                <a href=index.php><input type=button value=VOLTAR name=btnVoltar></a>
                            </dialog>";
                } else {
                    echo "Cadastro realizado com sucesso.";
                }
            } else {
                echo "Erro ao cadastrar o usuário.";
            }
        } else {
            echo "*Erro: Email ou Número de Série incorretos.";
        }}else{
            echo "*Erro: Senhas diferentes!";
        }
    } catch (PDOException $erro) {
        echo "Erro na conexão com o banco de dados: " . $erro->getMessage();
    }
} else {
    echo "";
}
?>
        </div>


        <label for="foto_usuario">Foto: </label>
        <input type="file" name="foto_usuario" id="foto_usuario" required><br><br>
        
        <div class="full-box">
            E-mail: <input type="text" name="campoEmail" id="email" placeholder="Digite o seu e-mail" data-min-length="3" data-required data-email-validate>
        </div>

        <!--ocupa metade do formulario (half-box)-->
        <div class="full-box spacing">
            Nome: <input type="text" name="campoNome" id="name" placeholder="Digite o seu nome" data-max-length="16" data-only-letters>
        </div>

        <div class="full-box spacing">
            Senha: <input type="password" name="campoSenha" id="password" placeholder="Digite a sua senha" data-required data-password-validate>
        </div>

        <div class="full-box">
            Confirmação senha: <input type="password" name="campoConfirmSenha" id="passconfirmation" placeholder="Confirme a sua senha" data-equal="password" data-required>
        </div>

        <div class="full-box">
            N° Série: <input type="text" name="campoSerie" id="passconfirmation" placeholder="N° Serie" data-equal="password" data-required>
        </div>

        <div class="full-box">
            <input type="submit" id="btn-submit" value="CADASTRAR">
        </div>

        <div class="div-voltar">
                <img src="fundoLogin/voltar.png" alt="Ícone de saída">
                <a href="index.php">Voltar</a>
            </div>    </form>
</div>

</body>
</html>
