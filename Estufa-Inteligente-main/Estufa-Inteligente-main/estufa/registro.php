<?php

include('protect.php');

//quantidade de linhas
$linhasPorPagina = 15;

$arquivo = 'dados.txt';

// ler linhas (array)
$linhas = file($arquivo);

// contar n total de linhas
$totalLinhas = count($linhas);

// contar n total de paginas
$totalPaginas = ceil($totalLinhas / $linhasPorPagina);

// Página atual (obtida a partir do parâmetro de URL)
$paginaAtual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

// Índice inicial da linha na página atual
$indiceInicial = ($paginaAtual - 1) * $linhasPorPagina;

// Índice final da linha na página atual
$indiceFinal = $indiceInicial + $linhasPorPagina;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Registro</title>
</head>

<body class="body-registro" style="background-image: url(fundoLogin/registro.png);">
<div class="div-registra-cabecalho">
<img src="fundoLogin/voltarWhite.png" alt="Ícone de saída">
                <a href="telaPrincipal.php">Voltar</a>

</div>
    <div class="div-registro">
        <table>

            <tr>
                <th>Data</th>
                <th>Hora</th>
                <th>Temperatura</th>
                <th>Umidade</th>
            </tr>

            <?php
            // Itera sobre as linhas da página atual
            for ($i = $indiceInicial; $i < $indiceFinal && $i < $totalLinhas; $i++) {

                $dados = explode(' ', $linhas[$i]);

                $data = $dados[0];
                $hora = $dados[1];
                $temperatura = $dados[2];
                $umidade = $dados[3];

                echo '<tr>';
                echo '<td>' . $data . '</td>';
                echo '<td>' . $hora . '</td>';
                echo '<td>' . $temperatura . '</td>';
                echo '<td>' . $umidade . '</td>';
                echo '</tr>';
            }
            ?>
        </table>
    </div>
            <!-- links pag anterior e proxima -->
            <div class="div-paginas">
            <?php if ($paginaAtual > 1) : ?>
                <img class = "img-ante" src="fundoLogin/anterior.png" alt="Ícone de anterior">
                <a href="?pagina=<?php echo $paginaAtual - 1; ?>">Anterior</a>
            <?php endif; ?>

            <?php if ($paginaAtual < $totalPaginas) : ?>
                <a class="a-posterior" href="?pagina=<?php echo $paginaAtual + 1; ?>">Próxima</a>
                <img class = "img-prox" src="fundoLogin/proximo.png" alt="Ícone de proximo">
            <?php endif; ?>
        </div>

</body>

</html>
