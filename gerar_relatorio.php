<?php
require 'fpdf186/fpdf.php'; // Inclui a biblioteca FPDF
require 'header.php'; // Inclui a conexão com o banco de dados

// Classe para gerar o PDF
class PDF extends FPDF {
    // Cabeçalho do PDF
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Relatorio Diario de Registros', 0, 1, 'C');
        $this->Ln(10);
    }

    // Rodapé do PDF
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}

try {
    // Cria a instância do banco de dados
    $db = new Database();
    $pdo = $db->getPdo();

    // Data do relatório
    $date = date('Y-m-d');

    // Consulta SQL para buscar registros do dia
    $stmt = $pdo->prepare("SELECT r.*, v.name AS visitante_nome, s.name AS secao_nome
                           FROM registros r
                           JOIN visitantes v ON r.visitante_id = v.id
                           JOIN secoes s ON r.secao = s.id
                           WHERE DATE(r.data) = ?");
    $stmt->execute([$date]);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cria uma instância da classe PDF
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    // Adiciona os dados dos registros no PDF
    foreach ($registros as $registro) {
        $pdf->Cell(0, 10, 'Visitante: ' . $registro['visitante_nome'], 0, 1);
        $pdf->Cell(0, 10, 'Seção: ' . $registro['secao_nome'], 0, 1);
        $pdf->Cell(0, 10, 'Data: ' . $registro['data'], 0, 1);
        $pdf->Cell(0, 10, 'Hora de Entrada: ' . $registro['hora_entrada'], 0, 1);
        $pdf->Cell(0, 10, 'Hora de Saída: ' . $registro['hora_saida'], 0, 1);
        $pdf->Ln(5);
    }

    // Define o nome do arquivo de relatório
    $filename = 'relatorios/relatorio_' . date('Y-m-d') . '.pdf';

    // Salva o PDF
    $pdf->Output($filename, 'F');

    echo "Relatório diário gerado com sucesso: $filename";

} catch (PDOException $e) {
    echo "Erro ao gerar o relatório: " . $e->getMessage();
}