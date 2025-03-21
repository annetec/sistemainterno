<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "employee") {
    include "DB_connection.php";
    include "app/Model/User.php";
    $user = get_user_by_id($conn, $_SESSION['id']);
    
 ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<title>Perfil</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">

	<style>
		/* Melhorias no Layout */
		.body {
			padding: 20px;
		}
		.section-1 {
			padding: 20px;
			border-radius: 10px;
			box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
			background-color: #fff;
			margin-top: 20px;
		}
		.title {
			font-size: 24px;
			font-weight: bold;
			margin-bottom: 20px;
		}
		.main-table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 20px;
		}
		.main-table td {
			padding: 10px;
			border: 1px solid #ddd;
		}
		.main-table tr:nth-child(even) {
			background-color: #f9f9f9;
		}
		.main-table td:first-child {
			font-weight: bold;
		}
		.form-group {
			margin-bottom: 15px;
		}
		.form-group label {
			display: block;
			font-weight: bold;
		}
		.form-group input, .form-group select {
			width: 100%;
			padding: 8px;
			border-radius: 5px;
			border: 1px solid #ccc;
		}
		.button {
			background-color: #4CAF50;
			color: white;
			padding: 10px 20px;
			border: none;
			border-radius: 5px;
			cursor: pointer;
		}
		.button:hover {
			background-color: #45a049;
		}
		.upload-container {
			text-align: center;
			margin-top: 20px;
		}
		.upload-container img {
			width: 150px;
			height: 150px;
			border-radius: 50%;
			object-fit: cover;
			border: 3px solid #ddd;
		}
		.upload-container input {
			margin-top: 10px;
		}
	</style>
</head>
<body>
	<input type="checkbox" id="checkbox">
	<?php include "inc/header.php" ?>
	<div class="body">
		<?php include "inc/nav.php" ?>
		<section class="section-1">
			<h4 class="title">Perfil <a href="edit_profile.php">Editar Perfil</a></h4>
         <table class="main-table" style="max-width: 600px;">
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
			</table>

			<!-- Troca de foto de perfil -->
			<div class="upload-container">
				<h4>Trocar Foto de Perfil</h4>
				<img src="uploads/<?=$user['profile_pic']?>" alt="Foto de Perfil">
				<form action="upload_photo.php" method="POST" enctype="multipart/form-data">
					<input type="file" name="profile_pic" accept="image/*" required>
					<button type="submit" class="button">Trocar Foto</button>
				</form>
			</div>

			<?php if ($_SESSION['role'] == "admin") { ?>
				<h4>Adicionar Novo Usuário</h4>
				<form method="POST" action="user.php">
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
					<button type="submit" class="button">Adicionar Usuário</button>
				</form>
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
