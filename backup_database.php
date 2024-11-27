<?php
$database_path = __DIR__ . '/controle_visitas.db';
$backup_dir = __DIR__ . '/backups';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0777, true);
}

$backup_filename = $backup_dir . '/backup_' . date('Y-m') . '.db';
copy($database_path, $backup_filename);
echo "Backup mensal gerado com sucesso: $backup_filename";
?>
