<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!--icon-->
    <link rel="shortcut icon" type="imagex/png" href="./images/icon.ico">
    <!--referenciar o login.css-->
    <link rel="stylesheet" type="text/css" href="./css/styles.css">
</head>
<body style="background-image: url(fundoLogin/login.png);">
    <div class="div-login">
    <form action="" method="POST">
        <br><br><h1>LOGIN</h1><br><br><br>
        <div class = "div-login-autentica">
        <?php

#------------------------------AUTENTICAÇÃO-----------------------------------------
include('conexao.php');

if(isset($_POST['email']) || isset($_POST['senha'])) {

    if(strlen($_POST['email']) == 0) {
        echo "*Preencha seu e-mail!";
    } else if(strlen($_POST['senha']) == 0) {
        echo "*Preencha sua senha!";
    } else {

        $email = $mysqli->real_escape_string($_POST['email']);
        $senha = $mysqli->real_escape_string($_POST['senha']);

        $sql_code = "SELECT * FROM usuario WHERE email = '$email' AND senha = '$senha'";
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);

        $quantidade = $sql_query->num_rows;

        #------------------------------SESSÕES--------------------------------------

        if($quantidade == 1) {
            
            $usuario = $sql_query->fetch_assoc();

            if(!isset($_SESSION)) {
                session_start();
            }

            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nome'] = $usuario['nome'];

            #direciona para tela principal
            header("Location: telaPrincipal.php");

        } else {
            echo "*Falha ao logar! E-mail ou senha incorretos";
        }

    }

}
?>

        </div>
        <p>
            <input id="email" type="text" name="email" placeholder="Email">
        </p>
        <p>
            <input id="password"type="password" name="senha" placeholder="Senha">
        </p>
        <p>
            <input type="submit" value="ENTRAR"></input>
            <h4>Não possui conta?<a href="cadasUsuario.php"> Cadastre-se</a></h4>
        </p>
    </form>

    </div>
    <script>
            var email = document.getElementById('email');
            var password= document.getElementById('password');


             /*borda verde ao clicar no campo email*/
            email.addEventListener('focus',()=>{
                email.style.borderColor= "#21572dcc";
            });
            /*voltar ao normal quando clica em outro campo email*/
            email.addEventListener('blur',()=>{
                email.style.borderColor= "#ccc";
            });

            /*borda verde ao clicar no campo SENHA*/
            password.addEventListener('focus',()=>{
                password.style.borderColor= "#21572dcc";
            });
            /*voltar ao normal quando clica em outro campo SENHA*/
            password.addEventListener('blur',()=>{
                password.style.borderColor= "#ccc";
            });
        </script>

</body>
</html>

