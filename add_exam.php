<?php
// Inclui o cabeçalho que contém a configuração da sessão e a conexão com o banco de dados
include 'header.php';

// Cria uma nova instância da classe Database e obtém a conexão PDO
$db = new Database();
$pdo = $db->getPdo();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Configura o PDO para lançar exceções em caso de erro

// Verifica se o formulário foi enviado via método POST e se o botão "add_visit" foi pressionado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_visit'])) {
    // Obtém os valores do formulário
    $visitante_nome = $_POST['visitante_nome'];
    $secao_nome = $_POST['secao_nome'];
    $hora_entrada = $_POST['hora_entrada'];
    $cracha = $_POST['cracha'];
    $placa_carro = $_POST['placa_carro'];

    // Obtém o ID do usuário da sessão
    $user_id = $_SESSION['user_id'];

    // Converte o ID do usuário em nome de usuário
    try {
        $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $username = $user['username'];
    } catch (PDOException $e) {
        // Exibe uma mensagem de erro se não for possível obter o nome de usuário
        echo "Erro ao obter o nome de usuário: " . $e->getMessage();
        exit();
    }

    // Verifica se o visitante já possui uma visita em aberto
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM registros WHERE visitante_nome = ? AND status = 'em aberto'");
    $stmt->execute([$visitante_nome]);
    $visitaEmAberto = $stmt->fetchColumn();

    // Se o visitante já tiver uma visita em aberto, exibe um alerta no navegador
    if ($visitaEmAberto > 0) {
        echo "<script>alert('Visitante com visita em aberto');</script>";
    } else {
        // Verifica se todos os campos obrigatórios estão preenchidos
        if (empty($visitante_nome) || empty($secao_nome) || empty($hora_entrada) || empty($cracha)) {
            echo "Por favor, preencha todos os campos obrigatórios.";
        } else {
            try {
                // Insere os dados no banco de dados com status 'em aberto'
                $stmt = $pdo->prepare("INSERT INTO registros (visitante_nome, secao_nome, data, hora_entrada, status, cracha, placa_carro, add_por) VALUES (?, ?, date('now'), ?, 'em aberto', ?, ?, ?)");
                $stmt->execute([$visitante_nome, $secao_nome, $hora_entrada, $cracha, $placa_carro, $username]);

                // Redireciona para a página de visualização de visitas após o sucesso
                header('Location: view_patients.php');
                exit();
            } catch (PDOException $e) {
                // Exibe uma mensagem de erro se ocorrer um problema ao adicionar o registro
                echo "Erro ao adicionar registro: " . $e->getMessage();
            }
        }
    }
}

// Busca os visitantes e seções do banco de dados para preencher os campos de seleção
$visitantes = $pdo->query("SELECT * FROM visitantes")->fetchAll(PDO::FETCH_ASSOC);
$secoes = $pdo->query("SELECT * FROM secoes")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Adicionar Visitas</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.0.0/dist/css/tom-select.css" rel="stylesheet">
</head>

<body class="is-preload">

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Main -->
        <div id="main">
            <div class="inner">

                <!-- Content -->
                <section>
                    <header class="main">
                        <h1>Adicionar Visita</h1>
                    </header>

                    <h2 id="content">Preencha os detalhes abaixo para adicionar uma nova visita</h2>

                    <form method="POST">
                        <label for="visitante_nome">Visitante:</label>
                        <select name="visitante_nome" id="visitante_nome" required>
                            <option></option> <!-- Linha em branco opcional para limpar a seleção -->
                            <?php foreach ($visitantes as $visitante): ?>
                                <option value="<?= htmlspecialchars($visitante['name']) ?>"><?= htmlspecialchars($visitante['name']) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label for="secao_nome">Seção:</label>
                        <select name="secao_nome" id="secao_nome" required>
                            <option></option>
                            <?php foreach ($secoes as $secao): ?>
                                <option value="<?= htmlspecialchars($secao['name']) ?>"><?= htmlspecialchars($secao['name']) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label for="hora_entrada">Hora de Entrada:</label>
                        <input type="time" id="hora_entrada" name="hora_entrada" required><br>

		                <label for="cracha">Nº Crachá:</label>
                        <input type="text" id="cracha" name="cracha" required><br>

                        <label for="placa_carro">Placa do veículo (SFC):</label>
                        <input type="text" id="placa_carro" name="placa_carro" oninput="this.value = this.value.toUpperCase()"><br><br>

                        <ul class="actions stacked">
                            <button type="submit" name="add_visit" class="button primary fit">Adicionar Visita</button>
                        </ul>

                    <hr class="major"/>
                </section>
            </div>
        </div>

        <!-- Sidebar -->
        <div id="sidebar">
            <div class="inner">
                <!-- Menu -->
                <nav id="menu">
                    <header class="major">
                        <h2>Menu</h2>
                    </header>
                    <ul>
                        <li><a href="add_exam.php">Adicionar Visita</a></li>
                        <li><a href="add_patient.php">Cadastrar Visitante</a></li>
                        <li><a href="view_patients.php">Visitas em Aberto</a></li>

                        <?php if ($_SESSION['role'] == 'operador'): ?>
                            <li><a href="relatorio.php" class="text-white hover:underline">Relatórios</a></li>
                        <?php endif; ?>

                        <?php if ($_SESSION['role'] == 'administrador'): ?>
                            <li><a href="relatorio.php" class="text-white hover:underline">Relatórios</a></li>
                            <li><a href="admin.php" class="text-white hover:underline">Admin</a></li>
                        <?php endif; ?>

                        <li><a href="logout.php" class="underline hover:text-gray-300">Sair</a></li>
                    </ul>
                </nav>

                <!-- Section -->
                <section>
                    <header class="major">
                        <h2>Contate-Nos</h2>
                    </header>
                    <ul class="contact">
                        <li class="icon solid fa-envelope"><a href="#">secinfor1becnst@gmail.com</a></li>
                        <li class="icon solid fa-phone">Ramal 2030</li>
                        <li class="icon solid fa-home">Seção de Tecnologia da Informação do 1º Batalhão de Engenharia de Construção</li>
                    </ul>
                </section>

                <!-- Footer -->
                <footer id="footer">
                    <p class="copyright">&copy; Untitled. All rights reserved. Demo Images: <a href="https://unsplash.com">Unsplash</a>. Design: <a href="https://html5up.net">HTML5 UP.</a><a> Desenvolvido pelo 2º Tenente Amelotti</a>.</p>
                </footer>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.0.0/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect("#visitante_nome",{
                create: false
            });
        });
    </script>

    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>

</body>
</html>
