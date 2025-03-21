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

// Prevenir SQL Injection usando prepared statements
$sql = "SELECT * FROM chamados WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);  // Garantir que o parâmetro seja um número inteiro
$stmt->execute();
$chamado = $stmt->fetch();

// Verificar se o chamado existe no banco de dados
if (!$chamado) {
    header("Location: consulta.php?error=Registro não encontrado");
    exit();
}

// Processar o formulário de edição quando ele for enviado via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter dados do formulário
    $equipamento = $_POST['equipamento'];
    $categoria = $_POST['categoria'];
    $numero_serie = $_POST['numero_serie'];
    $secretaria = $_POST['secretaria'];
    $setor = $_POST['setor'];
    $responsavel = $_POST['responsavel'];
    $nome_computador = $_POST['nome_computador'];
    $ip = $_POST['ip'];
    $observacao = $_POST['observacao'];

    // Atualizar os dados no banco de dados
    $sql = "UPDATE chamados SET equipamento = :equipamento, categoria = :categoria, numero_serie = :numero_serie, secretaria = :secretaria, setor = :setor, responsavel = :responsavel, nome_computador = :nome_computador, ip = :ip, observacao = :observacao WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':equipamento', $equipamento);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':numero_serie', $numero_serie);
    $stmt->bindParam(':secretaria', $secretaria);
    $stmt->bindParam(':setor', $setor);
    $stmt->bindParam(':responsavel', $responsavel);
    $stmt->bindParam(':nome_computador', $nome_computador);
    $stmt->bindParam(':ip', $ip);
    $stmt->bindParam(':observacao', $observacao);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirecionar para a página de consulta com a mensagem de sucesso
    header("Location: consulta.php?success=Registro atualizado com sucesso");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Equipamento</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Estilo para o formulário de edição */
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
            <h4 class="title text-center">Editar Equipamento</h4>
            <div class="container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="equipamento">Equipamento:</label>
                        <input type="text" id="equipamento" name="equipamento" class="form-control" value="<?php echo htmlspecialchars($chamado['equipamento']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="categoria">Categoria:</label>
                        <select id="categoria" name="categoria" class="form-control" required>
                            <?php
                            $categorias = ["Computador", "Notebook", "Impressora", "Monitor", "Nobreak", "DockStation", "Outros"];
                            foreach ($categorias as $cat) {
                                $selected = ($chamado['categoria'] == $cat) ? "selected" : "";
                                echo "<option value='$cat' $selected>$cat</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="numero_serie">Nº de Série:</label>
                        <input type="text" id="numero_serie" name="numero_serie" class="form-control" value="<?php echo htmlspecialchars($chamado['numero_serie']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="secretaria">Secretaria:</label>
                        <select id="secretaria" name="secretaria" class="form-control" required>
                            <?php
                            $secretaria = ["Administração", "Agricultura", "Assistência Social", "Controladoria", "Cultura", "Desenvolvimento Econômico", "Educação", "Esporte e Lazer", "Fazenda", "Governo", "Licitações e Contratos", "Meio Ambiente", "Obras", "Ordem Pública", "Planejamento e Gestão", "Previspa", "Procon", "Procuradoria Geral", "Saúde", "Serviços Públicos", "Turismo", "Segurança"];
                            foreach ($secretaria as $cat) {
                                $selected = ($chamado['secretaria'] == $cat) ? "selected" : "";
                                echo "<option value='$cat' $selected>$cat</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="setor">Setor:</label>
                        <input type="text" id="setor" name="setor" class="form-control" value="<?php echo htmlspecialchars($chamado['setor']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="responsavel">Responsável:</label>
                        <input type="text" id="responsavel" name="responsavel" class="form-control" value="<?php echo htmlspecialchars($chamado['responsavel']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nome_computador">Nome do Computador</label>
                        <input type="text" id="nome_computador" name="nome_computador" class="form-control" value="<?php echo htmlspecialchars($chamado['nome_computador']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="ip">Endereço IP</label>
                        <input type="text" id="ip" name="ip" class="form-control" value="<?php echo htmlspecialchars($chamado['ip']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="observacao">Observação:</label>
                        <textarea id="observacao" name="observacao" class="form-control"><?php echo htmlspecialchars($chamado['observacao']); ?></textarea>
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
    document.querySelector("form").addEventListener("submit", function(event) {
        let confirmacao = confirm("Tem certeza que deseja salvar as alterações?");
        if (!confirmacao) {
            event.preventDefault();
        }
    });
</script>
</body>
</html>
