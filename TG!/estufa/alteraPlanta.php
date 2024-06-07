<?php
include('protect.php');
include('connection.php');

if (isset($_SESSION['id_usuario'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Receber dados do formulário
        $id_usuario = $_SESSION['id_usuario'];
        $dat = $_POST['campoData'];
        $nSerie = $_POST['campoSerie'];
        $arquivo = $_FILES['foto_planta'];

        try {
            // Conexao bd
            $conn = new PDO("mysql:host=177.153.63.45;dbname=estufa", "estufa", "TgF@da3");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Verificar se número de série existe
            $checkQuery = $conn->prepare("SELECT n_serie, email_produto FROM produto WHERE n_serie = :nSerie");
            $checkQuery->bindParam(':nSerie', $nSerie, PDO::PARAM_STR);
            $checkQuery->execute();
            $resultProduto = $checkQuery->fetch(PDO::FETCH_ASSOC);
            
            if (!$resultProduto) {
                echo "<dialog id='msgSucesso' open>
                <center><img src='fundoLogin/sucesso.png'></center>
                <p>Número de série não encontrado na tabela de produtos.</p>
                <a href='telaPrincipal.php'><input type='button' value='VOLTAR' name='btnVoltar'></a>
            </dialog>";
            }
            
            // Encontrar o email do usuário na tb usuario
            $emailProduto = $resultProduto['email_produto'];
            
            $checkQuery = $conn->prepare("SELECT email FROM usuario WHERE id_usuario = :id_usuario");
            $checkQuery->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $checkQuery->execute();
            $resultUsuario = $checkQuery->fetch(PDO::FETCH_ASSOC);
            
            if (!$resultUsuario) {
                die("Usuário não encontrado na tabela de usuários.");
            }
            
            $emailUsuario = $resultUsuario['email'];
            
            // Comparar email (tb usuario) com email_produto (tb produto)
            if ($emailProduto == $emailUsuario) {
                // Verificar se usuário já cadastrou a planta
                $checkQuery = $conn->prepare("SELECT * FROM estufa WHERE n_serie = :nSerie");
                $checkQuery->bindParam(':nSerie', $nSerie, PDO::PARAM_STR);
                $checkQuery->execute();
                $resultPlantaExistente = $checkQuery->fetch(PDO::FETCH_ASSOC);
                
                if ($resultPlantaExistente) {
                    // Alterar dados da planta
                    $opcaoSelecionada = $_POST['categoria'];
                    
                    $stmt = $conn->prepare("SELECT * FROM planta WHERE id_planta = :opcaoSelecionada");
                    $stmt->bindParam(':opcaoSelecionada', $opcaoSelecionada, PDO::PARAM_INT);
                    $stmt->execute();
                    $dadosOpcao = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($dadosOpcao) {
                        $stmt = $conn->prepare("UPDATE estufa SET id_usuario = :id_usuario, data_criacao = :data_criacao, imagem = :imagem, nome = :nome, umidade = :umidade, temperatura = :temperatura WHERE n_serie = :nSerie");
                        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                        $stmt->bindParam(':nSerie', $nSerie, PDO::PARAM_STR);
                        $stmt->bindParam(':data_criacao', $dat, PDO::PARAM_STR);
                        $stmt->bindParam(':nome', $dadosOpcao['nome_planta']);
                        $stmt->bindParam(':umidade', $dadosOpcao['umidade_ideal']);
                        $stmt->bindParam(':temperatura', $dadosOpcao['temperatura_ideal']);
                        $stmt->bindParam(':imagem', $arquivo['name'], PDO::PARAM_STR);
                        
                        if ($stmt->execute()) {
                            // salvar imagem
                            if (!empty($arquivo['name'])) {

                                $diretorio = "planta/$nSerie/";

                                // se não possui o arquivo da foto da planta
                                if (!file_exists($diretorio)) {
                                    mkdir($diretorio, 0755, true); 
                                }

                                // se já existe a foto, substituir

                                $arquivos = glob($diretorio . '*');

                                foreach($arquivos as $arquivoExistente){
                                    if(is_file($arquivoExistente)){
                                        // excluir a foto anterior
                                        unlink($arquivoExistente);
                                    }
                                }

                                $nome_arquivo = $arquivo['name'];
                                move_uploaded_file($arquivo['tmp_name'], $diretorio . $nome_arquivo);

                                /* gerar arquivo txt */
                                $file_plant = fopen("dadosEstufas/estufa - $nSerie.txt", "a");
                                if ($file_plant) {
                                    fwrite($file_plant, $opcaoSelecionada . "\n");
                                    fclose($file_plant);
                                }
                                                             
                                echo "<dialog id='msgSucesso' open>
                                        <center><img src='fundoLogin/sucesso.png'></center>
                                        <p>Cadastro realizado com sucesso!</p>
                                        <a href='telaPrincipal.php'><input type='button' value='VOLTAR' name='btnVoltar'></a>
                                    </dialog>";
                            } else {
                                echo "Erro ao inserir dados da opção na tabela 'estufa'.";
                            }
                        } else {
                            echo "Erro ao atualizar dados na tabela 'estufa'.";
                        }
                    } else {
                        echo "Opção não encontrada na tabela 'planta'.";
                    }
                } else {
                    echo "<dialog id='msgSucesso' open>
                            <center><img src='fundoLogin/sucesso.png'></center>
                            <p>Estufa não encontrado!</p>
                            <a href='telaPrincipal.php'><input type='button' value='VOLTAR' name='btnVoltar'></a>
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Editar dados da planta</title>
</head>
<body class="body-planta" style="background-image: url(fundoLogin/cadas-planta.png);">

<div class="div-cadas-planta">
    <form class="form-cadas-planta" method="POST" enctype="multipart/form-data">
        <h1>EDITAR DADOS DA PLANTA</h1>
        <div class="div-cadas-planta-aviso">
            <?php
            // Aviso ou mensagens aqui
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

            <select name="categoria" class="catergoria" required>
                <option value="">Selecione a planta</option>
                <?php
                $query = $conn->query("SELECT * FROM planta ORDER BY nome_planta ASC");
                $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($registros as $options) {
                    echo "<option value='{$options['id_planta']}'>{$options['nome_planta']}</option>";
                }
                ?>
            </select>

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

            <div class="div-voltar">
                <img class="img-voltar" src="fundoLogin/voltar.png" alt="Ícone de saída">
                <a href="telaPrincipal.php">Voltar</a>
            </div>

        </div>
    </form>
</div>
</body>
</html>
