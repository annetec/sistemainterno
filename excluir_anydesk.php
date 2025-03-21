<?php
include "DB_connection.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Preparar a query para atualizar o campo anydesk_id para NULL
    $sql = "UPDATE chamados SET anydesk_id = NULL WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Após a atualização, redireciona para a página de consulta
    header("Location: cadastro_anydesk.php");
    exit;
}
?>
