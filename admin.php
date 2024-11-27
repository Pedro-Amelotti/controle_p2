<?php
require 'header.php';

// Verificar se o usuário é um administrador
if ($_SESSION['role'] !== 'administrador') {
    echo "Acesso negado.";
    exit();
}

$pdo = $db->getPdo();

// Lógica para aprovar/negar usuários
if (isset($_GET['approve'])) {
    $userId = $_GET['approve'];
    $stmt = $pdo->prepare("UPDATE users SET approved = 1 WHERE id = ?");
    $stmt->execute([$userId]);
    header('Location: admin.php');
    exit();
}

if (isset($_GET['deny'])) {
    $userId = $_GET['deny'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    header('Location: admin.php');
    exit();
}

// Lógica para excluir usuários
if (isset($_GET['delete_user'])) {
    $userId = $_GET['delete_user'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    header('Location: admin.php');
    exit();
}

// Lógica para alterar o 'role' do usuário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_role'])) {
    $userId = $_POST['user_id'];
    $newRole = $_POST['role'];
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$newRole, $userId]);
    header('Location: admin.php');
    exit();
}

// Buscar usuários e logs de acesso
$pending_users = $pdo->query("SELECT * FROM users WHERE approved = 0")->fetchAll(PDO::FETCH_ASSOC);
$all_users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
$logs = $pdo->query("SELECT username, page_accessed, access_time FROM user_access_logs ORDER BY access_time DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administração</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
</head>
<body>
    <nav class="container-fluid">
        <ul>
            <li><strong>Administração do Sistema</strong></li>
        </ul>
        <ul>
            <li><a href="add_patient.php">Cadastrar Visitante</a></li>
            <li><a href="add_exam.php">Adicionar Visita</a></li>
            <li><a href="view_patients.php">Ver Visitas em Aberto</a></li>
            <?php if ($_SESSION['role'] === 'operador'): ?>
                <li><a href="relatorio.php">Relatórios</a></li>
            <?php endif; ?>
            <?php if ($_SESSION['role'] === 'administrador'): ?>
                <li><a href="relatorio.php">Relatórios</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <main class="container">
        <div class="grid">
            <section>
                <hgroup>
                    <h2>Administração de Usuários</h2>
                    <h3>Gerencie os usuários e suas permissões</h3>
                </hgroup>

                <h4>Usuários Pendentes</h4>
                <ul>
                    <?php foreach ($pending_users as $user): ?>
                        <li>
                            <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>
                            <a href="admin.php?approve=<?= $user['id'] ?>">Aprovar</a>
                            <a href="admin.php?deny=<?= $user['id'] ?>">Negar</a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <h4>Todos os Usuários</h4>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome de Usuário</th>
                            <th>Role</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <select name="role">
                                            <option value="administrador" <?= $user['role'] === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                            <option value="operador" <?= $user['role'] === 'operador' ? 'selected' : '' ?>>Operador</option>
                                        </select>
                                        <button type="submit" name="change_role">Alterar Role</button>
                                    </form>
                                </td>
                                <td>
                                    <a href="admin.php?delete_user=<?= $user['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h4>Logs de Acesso dos Usuários</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Página Acessada</th>
                            <th>Data e Hora de Acesso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= htmlspecialchars($log['username'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($log['page_accessed'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($log['access_time'], ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>
</body>
</html>