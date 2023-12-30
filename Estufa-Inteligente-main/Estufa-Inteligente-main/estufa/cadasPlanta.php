<?php

include('protect.php');
include('connection.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Cadastrar Planta</title>

</head>
<body class="body-planta" style="background-image: url(fundoLogin/cadas-planta.png);">

<div class="div-cadas-planta">
        <form class="form-cadas-planta" method="POST" enctype="multipart/form-data">
            <h1>CADASTRAR PLANTA</h1>
            <div class = "div-cadas-planta-aviso">
      
            <?php
session_start();
include_once "conexao.php";

if (isset($_SESSION['id_usuario'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Receber dados do formulário
        $id_usuario = $_SESSION['id_usuario'];
        $dat = $_POST['campoData'];
        $nSerie = $_POST['campoSerie'];
        $arquivo = $_FILES['foto_planta'];

        try {
            // Criar uma conexão PDO
            $conn = new PDO("mysql:host=localhost;dbname=estufa", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Consultar o banco de dados para verificar se o número de série fornecido existe
            $checkQuery = $conn->prepare("SELECT n_serie FROM usuario WHERE n_serie = :nSerie");
            $checkQuery->bindParam(':nSerie', $nSerie, PDO::PARAM_STR);
            $checkQuery->execute();
            $result = $checkQuery->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                if (isset($_POST['categoria'])) {
                    $opcaoSelecionada = $_POST['categoria'];

                    $stmt = $conn->prepare("SELECT * FROM planta WHERE id_planta = :opcaoSelecionada");
                    $stmt->bindParam(':opcaoSelecionada', $opcaoSelecionada);
                    $stmt->execute();
                    $dadosOpcao = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($dadosOpcao) {
                        $stmt = $conn->prepare("INSERT INTO estufa (id_usuario, data_criacao, imagem, nome, umidade, temperatura) VALUES (:id_usuario, :data_criacao, :imagem, :nome, :umidade, :temperatura)");
                        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                        $stmt->bindParam(':data_criacao', $dat, PDO::PARAM_STR);
                        $stmt->bindParam(':nome', $dadosOpcao['nome_planta']);
                        $stmt->bindParam(':umidade', $dadosOpcao['umidade_ideal']);
                        $stmt->bindParam(':temperatura', $dadosOpcao['temperatura_ideal']);
                        $stmt->bindParam(':imagem', $arquivo['name'], PDO::PARAM_STR);

                        if ($stmt->execute()) {
                            // Move a lógica para criar a pasta e mover a imagem aqui
                            if ((isset($arquivo['name'])) && !empty($arquivo['name'])) {
                                $ultimo_id = $conn->lastInsertId();
                                $diretorio = "planta/$ultimo_id/";
                                mkdir($diretorio, 0755);
                                $nome_arquivo = $arquivo['name'];
                                move_uploaded_file($arquivo['tmp_name'], $diretorio . $nome_arquivo);


                                /*gerar arquivo txt*/
                                $file_plant = fopen("planta.txt","a");

                                
                                    fwrite($file_plant, $opcaoSelecionada."\n");                                  
                        
                        }

                            echo "<dialog id='msgSucesso' open>
                                <center><img src=fundoLogin/sucesso.png></center>
                                <p>Cadastro realizado com sucesso!</p>
                                <a href=telaPrincipal.php><input type=button value=VOLTAR name=btnVoltar></a>
                            </dialog>";

                        } else {
                            echo "Erro ao inserir dados da opção na tabela 'estufa'.";
                        }
                    } else {
                        echo "Opção não encontrada na tabela 'planta'.";
                    }
                }
            } else {
                echo "*Erro: Número de Série incorreto!";
            }
        } catch (PDOException $erro) {
            echo "Erro na conexão com o banco de dados: " . $erro->getMessage();
        }
    } else {
        echo "";
    }
} else {
    echo "Erro: Usuário não está logado";
}
?>

      
            </div>
            <div class="campos-planta">
            <!--ocupa metade do formulario (half-box)-->
            <br>
            <label for="foto_planta">Adicionar foto da planta: </label>
            <input type="file" name="foto_planta" id="foto_planta" required><br><br>

            <div class="tooltip"> <!-- O ponto de interrogação -->
            <abbr><img src="fundoLogin/ajuda.png" alt="Ícone de ajuda"></abbr>
            <span class="tooltiptext">Selecione a planta desejada e, após a escolha, será automaticamente detectada a temperatura e umidade ideais para seu cultivo.</span>
            </div>

            <select name="categoria" class = "catergoria" required>
        <option value="">Selecione a planta</option>
        <?php
            $query = $conn->query("SELECT * FROM planta ORDER BY nome_planta ASC");
            $registros = $query->fetchAll(PDO::FETCH_ASSOC);
            print_r($registros);
        
            foreach($registros as $options){
        ?>
        <option value="<?php echo $options['id_planta']?>">
        <?php echo $options['nome_planta']?>
        </option>
        <?php }?>

            <div class="half-box spacing">
              N° Série <input type="text" name="campoSerie" placeholder="N° Série">
            </div>

            <div class="half-box">
                Data que foi plantado: <input type="date" name="campoData" id="lastname" placeholder="Digite a data que foi plantado">
            </div>

              <div class="full-box">
                <input type="submit" id="btn_cadastrar" value="CADASTRAR">
              </div>


              <div class="div-voltar">
                <img class = "img-voltar" src="fundoLogin/voltar.png" alt="Ícone de saída">
                <a href="telaPrincipal.php">Voltar</a>
            </div>

            </div>
        </form>
    </div></body>
</html>
