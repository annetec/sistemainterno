<?php
session_start();
include "DB_connection.php";

if (!isset($_SESSION['role']) || !isset($_SESSION['id'])) {
    header("Location: login.php?error=First login");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: consulta.php?error=ID inválido");
    exit();
}

$id = $_GET['id'];

try {
    $sql = "DELETE FROM chamados WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        header("Location: consulta.php?success=Registro excluído com sucesso");
    } else {
        header("Location: consulta.php?error=Registro não encontrado ou já excluído");
    }
} catch (Exception $e) {
    header("Location: consulta.php?error=Erro ao excluir registro");
    exit();
}
?>
