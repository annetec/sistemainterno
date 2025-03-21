<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "employee") {
    include "DB_connection.php";
    include "app/Model/User.php";
    $user = get_user_by_id($conn, $_SESSION['id']);
    
 ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">Perfil <a href="edit_profile.php">Editar Perfil</a></h4>
            <table class="main-table" style="max-width: 300px;">
                <tr>
                    <td>Nome Completo</td>
                    <td><?=$user['full_name']?></td>
                </tr>
                <tr>
                    <td>Nome de Usuário</td>
                    <td><?=$user['username']?></td>
                </tr>
                <tr>
                    <td>Criado em</td>
                    <td><?=$user['created_at']?></td>
                </tr>
                <tr>
                    <td>Função</td>
                    <td>
                        <?php 
                            // Exibe se o usuário é Admin ou Funcionário
                            if ($user['role'] == 'admin') {
                                echo "<span style='color: green; font-weight: bold;'>Administrador</span>";
                            } else {
                                echo "<span style='color: blue; font-weight: bold;'>Funcionário</span>";
                            }
                        ?>
                    </td>
                </tr>
                <?php if ($user['profile_pic']): ?>
                    <tr>
                        <td>Foto de Perfil</td>
                        <td><img src="img/<?=$user['profile_pic']?>" alt="Foto de Perfil" width="100"></td>
                    </tr>
                <?php endif; ?>
            </table>

			<?php if ($_SESSION['role'] == "admin") { ?>
                <div class="admin-actions">
                    <h4>Adicionar Novo Usuário</h4>
                    <form method="POST" action="add_user.php" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="username">Nome de Usuário:</label>
                            <input type="text" name="username" id="username" required>
                        </div>
                        <div class="form-group">
                            <label for="full_name">Nome Completo:</label>
                            <input type="text" name="full_name" id="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Função:</label>
                            <select name="role" id="role" required>
                                <option value="employee">Funcionário</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Senha:</label>
                            <input type="password" name="password" id="password" required>
                        </div>
                        <div class="form-group">
                            <label for="profile_pic">Foto de Perfil:</label>
                            <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
                        </div>
                        <div class="form-group">
                            <button type="submit">Adicionar Usuário</button>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </section>
    </div>

    <script type="text/javascript">
        var active = document.querySelector("#navList li:nth-child(3)");
        active.classList.add("active");
    </script>
</body>
</html>
<?php } else { 
   $em = "Primeiro login necessário";
   header("Location: login.php?error=$em");
   exit();
}
 ?>
 ?>
