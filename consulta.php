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
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 95%;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow-x: auto;
    }
    .title {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        color: #343a40;
        margin-bottom: 20px;
    }
    .search-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
        justify-content: center;
    }
    .search-input, .search-select {
        flex: 1 1 200px;
        padding: 8px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
        outline: none;
        width:150px;
    }
    .table-container {
        width: 100%;
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        table-layout: fixed;
    }
    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        word-wrap: break-word;
    }
    th {
        background-color: #007bff;
        color: #fff;
    }
    tr:nth-child(even) {
        background-color: #f1f1f1;
    }
    .actions {
        display: flex;
        gap: 5px;
    }
    .edit-btn, .delete-btn {
        padding: 5px 8px;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        color: white;
    }
    .edit-btn { background-color: #28a745; }
    .delete-btn { background-color: #dc3545; }
    .edit-btn:hover { background-color: #218838; }
    .delete-btn:hover { background-color: #c82333; }

    @media (max-width: 768px) {
        .table-container {
            overflow-x: auto;
        }
        table {
            font-size: 12px;
        }
        th, td {
            padding: 8px;
        }
        .search-container {
            flex-direction: column;
        }
        .search-input, .search-select {
            width: 100%;
        }
    }
    .print-button {
        position: absolute;
        top: 100px;
        right: 40px;
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 5px;
        border: none;
        cursor: pointer;
    }

    .print-button:hover {
        background-color: #0056b3;
    }

    .print-button i {
        font-size: 18px;
    }
    </style>
</head>

<?php
include "DB_connection.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$filter = isset($_GET['filter']) ? $_GET['filter'] : "equipamento"; 

$whereClauses = [];
$sql = "SELECT * FROM chamados WHERE 1=1 ";

if ($search) {
    $whereClauses[] = "($filter LIKE :search)";
}

if (!empty($whereClauses)) {
    $sql .= " AND " . implode(" AND ", $whereClauses);
}

$sql .= " ORDER BY data_cadastro DESC"; // Remover a parte de LIMIT e OFFSET

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
            <div class="dashboard-container">
                <h4 class="title">Consulta de Equipamentos</h4>
                <br>
                <br> 
                <table id="tabelaEquipamentos">
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Equipamento</th>
                            <th>Secretaria</th>
                            <th>Setor</th>
                            <th>Responsável</th>
                            <th>Número de Série</th>


                            <?php if ($filter == "roteador"): ?>
                                <th>SSID</th>
                                <th>IP DA WAN</th>
                            <?php else: ?>
                                <th>Nome do Computador</th>
                                <th>Endereço IP</th>
                            <?php endif; ?>

                            <th>Ações</th>
                        </tr>
                        <button onclick="printFilteredTable()" class="print-button">
                        <i class="fa fa-print"></i> Imprimir
                        </button>
                        <tr>
                            <th>
                                <select id="searchCategoria" class="search-select" onchange="filterTable()">
                                    <option value="">Selecione...</option>
                                    <option value="Computador">Computador</option>
                                    <option value="Notebook">Notebook</option>
                                    <option value="Impressora">Impressora</option>
                                    <option value="Monitor">Monitor</option>
                                    <option value="Nobreak">Nobreak</option>
                                    <option value="DockStation">DockStation</option>
                                    <option value="Roteador">Roteador</option>
                                    <option value="Outros">Outros</option>
                                </select>
                            </th>
                            <th><input type="text" id="searchEquipamento" class="search-input" placeholder="Buscar Equipamento" onkeyup="filterTable()"></th>
                            <th>
                                <select id="searchSecretaria" class="search-select" onchange="filterTable()">
                                    <option value="">Selecione...</option>
                                    <option value="Administração">Administração</option>
                                    <option value="Agricultura">Agricultura</option>
                                    <option value="Assistência Social">Assistência Social</option>
                                    <option value="Controladoria">Controladoria</option>
                                    <option value="Cultura">Cultura</option>
                                    <option value="Desenvolvimento Econômico">Desenvolvimento Econômico</option>
                                    <option value="Educação">Educação</option>
                                    <option value="Esporte e Lazer">Esporte e Lazer</option>
                                    <option value="Fazenda">Fazenda</option>
                                    <option value="Governo">Governo</option>
                                    <option value="Licitações e Contratos">Licitações e Contratos</option>
                                    <option value="Meio Ambiente">Meio Ambiente</option>
                                    <option value="Obras">Obras</option>
                                    <option value="Ordem Pública">Ordem Pública</option>
                                    <option value="Planejamento e Gestão">Planejamento e Gestão</option>
                                    <option value="Previspa">Previspa</option>
                                    <option value="Procon">Procon</option>
                                    <option value="Procuradoria Geral">Procuradoria Geral</option>
                                    <option value="Saúde">Saúde</option>
                                    <option value="Serviços Públicos">Serviços Públicos</option>
                                    <option value="Turismo">Turismo</option>
                                    <option value="Segurança">Segurança</option>
                                    <!-- Opcões de Secretaria -->
                                </select>
                            </th>
                            <th><input type="text" id="searchSetor" class="search-input" placeholder="Buscar Setor" onkeyup="filterTable()"></th>
                            <th><input type="text" id="searchResponsavel" class="search-input" placeholder="Buscar Responsável" onkeyup="filterTable()"></th>
                                
                            <?php if ($filter == "roteador"): ?>
                                <th><input type="text" id="searchSSID" class="search-input" placeholder="Buscar SSID" onkeyup="filterTable()"></th>
                                <th><input type="text" id="searchIPWAN" class="search-input" placeholder="Buscar IP DA WAN" onkeyup="filterTable()"></th>
                            <?php else: ?>
                                <th><input type="text" id="searchNumerodeserie" class="search-input" placeholder="Buscar Nº de Série" onkeyup="filterTable()"></th>
                                <th><input type="text" id="searchComputador" class="search-input" placeholder="Nome do Computador" onkeyup="filterTable()"></th>
                                <th><input type="text" id="searchIP" class="search-input" placeholder="Numero do IP" onkeyup="filterTable()"></th>
                            <?php endif; ?>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="resultsTable">
                        <?php foreach ($chamados as $chamado) : ?>
                        <tr>
                            <td><?= htmlspecialchars($chamado['categoria']); ?></td>
                            <td><?= htmlspecialchars($chamado['equipamento']); ?></td>
                            <td><?= htmlspecialchars($chamado['secretaria']); ?></td>
                            <td><?= htmlspecialchars($chamado['setor']); ?></td>
                            <td><?= htmlspecialchars($chamado['responsavel']); ?></td>
                            <td><?= htmlspecialchars($chamado['numero_serie']); ?></td>


                            <?php if ($filter == "roteador"): ?>
                                <td><?= htmlspecialchars($chamado['ssid']); ?></td>
                                <td><?= htmlspecialchars($chamado['ip_wan']); ?></td>
                            <?php else: ?>
                                <td><?= htmlspecialchars($chamado['nome_computador']); ?></td>
                                <td><?= htmlspecialchars($chamado['ip']); ?></td>
                            <?php endif; ?>
                            <td class="actions">
                           <!-- Se for Roteador, redireciona para editar_roteador.php -->
                            <?php if ($chamado['categoria'] == 'Roteador'): ?>
                                <a href="editar_roteador.php?id=<?= $chamado['id']; ?>" class="edit-btn" style="background-color: #007bff;">Consulta</a>
                            <?php else: ?>
                                <a href="editar.php?id=<?= $chamado['id']; ?>" class="edit-btn">Editar</a>
                            <?php endif; ?>

                            <!-- Botão Excluir redireciona para excluir.php -->
                            <a href="excluir.php?id=<?= $chamado['id']; ?>" class="delete-btn" onclick="return confirm('Tem certeza que deseja excluir este item?');">Excluir</a>

                        </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="footer">
        <p>Gerado por Sistema de Suporte | Versão 1.0</p>
    </div>
    <div class="datetime">
        <p><?= date('d/m/Y H:i:s'); ?></p>
    </div>
    <script>
        function filterTable() {
    const inputs = document.querySelectorAll('.search-input, .search-select');
    const rows = document.querySelectorAll('#resultsTable tr');
    
    rows.forEach(row => {
        let showRow = true;

        // Verifica cada input (caixa de texto ou select)
        inputs.forEach((input, index) => {
            const cell = row.cells[index];
            
            if (input.tagName.toLowerCase() === 'select') {
                // Verifica se o valor da opção foi selecionado
                if (input.value && !cell.textContent.toLowerCase().includes(input.value.toLowerCase())) {
                    showRow = false;
                }
            } else if (input.tagName.toLowerCase() === 'input') {
                // Verifica se o valor digitado corresponde ao conteúdo da célula
                if (cell && !cell.textContent.toLowerCase().includes(input.value.toLowerCase())) {
                    showRow = false;
                }
            }
        });

        // Exibe ou oculta a linha com base na filtragem
        row.style.display = showRow ? '' : 'none';
    });
}

    </script>
        <script>
function printFilteredTable() {
    let table = document.getElementById('tabelaEquipamentos');
    let rows = document.querySelectorAll('#resultsTable tr');

    // Criar nova página para impressão
    let printWindow = window.open('', '', 'width=900,height=700');
    
    printWindow.document.write('<html><head><title>Impressão</title>');
    printWindow.document.write('<style>');
    printWindow.document.write(`
        @page { size: landscape; } /* Define a orientação padrão para paisagem */
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; font-size: 12px; text-align: center; }
        h2 { display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 14px; margin-bottom: 10px; }
        .logo { width: 30px; height: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
        th, td { padding: 6px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #007bff; color: #fff; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        .no-print { display: none; }
        .count-cell { width: 25px; text-align: center; font-size: 10px; color: #555; }
    `);
    printWindow.document.write('</style></head><body>');
    
    // Adiciona o título com a logo
    printWindow.document.write(`
        <h2>
            <img src="/img/favicon-pmspa.png" class="logo" alt="Logo">
            Setor de T.I. - Consulta de Equipamentos
        </h2>
    `);
    
    printWindow.document.write('<table>');
    
    // Captura cabeçalho e adiciona coluna de contagem
    let headerCells = table.querySelector('thead tr').cloneNode(true).children;
    let headerHTML = '<tr><th class="count-cell">#</th>'; // Adiciona numeração
    for (let i = 0; i < headerCells.length - 1; i++) { // Remove a última coluna (Ações)
        headerHTML += headerCells[i].outerHTML;
    }
    headerHTML += '</tr>';
    
    printWindow.document.write('<thead>' + headerHTML + '</thead><tbody>');

    // Captura apenas linhas visíveis e remove última coluna
    let count = 1;
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            let rowCells = row.cloneNode(true).children;
            let rowHTML = `<tr><td class="count-cell">${count++}</td>`; // Adiciona numeração
            for (let i = 0; i < rowCells.length - 1; i++) { // Remove a última coluna
                rowHTML += rowCells[i].outerHTML;
            }
            rowHTML += '</tr>';
            printWindow.document.write(rowHTML);
        }
    });

    printWindow.document.write('</tbody></table>');
    printWindow.document.write('</body></html>');

    printWindow.document.close();
    printWindow.print();
}
</script>



</body>
</html>
