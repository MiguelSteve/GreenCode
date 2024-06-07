<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Gráfico de Temperatura</title>
    <!-- Inclua o Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="tela-principal" style="background-image: url(fundoLogin/monitora.png);">

<div class = "div-monitora"></div>

<div class = "div-info-planta">

<!--AQUI, AQUI!!!-->

<?php
include('protect.php');

if (isset($_GET['n_serie'])) {
    $n_serie = $_GET['n_serie'];
} else {
    echo "Número de série não especificado";
    exit;
}
# Fazer conexão com BD
try
{
    # Conexão com MySQL usando PDO
    $conectaBD = new PDO("mysql:host=177.153.63;port=3306;dbname=estufa", "estufa", "TgF@da3");
    $conectaBD->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    # Preparar e executar a consulta
    $sql = "SELECT * FROM estufa WHERE n_serie = :n_serie";

    $stmt = $conectaBD->prepare($sql);
    $stmt->bindParam(':n_serie', $n_serie, PDO::PARAM_STR);
    $stmt->execute();

    if($stmt->rowCount() > 0){
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        $foto_planta = $dados["imagem"];
        $diretorio = "planta/$n_serie/$foto_planta";

        if (file_exists($diretorio)){
            echo "<div class='div-foto-planta'><img src='$diretorio'></div><br>";
        }
        else{
            echo "Erro ao carregar a imagem!";
        }

        echo "<h1>",$dados["nome"],"</h1></h5>"; 
        echo "<h5>Data criação: ",$dados["data_criacao"],"</h5>"; 
        echo "<h5>Umidade ideal: ",$dados["umidade"],"%</h5>";
        echo "<h5>Temperatura ideal: ",$dados["temperatura"],"°C</h5>";  
        
    } else {
        echo "Estufa não encontrada";
    }

}
catch(PDOException $erro)
{
    # Informar que houve erro ao fazer a conexão com BD
    echo "Houve erro ao fazer a conexão com o banco de dados!";
}

?>

<!-- MIGUEL VERIFICAR ESSE CODIGO-->
<script>
    function Monitora(){
    alert('Dados Recebidos');
    }
</script>
<form method="GET" action="http://localhost:3000/ReceberDados" onsubmit="Monitora()">
        <input type="submit" value="Iniciar monitoramento">
    </form>

    <div class="div-voltar">
                <img class = "img-voltar" src="fundoLogin/voltar.png" alt="Ícone de saída">
                <a href="listarEstufas.php">Voltar</a>
            </div>

</div>

<div class = "div-monitora-all">
<div class = "div-graficos">
<?php
// Caminho do arquivo TXT
$arquivo = 'dados.txt';

// Lê todas as linhas do arquivo em um array
$linhas = file($arquivo);

// Inicializa arrays para temperatura e horário
$temperaturaData = [];
$horarioData = [];

// Pega apenas as últimas 3 linhas do array (conforme seu exemplo)
$ultimas_linhas = array_slice($linhas, -10);

// Itera sobre as últimas linhas
foreach ($ultimas_linhas as $linha) {
    // Divide a linha em partes usando espaço como delimitador
    $dados = explode(' ', $linha);

    // Atribui os valores a variáveis
    $data = $dados[0];
    $hora = $dados[1];
    $temperatura = $dados[2];
    $umidade = $dados[3];

    // Adiciona os valores aos arrays
    $horarioData[] = $hora;
    $temperaturaData[] = $temperatura;
    $umidadeData[] = $umidade;
}

// Converte os arrays para JSON
$temperaturaDataJSON = json_encode($temperaturaData);
$horarioDataJSON = json_encode($horarioData);

$umidade = intval($umidade);
$umidadeDataJSON = json_encode($umidadeData);
?>


<!-- Seu HTML e CSS -->

<div class="div-monitora-temp">
    <!-- O gráfico será renderizado aqui -->
    <canvas id="graficTemp"></canvas>

    <!-- Seu script para criar o gráfico -->
    <script>
        // Seus dados de temperatura e horário (vindos do PHP)
        var temperaturaData = <?php echo $temperaturaDataJSON; ?>;
        var horarioData = <?php echo $horarioDataJSON; ?>;

        // Obtendo o contexto do canvas
        var ctx = document.getElementById('graficTemp').getContext('2d');

        // Criando o gráfico
        var graficTemp = new Chart(ctx, {
            type: 'line',
            data: {
                labels: horarioData,
                datasets: [{
                    label: 'Temperatura (°C)',
                    data: temperaturaData,
                    backgroundColor: 'rgba(77, 129, 36, 0.2)',
                    borderColor: 'rgba(77, 129, 36, 2.0)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</div>

<div class="div-monitora-umidade">
    <!-- O gráfico será renderizado aqui -->
    <canvas id="graficUmidade"></canvas>

    <!-- Seu script para criar o gráfico -->
    <script>
        // Seus dados de temperatura e horário (vindos do PHP)
        var umidadeData = <?php echo $umidadeDataJSON; ?>;
        var horarioData = <?php echo $horarioDataJSON; ?>;

        // Obtendo o contexto do canvas
        var ctx = document.getElementById('graficUmidade').getContext('2d');

        // Criando o gráfico
        var graficUmidade = new Chart(ctx, {
            type: 'line',
            data: {
                labels: horarioData,
                datasets: [{
                    label: 'Umidade (°C)',
                    data: umidadeData,
                    backgroundColor: 'rgba(79, 126, 217, 0.2)',
                    borderColor: 'rgba(79, 126, 217, 2.0)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</div>
</div>
</div>
</body>
</html>
