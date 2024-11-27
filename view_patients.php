<?php
include 'header.php';

$db = new Database();
$pdo = $db->getPdo();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_visit'])) {
    $registro_id = $_POST['registro_id'];
    $hora_saida = $_POST['hora_saida'];

    if (empty($hora_saida)) {
        echo "Por favor, insira a hora de saída.";
    } else {
        try {
            // Atualizar o registro com a hora de saída e mudar o status para 'concluído'
            $stmt = $pdo->prepare("UPDATE registros SET hora_saida = ?, status = 'concluído' WHERE id = ?");
            $stmt->execute([$hora_saida, $registro_id]);

        } catch (PDOException $e) {
            echo "Erro ao atualizar registro: " . $e->getMessage();
        }
    }
}

// Buscar registros em aberto
$registros_abertos = $pdo->query("SELECT * FROM registros WHERE status = 'em aberto'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Visitas em Aberto</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.0.0/dist/css/tom-select.css" rel="stylesheet">


</head>

<body class="is-preload">

    <!-- Wrapper -->
    <div id="wrapper">

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

        <!-- Main -->
        <div id="main">
            <div class="inner">

                <!-- Content -->
                <section>
                    <header class="main">
                        <h1>Visitas em Aberto</h1>
                    </header>
                
                    <h2 id="content">Abaixo estão as visitas em aberto.</h2>
                
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome do Visitante</th>
                                    <th>Seção</th>
                                    <th>Hora de Entrada</th>
                                    <th>Hora de Saída</th>
                                    <th>Crachá Nº</th>
                                    <th>Placa</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registros_abertos as $registro): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($registro['visitante_nome']) ?></td>
                                        <td><?= htmlspecialchars($registro['secao_nome']) ?></td>
                                        <td><?= htmlspecialchars($registro['hora_entrada']) ?></td>
                                        <td>
                                            <form method="POST">
                                                <input type="hidden" name="registro_id" value="<?= htmlspecialchars($registro['id']) ?>">
                                                <input type="time" name="hora_saida" required>
                                        </td>
                                        <td><?= htmlspecialchars($registro['cracha']) ?></td>
                                        <td><?= htmlspecialchars($registro['placa_carro']) ?></td>
                                        <td>
                                            <button type="submit" name="update_visit">Concluir Visita</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                                
                    <hr class="major"/>
                    </section>

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
