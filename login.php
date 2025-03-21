<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login | Sistema Interno</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="css/style.css">
	<!-- Ícone da página (favicon) -->
	<link rel="icon" type="image/png" href="assets/favicon-pmspa.png">
</head>
<body class="login-body">
	<form method="POST" action="app/login.php" class="shadow p-4 text-center">
		<!-- ======== LOGO DA PREFEITURA ========= -->
		<img src="img/favicon-pmspa.png" alt="Logo PMSPA" width="100">
		<!-- ======== TÍTULO ========= -->
		<h4 class="display-4" style="font-size: 1.5rem;">Sistema Interno - PMSPA</h4>
		
		<?php if (isset($_GET['error'])) { ?>
			<div class="alert alert-danger" role="alert">
				<?php echo stripcslashes($_GET['error']); ?>
			</div>
		<?php } ?>
		<?php if (isset($_GET['success'])) { ?>
			<div class="alert alert-success" role="alert">
				<?php echo stripcslashes($_GET['success']); ?>
			</div>
		<?php } ?>

		<!-- ======== DADOS LOGIN ========= -->
		<div class="mb-3">
			<label for="exampleInputEmail1" class="form-label">Usuário</label>
			<input type="text" class="form-control" name="user_name" required>
		</div>
		<div class="mb-3">
			<label for="exampleInputPassword1" class="form-label">Senha</label>
			<input type="password" class="form-control" name="password" id="exampleInputPassword1" required>
		</div>
		<button type="submit" class="btn btn-primary">Entrar</button>
	</form>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
