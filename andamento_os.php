<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conexão com o banco de dados
$servername = "localhost"; // ou o servidor do seu banco
$username = "root";
$password = "";
$dbname = "task_management_db";

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Pegar o número da página atual
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6; // Número de itens por página
$offset = ($page - 1) * $limit;

// Consulta para pegar os dados da tabela ordens_servico com a paginação
$sql = "SELECT * FROM ordens_servico LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Verificar se há resultados
if ($result->num_rows > 0) {
    // Criar o array de ordens_servico
    while ($row = $result->fetch_assoc()) {
        $ordens_servico[] = $row;
    }
} else {
    $ordens_servico = [];
}

// Pegar o total de itens para calcular o número de páginas
$sql_total = "SELECT COUNT(*) AS total FROM ordens_servico";
$total_result = $conn->query($sql_total);
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['total'];
$total_pages = ceil($total_items / $limit);

$conn->close(); // Fechar a conexão com o banco
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordens de Serviço</title>
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
            max-width: 85%;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .title {
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 20px;
        }
        .button-container {
            text-align: center;
            margin-top: 15px;
        }
        .button-container button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button-container button:hover {
            background-color: #0056b3;
        }
        .pagination a {
            padding: 8px 15px;
            margin: 0 5px;
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
        }
        .pagination a:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9ecef;
            cursor: pointer;
        }
        td {
            border-bottom: 1px solid #ddd;
        }
        .form-container {
            margin-top: 20px;
            text-align: center;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-container input, .form-container textarea {
            width: 300px;
            padding: 10px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin: 10px 0;
            transition: border-color 0.3s;
        }
        .form-container input:focus, .form-container textarea:focus {
            border-color: #007bff;
            outline: none;
        }
        .form-container label {
            font-weight: bold;
            color: #495057;
            display: block;
            margin-bottom: 8px;
        }
        .form-container textarea {
            height: 100px;
        }
    </style>
</head>

<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <div class="container">
                <h4 class="title">Ordens de Serviço Cadastradas</h4>

                <!-- Botões para "Nova OS" e "Finalizadas" -->
                <div class="button-container">
                    <button onclick="window.location.href='nova_os.php'">Nova OS</button>
                    <button onclick="window.location.href='finalizadas.php'">Finalizadas</button>
                </div>

                <!-- Campo de pesquisa -->
                <div class="search-container" style="text-align: center; margin-top: 20px;">
                    <input type="text" id="searchInput" class="search-input" placeholder="Buscar por qualquer campo" onkeyup="searchData()">
                </div>

                <!-- Tabela de resultados -->
                <table>
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Equipamento</th>
                            <th>Secretaria</th>
                            <th>Setor</th>
                            <th>Responsável</th>
                            <th>Número Série</th>
                            <th>Número OS</th>
                            <th>Solicitante</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th>Descrição</th>
                            <th>Data de Criação</th>
                        </tr>
                    </thead>
                    <tbody id="resultsTable">
                        <?php
                        if (!empty($ordens_servico)) {
                            foreach ($ordens_servico as $item) {
                                echo "<tr onclick=\"preencherFormulario({$item['id']}, '{$item['descricao']}')\">";
                                echo "<td>{$item['categoria']}</td>";
                                echo "<td>{$item['equipamento']}</td>";
                                echo "<td>{$item['secretaria']}</td>";
                                echo "<td>{$item['setor']}</td>";
                                echo "<td>{$item['responsavel']}</td>";
                                echo "<td>{$item['numero_serie']}</td>";
                                echo "<td>{$item['numero_os']}</td>";
                                echo "<td>{$item['solicitante']}</td>";
                                echo "<td>{$item['prioridade']}</td>";
                                echo "<td>{$item['status']}</td>";
                                echo "<td>{$item['descricao']}</td>";
                                echo "<td>{$item['data_criacao']}</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='12'>Nenhuma ordem de serviço encontrada.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Paginação -->
                <div class="pagination" id="pagination">
                    <!-- Links de navegação serão inseridos aqui -->
                </div>

                <!-- Formulário para exibir descrição e registrar número da OS -->
                <div class="form-container">
                    <h5>Registrar Número da OS</h5>
                    <br>
                    <label for="descricaoProblema">Descrição do Problema</label>
                    <textarea id="descricaoProblema" readonly placeholder="Descrição do problema selecionado"></textarea>

                    <label for="numeroOs">Número da OS</label>
                    <input type="text" id="numeroOs" placeholder="Digite o número da OS" />

                    <div class="button-container">
                        <button onclick="registrarNumeroOS()">Registrar</button>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <div class="footer">
        <p>Direitos Reservados - T.I - PMSPA 2025</p>
    </div>
    <div class="datetime">
        <p><?= date('d/m/Y H:i:s'); ?></p>
    </div>
    <!-- Campo oculto para armazenar o ID do chamado -->
    <input type="hidden" id="idChamado" />
    <script>
        let currentPage = 1;
        const itemsPerPage = 6;

        function searchData() {
            const searchValue = document.getElementById('searchInput').value;
            fetchData(searchValue);
        }

        function fetchData(searchValue = '') {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `search_chamados_os.php?search=${encodeURIComponent(searchValue)}&page=${currentPage}&limit=${itemsPerPage}`, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    let tableContent = '';
                    let pagination = '';

                    if (response.data.length > 0) {
                        response.data.forEach(function(item) {
                            tableContent += `<tr onclick="preencherFormulario(${item.id}, '${item.descricao}')">
                                                <td>${item.categoria}</td>
                                                <td>${item.equipamento}</td>
                                                <td>${item.secretaria}</td>
                                                <td>${item.setor}</td>
                                                <td>${item.responsavel}</td>
                                                <td>${item.numero_serie}</td>
                                                <td>${item.numero_os}</td>
                                                <td>${item.solicitante}</td>
                                                <td>${item.prioridade}</td>
                                                <td>${item.status}</td>
                                                <td>${item.descricao}</td>
                                                <td>${item.data_criacao}</td>
                                            </tr>`;
                        });

                        const totalPages = Math.ceil(response.totalItems / itemsPerPage);

                        if (currentPage > 1) {
                            pagination += `<a href="javascript:void(0);" onclick="goToPage(${currentPage - 1})">Anterior</a>`;
                        }

                        if (currentPage < totalPages) {
                            pagination += `<a href="javascript:void(0);" onclick="goToPage(${currentPage + 1})">Próxima Página</a>`;
                        }
                    } else {
                        tableContent = `<tr><td colspan="12">Nenhuma ordem de serviço encontrada.</td></tr>`;
                    }

                    document.getElementById('resultsTable').innerHTML = tableContent;
                    document.getElementById('pagination').innerHTML = pagination;
                }
            };
            xhr.send();
        }

        function goToPage(page) {
            currentPage = page;
            const searchValue = document.getElementById('searchInput').value;
            fetchData(searchValue);
        }

        function preencherFormulario(id, descricao) {
            document.getElementById('descricaoProblema').value = descricao;
            document.getElementById('numeroOs').value = ''; // Limpa o campo do número da OS
            document.getElementById('idChamado').value = id; // Define o ID do chamado
        }

        function registrarNumeroOS() {
            const numeroOs = document.getElementById('numeroOs').value;
            const descricaoProblema = document.getElementById('descricaoProblema').value;
            const id = document.getElementById('idChamado').value; // ID do chamado

            if (numeroOs === '') {
                alert('Por favor, preencha o número da OS.');
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'save_os.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);

                    if (response.status === 'success') {
                        alert(response.message);
                        // Limpa o formulário após o sucesso
                        document.getElementById('numeroOs').value = '';
                        document.getElementById('descricaoProblema').value = '';
                    } else {
                        alert(response.message);
                    }
                }
            };

            xhr.send('numero_os=' + encodeURIComponent(numeroOs) + 
                    '&descricao_problema=' + encodeURIComponent(descricaoProblema) + 
                    '&id=' + encodeURIComponent(id));
        }
    </script>
</body>
</html>
