<?php
// relatorio.php
require_once 'header.php';
require 'includes/vendor/autoload.php';

use Dompdf\Dompdf;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data_inicial = $_POST['data_inicial'];
    $data_final = $_POST['data_final'];
    $formato = $_POST['formato'];

    $pdo = $db->getPdo();
    $stmt = $pdo->prepare("SELECT visitante_nome, secao_nome, data, hora_entrada, hora_saida, add_por, cracha, placa_carro FROM registros WHERE data BETWEEN ? AND ?");
    $stmt->execute([$data_inicial, $data_final]);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($formato === 'pdf') {
        // Gerar PDF
        ob_start();
        include 'template_relatorio.php';
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'Relatório ' . date('d-m-Y', strtotime($data_inicial)) . ' a ' . date('d-m-Y', strtotime($data_final)) . '.pdf';
        $dompdf->stream($filename);
    } elseif ($formato === 'csv') {
        // Gerar CSV
        $filename = 'Relatório ' . date('d-m-Y', strtotime($data_inicial)) . ' a ' . date('d-m-Y', strtotime($data_final)) . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        
        // Escrever a linha de cabeçalho
        fputcsv($output, ['Visitante', 'Seção', 'Data', 'Hora de Entrada', 'Hora de Saída', 'Adicionado por', 'Crachá', 'Placa do Carro'], ',');

        // Escrever os dados dos registros
        foreach ($registros as $registro) {
            fputcsv($output, [
                $registro['visitante_nome'],
                $registro['secao_nome'],
                $registro['data'],
                $registro['hora_entrada'],
                $registro['hora_saida'],
                $registro['add_por'],
                $registro['cracha'],
                $registro['placa_carro']
            ], ',');
        }
        
        fclose($output);
    }
    exit;
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Gerar Relatórios</title>
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

                <!-- Header -->
				<header id="header">
					<a href="index.php" class="logo"><strong>Sistema de Cadastramento e Controle de Visitantes</strong> desenvolvido pela STI do 1º BEC</a>
					<ul class="icons">
					</ul>
				</header>

                <!-- Content -->
                <section>
                    <header class="main">
                        <h1>Gerar Relatórios</h1>
                    </header>

                    <h2 id="content">Preencha os detalhes abaixo para gerar um novo relatório</h2>

                    <form method="post" action="relatorio.php">
                        <label for="data_inicial" style="padding-top: 15px;">Data Inicial:</label>
                        <input type="date" id="data_inicial" name="data_inicial" required>
                        <br>
                        <label for="data_final" style="padding-top: 15px;">Data Final:</label>
                        <input type="date" id="data_final" name="data_final" required>
                        <br>
                        <label for="formato" style="padding-top: 15px;">Formato do Relatório:</label>
                        <select id="formato" name="formato" required  style="width: fit-content;">
                            <option value="pdf">PDF</option>
                            <option value="csv">CSV</option>
                        </select>
                        <br>
                        <button type="submit">Gerar Relatório</button>
                    </form>
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
            new TomSelect("#visitante_nome", {
                create: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect("#secao_nome", {
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
