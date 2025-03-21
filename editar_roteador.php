<?php
// Iniciar a sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir o arquivo de conexão com o banco de dados
include 'DB_connection.php';  // Certifique-se de que o caminho esteja correto

// Verificar se o ID foi passado pela URL
if (!isset($_GET['id'])) {
    header("Location: consulta.php?error=ID inválido");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM chamados WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);
$chamado = $stmt->fetch();

if (!$chamado) {
    header("Location: consulta.php?error=Registro não encontrado");
    exit();
}

// Processar o formulário quando enviado via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar e sanitizar dados recebidos do formulário
    $equipamento = htmlspecialchars($_POST['equipamento'] ?? '');
    $categoria = htmlspecialchars($_POST['categoria'] ?? '');
    $ssid = htmlspecialchars($_POST['ssid'] ?? '');
    $senha = htmlspecialchars($_POST['senha'] ?? '');
    $ip_wan = htmlspecialchars($_POST['ip_wan'] ?? '');
    $usuario_admin = htmlspecialchars($_POST['usuario_admin'] ?? '');
    $senha_usuario = htmlspecialchars($_POST['senha_usuario'] ?? '');

    // Aqui podemos hash a senha para garantir segurança (caso seja necessário)
    // $senha = password_hash($senha, PASSWORD_DEFAULT); // Para senhas

    // Atualizar no banco de dados
    $sql = "UPDATE chamados SET 
                equipamento = :equipamento, 
                categoria = :categoria, 
                ssid = :ssid, 
                senha = :senha, 
                ip_wan = :ip_wan, 
                usuario_admin = :usuario_admin, 
                senha_usuario = :senha_usuario 
            WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':equipamento' => $equipamento,
        ':categoria' => $categoria,
        ':ssid' => $ssid,
        ':senha' => $senha,  // Senha em texto plano ou pode ser criptografada
        ':ip_wan' => $ip_wan,
        ':usuario_admin' => $usuario_admin,
        ':senha_usuario' => $senha_usuario,
        ':id' => $id
    ]);

    // Redirecionar com mensagem de sucesso
    header("Location: consulta.php?success=Registro atualizado com sucesso");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Roteador</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h4 {
            text-align: center;
            color: #343a40;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
            background: #fff;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            color: #fff;
            transition: background 0.3s;
        }
        .btn-success { background-color: #28a745; }
        .btn-danger { background-color: #dc3545; }
        .btn-success:hover { background-color: #218838; }
        .btn-danger:hover { background-color: #c82333; }
        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
    </style>
</head>
<body>
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <h4 class="title text-center">Editar Roteador</h4>
            <div class="container">
                <form method="POST" action="" onsubmit="return confirmarSalvar();">
                    <div class="form-group">
                        <label for="equipamento">Equipamento:</label>
                        <input type="text" id="equipamento" name="equipamento" class="form-control" value="<?php echo htmlspecialchars($chamado['equipamento']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="categoria">Categoria:</label>
                        <input type="text" id="categoria" name="categoria" class="form-control" value="<?php echo htmlspecialchars($chamado['categoria']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="ssid">SSID:</label>
                        <input type="text" id="ssid" name="ssid" class="form-control" value="<?php echo htmlspecialchars($chamado['ssid']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="text" id="senha" name="senha" class="form-control" value="<?php echo htmlspecialchars($chamado['senha']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="ip_wan">IP da WAN:</label>
                        <input type="text" id="ip_wan" name="ip_wan" class="form-control" value="<?php echo htmlspecialchars($chamado['ip_wan']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="usuario_admin">Usuário Administrador:</label>
                        <input type="text" id="usuario_admin" name="usuario_admin" class="form-control" value="<?php echo htmlspecialchars($chamado['usuario_admin']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="senha_usuario">Senha do Usuário:</label>
                        <input type="text" id="senha_usuario" name="senha_usuario" class="form-control" value="<?php echo htmlspecialchars($chamado['senha_usuario']); ?>" required>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <a href="consulta.php" class="btn btn-danger">Cancelar</a>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <script>
        // Função de confirmação de salvar
        function confirmarSalvar() {
            return confirm('Tem certeza que deseja salvar as alterações?');
        }
    </script>
</body>
</html>
