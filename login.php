<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//phpinfo();

//inicia sessão
session_start();

include '/var/www/html/controle_p2/includes/database.php';

// EESA BUCETA NÃO FUNCIONA QUANDO DESCOMENTA AAAAAAAAAAAAAAA
//Verificar path
//include "header.php";

try{
	$db = new Database();
	$pdo = $db->getPdo();	
}catch (Exception $e){
	die('Erro ao conectar com a database: '. $e->getMessage());
}

//Verificar se o métrodo é post
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	$username = $_POST['username'] ?? '';
	$password = $_POST['password'] ?? '';

	try{
		//Buscar usuario
		$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->execute([$username]);
		$user = $stmt->fetch();

		//ver se encontrou e senha está correta
		if ($user && password_verify($password, $user['password'])){
			if ($user['approved']){
				$_SESSION['user_id'] = $user['id'];
				$_SESSION['username'] = $user['username'];
				$_SESSION['role'] = $user['role'];

				header('Location: index.php');
				exit;
			} else {
				$error = 'Sua conta não está aprovada';
			
			}
		} else {
			$error = 'Credenciais inválidas';
		}
	} catch (Exception $e){
		$error = 'erro ao executar a consulta: ' . $e->getMessage();
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Controle de Entrada e Saída</title>

    <script
      src="https://kit.fontawesome.com/66aa7c98b3.js"
      crossorigin="anonymous"
    ></script>

    <link rel="stylesheet" href="assets/css/style.css"/>
  </head>

  <body>
    <div class="container">
      <form class="form-1" method="post">
        <h1>Login</h1>
            <label for="user">Usuário</label>
            <input type="text" name="username" required />

            <label for="password">Senha</label>
            <input type="password" name="password" required />

            <p>Não tem uma conta? <a href="register.php">Cadastre-se aqui</a></p>
        <?php if (isset($error)): ?>
            <p><?= $error ?></p>
        <?php endif; ?>
            <button>Login</button>
        </div>
      </form>
    </div>
  </body>
</html>
