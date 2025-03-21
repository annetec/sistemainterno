<?php
session_start();
include "DB_connection.php";

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera os dados do formulário
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Criptografa a senha

    // Processa o upload da foto de perfil, se houver
    $profile_pic = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $file = $_FILES['profile_pic'];
        $upload_dir = __DIR__ . "/img/";
        $file_name = time() . "_" . basename($file['name']);
        $upload_path = $upload_dir . $file_name;

        // Verifica se o diretório de upload existe
        if (!is_dir($upload_dir)) {
            echo "Diretório de upload não encontrado.";
            exit();
        }

        // Move o arquivo para o diretório de uploads
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $profile_pic = $file_name;
        } else {
            echo "Erro ao fazer o upload da foto.";
            exit();
        }
    }

    // Prepara a query para inserir o novo usuário no banco de dados
    $stmt = $conn->prepare("INSERT INTO Users (full_name, username, password, role, profile_pic, created_at) 
                            VALUES (:full_name, :username, :password, :role, :profile_pic, NOW())");
    $stmt->bindValue(":username", $username, PDO::PARAM_STR);
    $stmt->bindValue(":full_name", $full_name, PDO::PARAM_STR);
    $stmt->bindValue(":role", $role, PDO::PARAM_STR);
    $stmt->bindValue(":password", $password, PDO::PARAM_STR);
    $stmt->bindValue(":profile_pic", $profile_pic, PDO::PARAM_STR);  // Adiciona a foto no banco

    // Executa a query e verifica se foi bem-sucedida
    if ($stmt->execute()) {
        header("Location: user.php?success=Usuário adicionado com sucesso!");
        exit();
    } else {
        echo "Erro ao adicionar usuário.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Usuário</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <h4 class="title">Adicionar Novo Usuário</h4>
            <form method="POST" action="add_user.php" enctype="multipart/form-data">
                <div>
                    <label for="username">Nome de Usuário:</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div>
                    <label for="full_name">Nome Completo:</label>
                    <input type="text" name="full_name" id="full_name" required>
                </div>
                <div>
                    <label for="role">Função:</label>
                    <select name="role" id="role" required>
                        <option value="employee">Funcionário</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div>
                    <label for="password">Senha:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div>
                    <label for="profile_pic">Foto de Perfil:</label>
                    <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
                </div>
                <button type="submit">Adicionar Usuário</button>
            </form>
        </section>
    </div>
    <script type="text/javascript">
        var active = document.querySelector("#navList li:nth-child(3)");
        active.classList.add("active");
    </script>
</body>
</html>
