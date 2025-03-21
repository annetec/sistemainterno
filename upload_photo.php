<?php
session_start();
include "DB_connection.php";

// Verifica se o formulário de upload foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_pic'])) {
    $file = $_FILES['profile_pic'];
    $user_id = $_SESSION['id'];  // ID do usuário logado

    // Defina o diretório de upload como "img"
    $upload_dir = __DIR__ . "/img/";  // Caminho absoluto para a pasta "img"

    // Verifica se o diretório de upload existe
    if (!is_dir($upload_dir)) {
        echo "Diretório de upload não encontrado.";
        exit();
    }

    // Verifica se o arquivo foi enviado corretamente
    if ($file['error'] == 0) {
        $file_name = time() . "_" . basename($file['name']);
        $upload_path = $upload_dir . $file_name;

        // Move o arquivo para o diretório de uploads
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Atualiza o caminho da foto no banco de dados utilizando PDO
            $stmt = $conn->prepare("UPDATE Users SET profile_pic = :profile_pic WHERE id = :id");
            $stmt->bindValue(":profile_pic", $file_name, PDO::PARAM_STR);
            $stmt->bindValue(":id", $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                header("Location: user.php?success=Foto atualizada com sucesso!");
            } else {
                echo "Erro ao atualizar a foto.";
            }
        } else {
            echo "Erro ao fazer o upload da foto.";
        }
    } else {
        echo "Erro no envio do arquivo.";
    }
}
?>
