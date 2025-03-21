<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "task_management_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Receber parâmetros
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? $_GET['limit'] : 6;
$offset = ($page - 1) * $limit;

// Consulta SQL com pesquisa em vários campos
$sql = "SELECT * FROM ordens_servico WHERE 
            categoria LIKE ? OR
            equipamento LIKE ? OR
            secretaria LIKE ? OR
            setor LIKE ? OR
            responsavel LIKE ? OR
            numero_serie LIKE ? OR
            numero_os LIKE ? OR
            solicitante LIKE ? OR
            prioridade LIKE ? OR
            status LIKE ? OR
            descricao LIKE ? 
        LIMIT $limit OFFSET $offset";

// Prepare e execute a consulta
$stmt = $conn->prepare($sql);
$searchParam = "%$search%";
$stmt->bind_param("sssssssssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);

$stmt->execute();
$result = $stmt->get_result();

// Recuperar os dados
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Contar o total de itens para a paginação
$sqlCount = "SELECT COUNT(*) as total FROM ordens_servico WHERE 
                    categoria LIKE ? OR
                    equipamento LIKE ? OR
                    secretaria LIKE ? OR
                    setor LIKE ? OR
                    responsavel LIKE ? OR
                    numero_serie LIKE ? OR
                    numero_os LIKE ? OR
                    solicitante LIKE ? OR
                    prioridade LIKE ? OR
                    status LIKE ? OR
                    descricao LIKE ?";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->bind_param("sssssssssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
$stmtCount->execute();
$countResult = $stmtCount->get_result();
$totalItems = $countResult->fetch_assoc()['total'];

echo json_encode([
    'data' => $data,
    'totalItems' => $totalItems
]);

$conn->close();
?>
