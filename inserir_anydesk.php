<?php
session_start();
include "DB_connection.php";

// Verificando se o ID foi passado via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Buscar o chamado específico pelo ID
    $sql = "SELECT * FROM chamados WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    $chamado = $stmt->fetch();

    if (!$chamado) {
        echo "Chamado não encontrado!";
        exit();
    }
} else {
    echo "ID não fornecido.";
    exit();
}

// Verificando se o formulário foi enviado para atualizar o ID AnyDesk
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['anydesk_id'])) {
        $anydesk_id = $_POST['anydesk_id'];

        // Atualizando o ID AnyDesk no banco de dados
        $updateSql = "UPDATE chamados SET anydesk_id = :anydesk_id WHERE id = :id";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->execute([':anydesk_id' => $anydesk_id, ':id' => $id]);

        // Redirecionando de volta para a página de consulta após a atualização
        header("Location: cadastro_anydesk.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Chamado</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        h4.title {
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 16px;
            font-weight: bold;
            color: #343a40;
            display: block;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
            background-color: #f0f0f0;
        }

        .form-control:focus {
            border-color: #007bff;
            background-color: #f0f0f0;
        }

        .form-control-editable {
            background-color: #fff;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn {
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #6c757d;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
    <script>
        // Função para confirmar a ação de salvar
        function confirmSave(event) {
            if (!confirm("Tem certeza de que deseja salvar as alterações?")) {
                event.preventDefault();
            }
        }

        // Função para confirmar a ação de voltar
        function confirmCancel() {
            return confirm("Tem certeza de que deseja voltar sem salvar?");
        }
    </script>
</head>
<body>
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <div class="container">
                <h4 class="title">Visualizar Chamado</h4>
                <form method="POST" action=""> 

                    <!-- Exibindo os dados do chamado, mas bloqueados para edição -->
                    <div class="form-group">
                        <label for="anydesk_id">ID AnyDesk:</label>
                        <input type="text" id="anydesk_id" class="form-control form-control-editable" name="anydesk_id" value="<?php echo htmlspecialchars($chamado['anydesk_id']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="categoria">Categoria:</label>
                        <input type="text" id="categoria" class="form-control" value="<?php echo htmlspecialchars($chamado['categoria']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="equipamento">Equipamento:</label>
                        <input type="text" id="equipamento" class="form-control" value="<?php echo htmlspecialchars($chamado['equipamento']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="secretaria">Secretaria:</label>
                        <input type="text" id="secretaria" class="form-control" value="<?php echo htmlspecialchars($chamado['secretaria']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="setor">Setor:</label>
                        <input type="text" id="setor" class="form-control" value="<?php echo htmlspecialchars($chamado['setor']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="responsavel">Responsável:</label>
                        <input type="text" id="responsavel" class="form-control" value="<?php echo htmlspecialchars($chamado['responsavel']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="numero_serie">Número de Série:</label>
                        <input type="text" id="numero_serie" class="form-control" value="<?php echo htmlspecialchars($chamado['numero_serie']); ?>" readonly>
                    </div>

                    <!-- Botões de ação -->
                    <div class="btn-container">
                        <a href="cadastro_anydesk.php" class="btn btn-primary" onclick="return confirmCancel();">Voltar para Consulta</a>
                        <button type="submit" class="btn btn-success" onclick="confirmSave(event)">Salvar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <div class="footer">
        <p>© 2025 T.I - PMSPA | <a href="cadastro_anydesk.php">Voltar para consulta</a></p>
    </div>
</body>
</html>
