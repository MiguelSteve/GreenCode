<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--<link rel="stylesheet" href="css/protect.css">-->
    <!--icon-->
    <link rel="shortcut icon" type="imagex/png" href="./images/icon.ico">
    <title>GreenCode</title>
</head>
<body>
    <div class="protect">
        <?php
            if(!isset($_SESSION)) {
                session_start();
            }

            if(!isset($_SESSION['id_usuario'])) {

                echo "<div style='text-align: center; color: #325c12; font-family: \"Lucida Sans\", \"Lucida Sans Regular\", \"Lucida Grande\", \"Lucida Sans Unicode\", Geneva, Verdana, sans-serif; margin-top: 270px;'>";
                echo "<img src='fundoLogin/erro.png' alt='Erro'><br>";
                die("<h1>Não foi possível carregar esta página!</h1> <h3> Para acessá-la, faça o <a href=\"login.php\">login.</a> <br><br>Caso não tenha uma conta, por favor, <a href=\"cadasUsuario.php\">cadastre-se.</a></h3></div>");
                                                            
            }
        ?>

    </div>
</body>
</html>

