<?php 
session_start();
if (isset($_SESSION['username']) && isset($_SESSION['user_id'])) { // Corrigido para 'username'

    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";

    // Obtendo o número total de usuários
    $num_users = count_users($conn);

    // Obtendo a contagem de equipamentos por categoria
    function count_equipments_by_category($conn) {
        $query = "SELECT categoria, COUNT(*) as total FROM chamados GROUP BY categoria";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $equipments_by_category = count_equipments_by_category($conn);

    // Preparando arrays para o gráfico
    $categories = [];
    $category_counts = [];
    foreach ($equipments_by_category as $row) {
        $categories[] = $row['categoria'];
        $category_counts[] = $row['total'];
    }

    // Convertendo os arrays para JSON
    $categories_json = json_encode($categories);
    $category_counts_json = json_encode($category_counts);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Painel do Sistema</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Estilos Gerais */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .dashboard {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            max-width: 100%;
            margin-top: 10px;
        }

        .dashboard-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 180px;
            height: 120px;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.2);
        }

        .dashboard-item i {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 14px;
            color: gray;
        }

        .datetime {
            position: fixed;
            bottom: 10px;
            right: 20px;
            font-size: 14px;
            color: gray;
        }

        .title {
            text-align: center;
            margin-bottom: 20px;
        }

        .delta-container {
            margin-top: 30px;
            padding: 20px;
            width: 80%;
            max-width: 500px;
            background-color: #ecf0f1;
            border-radius: 10px;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1);
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        .delta-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .delta-form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .delta-form input[type="text"] {
            padding: 10px;
            margin-bottom: 10px;
            width: 100%;
            max-width: 400px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .delta-form button {
            padding: 10px;
            width: 100%;
            max-width: 400px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .delta-form button:hover {
            background-color: #2980b9;
        }

        .delta-response {
            margin-top: 20px;
            padding: 10px;
            background-color: #bdc3c7;
            border-radius: 5px;
            font-size: 16px;
            color: #2c3e50;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .dashboard {
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .dashboard-item {
                width: 100%;
                max-width: 350px;
                margin-bottom: 15px;
            }

            .chart-container {
                width: 100%;
                padding: 0 10px;
            }

            .delta-container {
                width: 90%;
                max-width: 450px;
            }
        }

        @media (max-width: 480px) {
            .dashboard-item {
                width: 100%;
                max-width: 300px;
                font-size: 14px;
            }

            .title {
                font-size: 18px;
            }

            .delta-header {
                font-size: 18px;
            }

            .delta-form input[type="text"], .delta-form button {
                max-width: 90%;
            }
        }
    </style>
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <div class="dashboard-container">
                <h2 class="title">Painel do Sistema</h2>
                <div class="dashboard">
                    <div class="dashboard-item" style="background-color: #3498db;">
                        <i class="fa fa-users"></i>
                        <span><?=$num_users?> Usuários</span>
                    </div>
                    <div class="dashboard-item" style="background-color: #e74c3c;">
                        <i class="fa fa-cogs"></i>
                        <span>Configurações</span>
                    </div>
                    <div class="dashboard-item" style="background-color: #2ecc71;">
                        <i class="fa fa-database"></i>
                        <span>Banco de Dados</span>
                    </div>
                    <div class="dashboard-item" style="background-color: #f1c40f;">
                        <i class="fa fa-file"></i>
                        <span>Relatórios</span>
                    </div>
                </div>
            </div>
            <div class="chart-container" style="width: 50%; margin: auto;">
                <canvas id="equipmentsChart"></canvas>
            </div>

            <!-- Delta AI Section -->
            <div class="delta-container">
                <div class="delta-header">Inteligência Artificial - Delta</div>
                <form class="delta-form" id="deltaForm">
                    <input type="text" id="userQuestion" placeholder="Digite sua pergunta..." required>
                    <button type="submit">Enviar Pergunta</button>
                </form>
                <div class="delta-response" id="responseContainer" style="display: none;">
                    <p><strong>Resposta:</strong> <span id="aiResponse">Aqui será a resposta da IA.</span></p>
                </div>
            </div>

        </section>
        <div class="footer">Direitos Reservados - T.I - PMSPA 2025</div>
        <div class="datetime">
            <span id="currentDate"></span> - <span id="currentTime"></span>
        </div>
    </div>

<script>
    document.getElementById("currentDate").innerText = new Date().toLocaleDateString('pt-BR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

    function updateClock() {
        document.getElementById("currentTime").innerText = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    setInterval(updateClock, 1000);
    updateClock();

    const ctx = document.getElementById('equipmentsChart').getContext('2d');
    const categories = <?=$categories_json?>;
    const categoryCounts = <?=$category_counts_json?>;

    const equipmentsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: categories,
            datasets: [{
                label: 'Quantidade por Categoria',
                data: categoryCounts,
                backgroundColor: categories.map(() => '#' + Math.floor(Math.random() * 16777215).toString(16)) // Gera cores aleatórias
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

		// Função para enviar perguntas e obter respostas do "Delta"
document.getElementById("deltaForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const userQuestion = document.getElementById("userQuestion").value;

    // Exibe a mensagem enquanto está aguardando a resposta
    document.getElementById("aiResponse").innerText = "Aguarde enquanto processamos sua pergunta...";
    document.getElementById("responseContainer").style.display = "block";

    // Verifica se a pergunta não está vazia
    if (userQuestion.trim() === "") {
        document.getElementById("aiResponse").innerText = "Por favor, digite uma pergunta.";
        return; // Impede o envio se a pergunta estiver vazia
    }

    // Enviar a pergunta para o backend (ask_ai.php)
    const formData = new FormData();
    formData.append('question', userQuestion);

    fetch('ask_ai.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Exibe a resposta da IA
        if (data.response) {
            document.getElementById("aiResponse").innerText = data.response;
        } else {
            document.getElementById("aiResponse").innerText = "Desculpe, não consegui entender sua pergunta. Tente novamente.";
        }
    })
    .catch(error => {
        document.getElementById("aiResponse").innerText = "Ocorreu um erro ao tentar se comunicar com a IA.";
    });
		});

</script>
</body>
</html>
<?php } else { 
   header("Location: login.php?error=Faça login primeiro");
   exit();
} ?>
