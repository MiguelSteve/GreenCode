<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Listar Estufas</title>
</head>
<body class = "body-listar-estufa">

    <div class = "div-principal-listar-estufa">

    <h1>Listar Estufas Cadastradas</h1>
    
    <?php
    session_start();
    
    if (isset($_SESSION['id_usuario'])) {
        try {
            // conexao bd
            $conn = new PDO("mysql:host=177.153.63;dbname=estufa", "estufa", "TgF@da");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // pegar o id_usuario da sessao
            $id_usuario = $_SESSION['id_usuario'];

            // consultar n_serie e nome
            $sql = "SELECT n_serie, nome FROM estufa";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            // Verificar se o número de série existe na tabela de produtos
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $n_serie = $row['n_serie'];
                $nome = $row['nome'];

                // verificar se email do usuario corresponde ao n_serie
                $checkQuery = $conn->prepare("SELECT email_produto FROM produto WHERE n_serie = :nSerie");
                $checkQuery->bindParam(':nSerie', $n_serie, PDO::PARAM_STR);
                $checkQuery->execute();
                $resultProduto = $checkQuery->fetch(PDO::FETCH_ASSOC);

                if ($resultProduto) {

                    // encontrar o email do usuario na tb usuario

                    $emailProduto = $resultProduto['email_produto'];

                    $checkQuery = $conn->prepare("SELECT email FROM usuario WHERE id_usuario = :id_usuario");
                    $checkQuery->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                    $checkQuery->execute();
                    $resultUsuario = $checkQuery->fetch(PDO::FETCH_ASSOC);

                    if ($resultUsuario) {
                        $emailUsuario = $resultUsuario['email'];
                        // comparar email (tb usuario) com email_produto (tb produto)
                        if ($emailProduto == $emailUsuario) {
                            // criar botao para cada estufa
                            echo '<div class="div-listar-estufa">
                            <button type="button" onclick="redirecionaMonitoramento(' . htmlspecialchars(json_encode($n_serie), ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars(json_encode($nome), ENT_QUOTES, 'UTF-8') . ')">' . $nome . ' - Estufa: ' . $n_serie . '</button>
                            <br>
                        </div>';
                                          }
                    } else {
                        echo "Usuário não encontrado na tabela de usuários.<br>";
                    }
                } else {
                    echo "Número de série $n_serie não encontrado na tabela de produtos.<br>";
                }
            }

        } catch (PDOException $erro) {
            echo "Erro na conexão com o banco de dados: " . $erro->getMessage();
        }
    } else {
        echo "Sessão de usuário não iniciada.";
    }
    ?>
    <!--funcao para redirecionar o monitoramento de estufa correta-->
    <script>
        function redirecionaMonitoramento(n_serie, nome) {
            // redireciona a pagina
            window.location.href = 'monitora.php?n_serie=' + n_serie;
        }
    </script>
        <div class="div-voltar">
                <img class = "img-voltar" src="fundoLogin/voltar.png" alt="Ícone de saída">
                <a href="telaPrincipal.php">Voltar</a>
            </div>
    </div>
</body>
</html>
