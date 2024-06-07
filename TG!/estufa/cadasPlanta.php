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


include('protect.php');
include('connection.php');

    if (isset($_SESSION['id_usuario'])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // receber dados inseridos pelo usuário
            $id_usuario = $_SESSION['id_usuario'];
            $dat = $_POST['campoData'];
            $nSerie = $_POST['campoSerie'];
            $arquivo = $_FILES['foto_planta'];
            
            try {
                // conexao bd
                $conn = new PDO("mysql:host=177.153.63.45; dbname=estufa", "estufa", "TgF@da3");
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // verificar se n serie existe
                $checkQuery = $conn->prepare("SELECT n_serie, email_produto FROM produto WHERE n_serie = :nSerie");
                $checkQuery->bindParam(':nSerie', $nSerie, PDO::PARAM_STR);
                $checkQuery->execute();
                $resultProduto = $checkQuery->fetch(PDO::FETCH_ASSOC);
                
                if (!$resultProduto) {
                    die("Número de série não encontrado na tabela de produtos.");
                }
                
                // encontrar o email do usuario na tb usuario
                $emailProduto = $resultProduto['email_produto'];
                
                $checkQuery = $conn->prepare("SELECT email FROM usuario WHERE id_usuario = :id_usuario");
                $checkQuery->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $checkQuery->execute();
                $resultUsuario = $checkQuery->fetch(PDO::FETCH_ASSOC);
                
                if (!$resultUsuario) {
                    die("Usuário não encontrado na tabela de usuários.");
                }
                
                $emailUsuario = $resultUsuario['email'];
                
                // comparar email (tb usuario) com email_produto (tb produto)
                if ($emailProduto == $emailUsuario) {

                    // verificar se usuario ja cadastrou a planta
                    $checkQuery = $conn->prepare("SELECT * FROM estufa WHERE n_serie = :nSerie");
                    $checkQuery->bindParam(':nSerie', $nSerie, PDO::PARAM_STR);
                    $checkQuery->execute();
                    $resultPlantaExistente = $checkQuery->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$resultPlantaExistente) {
                        // cadastrar se ainda nao tem planta na estufa
                        $opcaoSelecionada = $_POST['categoria'];
                        
                        $stmt = $conn->prepare("SELECT * FROM planta WHERE id_planta = :opcaoSelecionada");
                        $stmt->bindParam(':opcaoSelecionada', $opcaoSelecionada, PDO::PARAM_INT);
                        $stmt->execute();
                        $dadosOpcao = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($dadosOpcao) {
                            $stmt = $conn->prepare("INSERT INTO estufa (id_usuario, n_serie, data_criacao, imagem, nome, umidade, temperatura) VALUES (:id_usuario, :n_serie, :data_criacao, :imagem, :nome, :umidade, :temperatura)");
                            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                            $stmt->bindParam(':n_serie', $nSerie, PDO::PARAM_STR);
                            $stmt->bindParam(':data_criacao', $dat, PDO::PARAM_STR);
                            $stmt->bindParam(':nome', $dadosOpcao['nome_planta']);
                            $stmt->bindParam(':umidade', $dadosOpcao['umidade_ideal']);
                            $stmt->bindParam(':temperatura', $dadosOpcao['temperatura_ideal']);
                            $stmt->bindParam(':imagem', $arquivo['name'], PDO::PARAM_STR);

                            
                            if ($stmt->execute()) {
                                // salvar imagem
                                if ((isset($arquivo['name'])) && !empty($arquivo['name'])) {
                                    $ultimo_id = $conn->lastInsertId();
                                    $diretorio = "planta/$nSerie/";

                                    // verificar se existe o arquivo
                                    if (!file_exists($diretorio)) {
                                        mkdir($diretorio, 0755, true); // criar o arquivo
                                    }

                                    $nome_arquivo = $arquivo['name'];
                                    move_uploaded_file($arquivo['tmp_name'], $diretorio . $nome_arquivo);

                                    /* gerar arquivo txt */
                                    $file_plant = fopen("dadosEstufas/estufa - $nSerie.txt", "a");
                                    if ($file_plant) {
                                        fwrite($file_plant, $opcaoSelecionada . "\n");
                                        fclose($file_plant);
                                    }}                                
                                
                                echo "<dialog id='msgSucesso' open>
                                        <center><img src='fundoLogin/sucesso.png'></center>
                                        <p>Cadastro realizado com sucesso!</p>
                                        <a href='telaPrincipal.php'><input type='button' value='VOLTAR' name='btnVoltar'></a>
                                    </dialog>";
                            } else {
                                echo "Erro ao inserir dados da opção na tabela 'estufa'.";
                            }
                        } else {
                            echo "Opção não encontrada na tabela 'planta'.";
                        }
                    } else {
                        echo "<dialog id='msgSucesso' open>
                                <center><img src='fundoLogin/sucesso.png'></center>
                                <p>Estufa já possui planta!</p>
                                <a href='telaPrincipal.php'><input type='button' value='VOLTAR' name='btnVoltar'></a>
                                <a href='alteraPlanta.php'><input type='button' value='EDITAR PLANTA' name='btnVoltar'></a>
                            </dialog>";
                    }
                } else { 
                    echo "*Erro: O email do usuário não corresponde ao email associado ao número de série.";
                }
            } catch (PDOException $erro) {
                echo "Erro na conexão com o banco de dados: " . $erro->getMessage();
            }
            
        }
    } else {
        echo "Erro: Usuário não está logado.";
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
              N° Série <input type="text" name="campoSerie" placeholder="N° Série" required>
            </div>

            <div class="half-box">
                Data que foi plantado: <input type="date" name="campoData" id="lastname" placeholder="Digite a data que foi plantado" required>
            </div>

    <!-- MIGUEL VERIFICAR ESSE CODIGO-->
    <script>
    function Enviar(){
    alert("Planta Atualizada com sucesso");
    }
    </script>
        <form class="form-atualiza-dados" method="POST" action="http://localhost:3000/EnviarDados" onsubmit="Enviar()">
              <div class="full-box">
                <input type="submit" id="btn_cadastrar" value="CADASTRAR">
              </div>
        </form>

              <div class="div-voltar">
                <img class = "img-voltar" src="fundoLogin/voltar.png" alt="Ícone de saída">
                <a href="telaPrincipal.php">Voltar</a>
            </div>

            </div>
        </form>
    </div></body>
</html>
