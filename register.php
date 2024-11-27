<?php
session_start();
include './includes/database.php';

$db = new Database();
$pdo = $db->getPdo();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'user'; // Default role for new users
    $approved = 0; // Default approval status

    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, approved) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$username, $password, $role, $approved])) {
        header('Location: login.php');
        exit();
    } else {
        $error = 'Erro ao registrar. Por favor, tente novamente.';
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
        <h1>Cadastro</h1>
            <label for="user">Usuário</label>
            <input type="text" name="username" required />

            <label for="password">Senha</label>
            <input type="password" name="password" required />

            <p>Possui uma conta? <a href="login.php">Entre aqui</a></p>

            <button type="submit">Cadastrar</button>
        </form>




            <?php if (isset($error)): ?>
            <p><?= $error ?></p>
        <?php endif; ?>
        </div>

    </div>
  </body>
</html>
