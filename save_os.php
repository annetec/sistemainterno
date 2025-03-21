<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados enviados
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Verifique se todos os campos obrigatórios estão presentes
    if (
        isset($data['motivoOS']) && isset($data['solicitante']) &&
        isset($data['prioridade']) && isset($data['condicao']) &&
        isset($data['descricao']) && isset($data['categoria']) &&
        isset($data['equipamento']) && isset($data['secretaria']) &&
        isset($data['setor']) && isset($data['responsavel']) &&
        isset($data['numeroSerie'])
    ) {
        // Conectar ao banco de dados
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "task_management_db";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        // Preparar a query SQL
        $sql = "INSERT INTO ordens_servico (motivo_os, solicitante, prioridade, condicao, descricao, data_criacao, categoria, equipamento, secretaria, setor, responsavel, numero_serie, data_abertura)
                VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(['success' => false, 'error' => 'Erro na preparação da consulta']);
            die();
        }

        // Vincular os parâmetros
        $stmt->bind_param(
            "ssssssssssss", 
            $data['motivoOS'], $data['solicitante'], $data['prioridade'], 
            $data['condicao'], $data['descricao'], $data['categoria'], 
            $data['equipamento'], $data['secretaria'], $data['setor'], 
            $data['responsavel'], $data['numeroSerie'], $data['dataAbertura'] 
        );

        // Executar a query
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);  // Exibe o erro, se houver
        }

        // Fechar a conexão
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Campos obrigatórios não preenchidos']);
    }
}
?>
