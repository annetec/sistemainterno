<?php 
session_start();
include "DB_connection.php";

if (!isset($_SESSION['role']) || !isset($_SESSION['id'])) {
    header("Location: login.php?error=First login");
    exit();
}

$user_id = $_SESSION['id'];

// Buscar usuário no banco de dados
$sql = "SELECT username AS nome, role FROM users WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

$user_nome = $user['nome'] ?? 'Desconhecido';
$user_role = $user['role'] ?? 'Desconhecido';

// Função para registrar logs
function registrar_log($conn, $usuario_nome, $usuario_role, $acao) {
    $sql = "INSERT INTO logs (usuario_nome, usuario_role, acao, data_hora) VALUES (:usuario_nome, :usuario_role, :acao, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['usuario_nome' => $usuario_nome, 'usuario_role' => $usuario_role, 'acao' => $acao]);
}

// Registrar acesso à página de logs
registrar_log($conn, $user_nome, $user_role, 'Acessou a página de logs');

// Buscar logs no banco de dados
$sql = "SELECT * FROM logs ORDER BY data_hora DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs do Sistema</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 900px;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
        }
        h4 {
            color: #343a40;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-back {
            display: block;
            width: 150px;
            margin: 20px auto;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include "inc/header.php"; ?>
    <div class="container">
        <h4>Logs do Sistema</h4>
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Usuário</th>
                        <th>Perfil</th>
                        <th>Ação</th>
                        <th>Data e Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo $log['id']; ?></td>
                        <td><?php echo htmlspecialchars($log['usuario_nome']); ?></td>
                        <td><?php echo htmlspecialchars($log['usuario_role']); ?></td>
                        <td><?php echo htmlspecialchars($log['acao']); ?></td>
                        <td><?php echo $log['data_hora']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="index.php" class="btn btn-primary btn-back">Voltar</a>
    </div>
</body>
</html>
