<?php
include "DB_connection.php";

// Parâmetros de pesquisa e paginação
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5; // Alterado para 5 itens por vez

// Determinar o deslocamento
$offset = ($page - 1) * $limit;

// Consulta SQL com limite e deslocamento
$sql = "SELECT * FROM chamados WHERE numero_serie LIKE :search OR responsavel LIKE :search ORDER BY data_cadastro DESC LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$chamados = $stmt->fetchAll();

// Total de resultados
$totalQuery = "SELECT COUNT(*) as total FROM chamados WHERE numero_serie LIKE :search OR responsavel LIKE :search";
$totalStmt = $conn->prepare($totalQuery);
$totalStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$totalStmt->execute();
$totalResult = $totalStmt->fetch();
$totalResults = $totalResult['total'];
$totalPages = ceil($totalResults / $limit);

// Gerar o HTML da tabela
$dataHtml = '';
if (count($chamados) > 0) {
    foreach ($chamados as $chamado) {
        $dataHtml .= "<tr>
                        <td>" . htmlspecialchars($chamado['categoria']) . "</td>
                        <td>" . htmlspecialchars($chamado['equipamento']) . "</td>
                        <td>" . htmlspecialchars($chamado['secretaria']) . "</td>
                        <td>" . htmlspecialchars($chamado['setor']) . "</td>
                        <td>" . htmlspecialchars($chamado['responsavel']) . "</td>
                        <td>" . htmlspecialchars($chamado['numero_serie']) . "</td>
                    </tr>";
    }
} else {
    $dataHtml .= "<tr><td colspan='6'>Nenhum resultado encontrado.</td></tr>";
}

// Retornar os dados e a quantidade total de páginas
echo json_encode([
    'data' => $dataHtml,
    'totalPages' => $totalPages
]);
?>
