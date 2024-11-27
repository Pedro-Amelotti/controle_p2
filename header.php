<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Definindo os parâmetros de cookie da sessão antes de iniciar a sessão
$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    //'lifetime' => 1800, // Tempo de expiração de 30 minutos
    'path' => $cookieParams["path"],
    'domain' => $cookieParams["domain"],
    'secure' => isset($_SERVER['HTTPS']), // True se estiver usando HTTPS
    'httponly' => true,
    'samesite' => 'Strict' // Protege contra ataques de CSRF
]);

// segurança (definir antes de session_start())
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', 0);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.cookie_samesite', 'Strict');

// ------------------------------------------------------------------------------------------------ //

// Iniciar sessão
session_start();
include '/var/www/html/controle_p2/includes/database.php';

	try{
		$db = new Database();
		$pdo = $db->getPdo();
	}catch(Exception $e){
		die('Erro ao conectar com a database: ' . $e->getMessage());
	}

//$db = new Database();
//$pdo = $db->getPdo();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Definindo o fuso horário para obter a data e hora corretas
date_default_timezone_set('America/Sao_Paulo'); 

// ------------------------------------------------------------------------------------------------ //

// Coleta userId, username, data/hora e página acessada
function logUserAccess($pdo, $userId, $username, $pageName) {
    try {
        // Obtém a data e hora atuais
        $accessTime = date('Y-m-d H:i:s');

        // Prepara e executa a consulta para inserir o log de acesso
        $stmt = $pdo->prepare("INSERT INTO user_access_logs (user_id, username, page_accessed, access_time) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $username, $pageName, $accessTime]);
    } catch (PDOException $e) {
        // Log de erro ou tratamento de exceção apropriado
        error_log("Erro ao registrar acesso: " . $e->getMessage());
    }
}

// Registra o acesso do usuário
$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
$pageName = basename($_SERVER['PHP_SELF']); // Obtém o nome da página atual

logUserAccess($pdo, $userId, $username, $pageName);

// ------------------------------------------------------------------------------------------------ //

// Regenerar o ID da sessão a cada pedido para prevenir sequestro de sessão
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Função de logout seguro
function secureLogout() {
    session_start();
    // Limpar todos os dados da sessão
    $_SESSION = [];

    // Invalidar o cookie de sessão
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destruir a sessão
    session_destroy();
}
?>
