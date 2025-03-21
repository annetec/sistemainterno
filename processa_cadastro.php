<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inicia variáveis para mensagens
$msg = '';
$msg_class = '';

// Conexão com o banco de dados e lógica de inserção
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexão com o banco de dados
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "task_management_db"; // Nome correto do banco de dados

    // Criando a conexão
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificando a conexão
    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Coleta e sanitiza os dados do formulário
    $categoria = $conn->real_escape_string($_POST['categoria']);
    $equipamento = $conn->real_escape_string($_POST['equipamento']);
    $secretaria = $conn->real_escape_string($_POST['secretaria']);
    $setor = $conn->real_escape_string($_POST['setor']);
    $responsavel = $conn->real_escape_string($_POST['responsavel']);
    $nome_computador = isset($_POST['nome_computador']) ? $conn->real_escape_string($_POST['nome_computador']) : null;
    $ip = isset($_POST['ip']) ? $conn->real_escape_string($_POST['ip']) : null;
    $observacao = isset($_POST['observacao']) ? $conn->real_escape_string($_POST['observacao']) : null;

    // Campos para roteador (caso esteja visível)
    $ssid = isset($_POST['ssid']) ? $conn->real_escape_string($_POST['ssid']) : null;
    $senha = isset($_POST['senha']) ? $conn->real_escape_string($_POST['senha']) : null;
    $ip_wan = isset($_POST['ip_wan']) ? $conn->real_escape_string($_POST['ip_wan']) : null;
    $usuario_admin = isset($_POST['usuario_admin']) ? $conn->real_escape_string($_POST['usuario_admin']) : null;
    $senha_admin = isset($_POST['senha_admin']) ? $conn->real_escape_string($_POST['senha_admin']) : null;

    // Verifica se a categoria é "Roteador" e usa o SSID em vez do número de série
    if ($categoria === 'Roteador') {
        // Se for roteador, o número de série será o SSID
        $identificador = $ssid;
    } else {
        // Caso contrário, usa o número de série
        $identificador = isset($_POST['numero_serie']) ? $conn->real_escape_string($_POST['numero_serie']) : null;
    }

    // Data de criação (data atual)
    $data_criacao = date('Y-m-d H:i:s');

    // Consulta SQL para inserir os dados na tabela "chamados"
    $sql = "INSERT INTO chamados (categoria, equipamento, numero_serie, secretaria, setor, responsavel, nome_computador, ip, observacao, ssid, senha, ip_wan, usuario_admin, senha_admin, data_cadastro)
            VALUES ('$categoria', '$equipamento', '$identificador', '$secretaria', '$setor', '$responsavel', '$nome_computador', '$ip', '$observacao', '$ssid', '$senha', '$ip_wan', '$usuario_admin', '$senha_admin', '$data_criacao')";

    // Executando a consulta
    if ($conn->query($sql) === TRUE) {
        // Se a inserção for bem-sucedida, define a mensagem de sucesso
        $_SESSION['msg'] = "Cadastro realizado com sucesso!";
        $_SESSION['msg_class'] = "success";
        header("Location: my_task.php"); // Redireciona para a página de cadastro
    } else {
        // Se ocorrer erro
        $_SESSION['msg'] = "Erro ao cadastrar: " . $conn->error;
        $_SESSION['msg_class'] = "error";
        header("Location: my_task.php");
    }

    // Fechar a conexão
    $conn->close();
} else {
    echo "<script>
            alert('Método inválido');
            window.location.href = 'my_task.php'; // Redireciona para a página de cadastro
          </script>";
}
?>
