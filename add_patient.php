<?php
include 'header.php';

$db = new Database();
$pdo = $db->getPdo();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_patient'])) {
    $name = $_POST['name'];
    $data_nascimento = $_POST['data_nascimento'];
    $cpf = $_POST['cpf'];
    $stmt = $pdo->prepare("INSERT INTO visitantes (name, data_nascimento, cpf) VALUES (?, ?, ?)");
    $stmt->execute([$name, $data_nascimento, $cpf]);
    header('Location: view_patients.php');
    exit();
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Cadastrar Visitante</title>
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
                        <h1>Cadastrar Visitante</h1>
                    </header>

                    <h2 id="content">Preencha os campos abaixo para adicionar um novo visitante</h2>

                    <form method="POST">
                    <label>Nome Completo: <input type="text" name="name" required></label>
                    <label>Data de Nascimento: <br><input type="date" name="data_nascimento"></label>
                    <label>CPF (IDT MIL caso militar): <input type="text" name="cpf" required></label>
                    <button type="submit" name="add_patient">Cadastrar Visitante</button>
                </form>

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
