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
    <title>Nova Ordem de Serviço</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Seu estilo aqui */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 70%;
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
            margin-bottom: 20px;
        }
        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }
        .search-input {
            width: 250px;
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
        .filters-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .filter-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 10px;
        }
        .filter-button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px 12px;
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
        .form-container {
            margin-top: 30px;
        }
        .form-container input, .form-container select, .form-container textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-container textarea {
            height: 150px;
        }
        .form-container button {
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #218838;
        }
        .status-message {
            display: none;
            padding: 10px;
            margin-top: 15px;
            text-align: center;
            border-radius: 5px;
        }

        /* Novo estilo para a paginação */
        #pagination {
            text-align: center;
            margin-top: 20px;
        }

        #pagination button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 10px;
        }

        #pagination button:hover {
            background-color: #0056b3;
        }

        #pagination button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<script>
    let currentPage = 1;
    const itemsPerPage = 5;
    let totalPages = 1;
    let currentFilter = ''; // Variável para armazenar o filtro de condição

    // Função para buscar os dados com base no número de série
    function searchData() {
        const searchValue = document.getElementById('searchNumeroSerie').value;

        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'search_chamados.php?search=' + encodeURIComponent(searchValue) + '&page=' + currentPage + '&limit=' + itemsPerPage + '&condicao=' + currentFilter, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                document.getElementById('resultsTable').innerHTML = response.data;
                totalPages = response.totalPages;
                generatePagination();
            }
        };
        xhr.send();
    }

    // Função para filtrar as OS com base na condição
    function filterOS(condicao) {
        currentFilter = condicao; // Atualiza o filtro de condição
        searchData(); // Reexecuta a pesquisa com o filtro aplicado
    }

    // Função para gerar a paginação
    function generatePagination() {
        const paginationHTML = `
            <button ${currentPage === 1 ? 'disabled' : ''} onclick="changePage('previous')">Anterior</button>
            <button ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage('next')">Próximo</button>
        `;
        document.getElementById('pagination').innerHTML = paginationHTML;
    }

    // Função para alterar a página
    function changePage(direction) {
        if (direction === 'previous' && currentPage > 1) {
            currentPage--;
        } else if (direction === 'next' && currentPage < totalPages) {
            currentPage++;
        }
        searchData();
    }

    // Função para preencher os dados no formulário ao dar duplo clique em uma linha da tabela
    function selectEquipment(categoria, equipamento, secretaria, setor, responsavel, numeroSerie, motivoOS) {
        const dataAbertura = new Date().toLocaleDateString('pt-BR'); // Data atual

        // Preenche os campos do formulário com as informações do equipamento
        document.getElementById('motivoOS').value = motivoOS; // Preenche o campo "Motivo da OS"
        document.getElementById('dataAbertura').value = dataAbertura;
        document.getElementById('prioridade').value = 'media'; // Valor padrão
        document.getElementById('solicitante').value = ''; // Solicitação em branco
        document.getElementById('condicao').value = 'aberta'; // Condição como "Aberta"
        document.getElementById('descricao').value = ''; // Descrição em branco

        // Armazenar os dados do equipamento para envio
        window.selectedEquipment = {
            categoria: categoria,
            equipamento: equipamento,
            secretaria: secretaria,
            setor: setor,
            responsavel: responsavel,
            numeroSerie: numeroSerie
        };
    }

    // Função para salvar a Ordem de Serviço
   // Função para salvar a Ordem de Serviço
        function saveOS() {
            const motivoOS = document.getElementById('motivoOS').value;
            const solicitante = document.getElementById('solicitante').value;
            const prioridade = document.getElementById('prioridade').value;
            const condicao = document.getElementById('condicao').value; // Alterado de status para condicao
            const descricao = document.getElementById('descricao').value;
            const dataAbertura = document.getElementById('dataAbertura').value;
            const numeroOS = 'OS_' + Date.now(); // Gerando um número de OS único

            // Verifique se os campos obrigatórios foram preenchidos
            if (!motivoOS || !solicitante || !descricao) {
                alert("Por favor, preencha todos os campos obrigatórios.");
                return;
            }

            // Enviar os dados via AJAX para o backend (PHP)
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'save_os.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');  // Enviar como JSON
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    const statusMessage = document.getElementById('statusMessage');
                    statusMessage.style.display = 'block';
                    if (response.success) {
                        statusMessage.style.backgroundColor = '#28a745'; // verde para sucesso
                        statusMessage.style.color = 'white';
                        statusMessage.innerHTML = 'Ordem de Serviço cadastrada com sucesso!';

                        // Limpar os campos de inserção após sucesso
                        clearForm();
                    } else {
                        statusMessage.style.backgroundColor = '#dc3545'; // vermelho para erro
                        statusMessage.style.color = 'white';
                        statusMessage.innerHTML = 'Erro ao cadastrar a OS: ' + response.error;
                    }
                }
            };

            // Preparar os dados para envio via JSON
            const formData = {
                numero_os: numeroOS, motivoOS, solicitante, prioridade, condicao, descricao, // Alterado de status para condicao
                categoria: window.selectedEquipment.categoria,
                equipamento: window.selectedEquipment.equipamento,
                secretaria: window.selectedEquipment.secretaria,
                setor: window.selectedEquipment.setor,
                responsavel: window.selectedEquipment.responsavel,
                numeroSerie: window.selectedEquipment.numeroSerie,
                dataAbertura
            };

            // Enviar via POST
            xhr.send(JSON.stringify(formData));
        }

        // Função para limpar os campos do formulário após salvar a OS
        function clearForm() {
            document.getElementById('motivoOS').value = '';
            document.getElementById('dataAbertura').value = '';
            document.getElementById('prioridade').value = 'media'; // Resetando para o valor padrão
            document.getElementById('solicitante').value = '';
            document.getElementById('condicao').value = 'aberta'; // Resetando para o valor padrão
            document.getElementById('descricao').value = '';
        }


    $(document).ready(function() {
        $('#resultsTable').on('dblclick', 'tr', function() {
            const categoria = $(this).find('td').eq(0).text();
            const equipamento = $(this).find('td').eq(1).text();
            const secretaria = $(this).find('td').eq(2).text();
            const setor = $(this).find('td').eq(3).text();
            const responsavel = $(this).find('td').eq(4).text();
            const numeroSerie = $(this).find('td').eq(5).text();
            const motivoOS = $(this).find('td').eq(6).text();  // Assuming the "Motivo da OS" is in the 7th column (index 6)

            selectEquipment(categoria, equipamento, secretaria, setor, responsavel, numeroSerie, motivoOS);
        });
    });
</script>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <div class="container">
                <h4 class="title">Cadastro de Ordens de Serviço</h4>

                <!-- Mensagem de status -->
                <div id="statusMessage" class="status-message"></div>

                <!-- Botões de filtro -->
                <div class="filters-container">
                    <button class="filter-button" onclick="window.location.href='andamento_os.php'">Em Andamento</button>
                    <button class="filter-button" onclick="filterOS('finalizada')">Finalizadas</button>
                </div>

                <!-- Campo de pesquisa -->
                <div class="search-container">
                    <input type="text" id="searchNumeroSerie" class="search-input" placeholder="Buscar por Nº de Série" onkeyup="searchData()">
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
                            <th>Número de Série</th>
                        </tr>
                    </thead>
                    <tbody id="resultsTable">
                        <!-- Os resultados da pesquisa serão inseridos aqui via AJAX -->
                    </tbody>
                </table>

                <!-- Paginação -->
                <div id="pagination" class="pagination"></div>

                <!-- Formulário de detalhes -->
                <div class="form-container">
                    <h5>Detalhes da Ordem de Serviço</h5>

                    <label for="Motivo_os">Motivo da OS</label>
                    <input type="text" id="motivoOS" value="">

                    <label for="dataAbertura">Data de Abertura</label>
                    <input type="text" id="dataAbertura" readonly>

                    <label for="prioridade">Prioridade</label>
                    <select id="prioridade">
                        <option value="baixa">Baixa</option>
                        <option value="media">Média</option>
                        <option value="alta">Alta</option>
                    </select>

                    <label for="solicitante">Solicitante</label>
                    <input type="text" id="solicitante" value="">

                    <label for="condicao">Condição</label>
                    <select id="condicao">
                        <option value="aberta">Aberta</option>
                        <option value="em_andamento">Em Andamento</option>
                        <option value="finalizada">Finalizada</option>
                    </select>

                    <label for="descricao">Descrição</label>
                    <textarea id="descricao"></textarea>

                    <button type="button" onclick="saveOS()">Salvar OS</button>
                </div>
            </div>
        </section>
    </div>
    <footer class="footer">Sistema de Gestão de Ordens de Serviço</footer>
    <div class="datetime"><?php echo date("d/m/Y H:i:s"); ?></div>
</body>
</html>
