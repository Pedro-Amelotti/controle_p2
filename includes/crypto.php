<?php
//include 'includes/crypto.php'
function getEncryptionKey() {
    // Coloque o caminho correto para a chave de criptografia
    return file_get_contents('includes/crypto.php');
}

function encryptData($data) {
    $encryption_key = getEncryptionKey();
    $iv = openssl_random_pseudo_bytes(16); // Gera um IV de 16 bytes
    $encryptedData = openssl_encrypt($data, 'AES-256-CBC', $encryption_key, 0, $iv);
    return base64_encode($iv . $encryptedData); // Combine IV e dados criptografados, codificados em base64
}

function decryptData($encryptedData) {
    $encryption_key = getEncryptionKey();
    $data = base64_decode($encryptedData); // Decodifica os dados base64
    $iv = substr($data, 0, 16); // Extrai o IV de 16 bytes
    $encryptedData = substr($data, 16); // Extrai os dados criptografados restantes
    return openssl_decrypt($encryptedData, 'AES-256-CBC', $encryption_key, 0, $iv);
}
?>
