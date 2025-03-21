<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Equipamentos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 90%;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .title {
            text-align: center;
            font-size: 26px;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 30px;
        }
        .search-container {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 20px;
        }
        .search-input {
            width: 300px;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.3s;
        }
        .search-input:focus {
            border-color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
        }
        td {
            background-color: #f9f9f9;
        }
        tr:nth-child(even) td {
            background-color: #f1f1f1;
        }
        tr:hover td {
            background-color: #e9ecef;
        }
        .footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
        .datetime {
            position: fixed;
            bottom: 10px;
            right: 20px;
            font-size: 14px;
            color: #6c757d;
        }
        .insert-btn {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .insert-btn:hover {
            background-color: #218838;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>

<?php
include "DB_connection.php";

// Variável de busca
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Construa a consulta SQL com base nos parâmetros
$whereClauses = [];
$sql = "SELECT * FROM chamados WHERE (categoria = 'computador' OR categoria = 'notebook')";

if ($search) {
    $whereClauses[] = "(numero_serie LIKE :search OR responsavel LIKE :search)";
}

if (!empty($whereClauses)) {
    $sql .= " AND " . implode(" AND ", $whereClauses);
}

$sql .= " ORDER BY data_cadastro DESC";

$stmt = $conn->prepare($sql);

if ($search) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

$stmt->execute();
$chamados = $stmt->fetchAll();
?>

<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <div class="container">
                <h4 class="title">Consulta de Equipamentos</h4>

                <div class="search-container">
                    <input type="text" id="search" class="search-input" placeholder="Buscar por número de série ou usuário" value="<?= htmlspecialchars($search); ?>" oninput="searchData()">
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Equipamento</th>
                            <th>Secretaria</th>
                            <th>Setor</th>
                            <th>Responsável</th>
                            <th>Número de Série</th>
                            <th>ID AnyDesk</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="results">
                        <?php foreach ($chamados as $chamado) : ?>
                        <tr>
                            <td><?= htmlspecialchars($chamado['categoria']); ?></td>
                            <td><?= htmlspecialchars($chamado['equipamento']); ?></td>
                            <td><?= htmlspecialchars($chamado['secretaria']); ?></td>
                            <td><?= htmlspecialchars($chamado['setor']); ?></td>
                            <td><?= htmlspecialchars($chamado['responsavel']); ?></td>
                            <td><?= htmlspecialchars($chamado['numero_serie']); ?></td>
                            <td><?= htmlspecialchars($chamado['anydesk_id']); ?></td>
                            <td>
                                <!-- Redireciona para a página de Inserir ID -->
                                <a href="inserir_anydesk.php?id=<?= $chamado['id']; ?>" class="insert-btn">Inserir ID</a>
                                <!-- Botão Excluir ID -->
                                <button class="delete-btn" onclick="confirmDelete('<?= $chamado['id']; ?>')">Excluir ID</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="footer">
        <p>Direitos Reservados - T.I - PMSPA 2025</p>
    </div>
    <div class="datetime">
        <p><?= date('d/m/Y H:i:s'); ?></p>
    </div>

    <script>
        function searchData() {
            const searchValue = document.getElementById('search').value.toLowerCase();
            const rows = document.querySelectorAll('#results tr');
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const match = Array.from(cells).some(cell => {
                    return cell.textContent.toLowerCase().includes(searchValue);
                });
                row.style.display = match ? '' : 'none';
            });
        }

        function confirmDelete(id) {
            if (confirm("Tem certeza que deseja excluir este ID AnyDesk?")) {
                window.location.href = "excluir_anydesk.php?id=" + id;
            }
        }
    </script>
</body>
</html>
