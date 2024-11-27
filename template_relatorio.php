<!-- template_relatorio.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Visitantes</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Relatório de Visitantes</h1>
    <table>
        <thead>
            <tr>
                <th>Visitante</th>
                <th>Seção</th>
                <th>Data</th>
                <th>Hora de Entrada</th>
                <th>Hora de Saída</th>
                <th>Adicionado por</th>
                <th>Crachá</th>
                <th>Placa do Carro</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros as $registro): ?>
                <tr>
                    <td><?= htmlspecialchars($registro['visitante_nome']) ?></td>
                    <td><?= htmlspecialchars($registro['secao_nome']) ?></td>
                    <td><?= htmlspecialchars($registro['data']) ?></td>
                    <td><?= htmlspecialchars($registro['hora_entrada']) ?></td>
                    <td><?= htmlspecialchars($registro['hora_saida']) ?></td>
                    <td><?= htmlspecialchars($registro['add_por']) ?></td>
                    <td><?= htmlspecialchars($registro['cracha']) ?></td>
                    <td><?= htmlspecialchars($registro['placa_carro']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
