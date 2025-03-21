<?php
// Configuração do diretório de backup
define('BACKUP_DIR', __DIR__ . '/backups/');

// Configuração do banco de dados
$host = 'localhost'; // Alterar para o seu host do banco, geralmente 'localhost'
$dbname = 'task_management_db'; // Substitua pelo nome correto do seu banco de dados
$username = 'root'; // Substitua pelo seu usuário do banco de dados
$password = ''; // Substitua pela sua senha do banco de dados, se houver

// Conectar ao banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro na conexão com o banco de dados: ' . $e->getMessage());
}

// Criar diretório de backup se não existir
if (!is_dir(BACKUP_DIR)) {
    mkdir(BACKUP_DIR, 0777, true);
}

// Verificar autenticação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    
    // Consultar o banco de dados para verificar as credenciais
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->bindParam(':username', $user, PDO::PARAM_STR);
    $stmt->execute();
    
    // Verificar se o usuário foi encontrado
    $userRecord = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userRecord && password_verify($pass, $userRecord['password'])) {
        // Nome do arquivo de backup
        $date = date('Y-m-d_H-i-s');
        $backupFile = BACKUP_DIR . "backup_{$date}.zip";

        // Criar um novo arquivo ZIP
        $zip = new ZipArchive();
        if ($zip->open($backupFile, ZipArchive::CREATE) !== TRUE) {
            die('Erro ao criar arquivo ZIP');
        }

        // Função para adicionar arquivos ao ZIP
        function addFilesToZip($folder, $zip, $rootFolder) {
            $files = scandir($folder);
            foreach ($files as $file) {
                if ($file == '.' || $file == '..' || $file == 'backups') continue;
                
                $filePath = "$folder/$file";
                $relativePath = substr($filePath, strlen($rootFolder) + 1);
                
                if (is_dir($filePath)) {
                    addFilesToZip($filePath, $zip, $rootFolder);
                } else {
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }

        // Adicionar todos os arquivos do diretório raiz ao backup
        addFilesToZip(__DIR__, $zip, __DIR__);

        // Fechar o arquivo ZIP
        $zip->close();

        // Exibir popup de sucesso
        echo '<script type="text/javascript">
                window.onload = function() {
                    document.getElementById("success-popup").style.display = "block";
                    setTimeout(function() {
                        window.location.href = "index.php";
                    }, 1000); // 1 segundo de delay antes de redirecionar
                }
              </script>';
        exit;
    } else {
        die('Autenticação falhou!');
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Backup do Site</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            border-radius: 10px;
            display: none;
            width: 300px;
            text-align: center;
        }
        .popup input {
            display: block;
            width: calc(100% - 20px);
            margin: 10px auto;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .popup button {
            background: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        .popup button:hover {
            background: #0056b3;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
        }
        .backup-button {
            background: #28a745;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }
        .backup-button:hover {
            background: #218838;
        }

        /* Popup de sucesso */
        .success-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #28a745;
            color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.3);
        }

        .success-popup button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .success-popup button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function solicitarBackup() {
            document.getElementById("overlay").style.display = "block";
            document.getElementById("popup").style.display = "block";
        }

        function realizarBackup() {
            let username = document.getElementById("username").value;
            let password = document.getElementById("password").value;
            
            if (username && password) {
                let form = document.createElement("form");
                form.method = "POST";
                form.style.display = "none";
                
                let userInput = document.createElement("input");
                userInput.name = "username";
                userInput.value = username;
                form.appendChild(userInput);
                
                let passInput = document.createElement("input");
                passInput.name = "password";
                passInput.value = password;
                form.appendChild(passInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        function fecharPopup() {
            document.getElementById("overlay").style.display = "none";
            document.getElementById("popup").style.display = "none";
        }
    </script>
</head>
<body>
    <h2>Backup do Site</h2>
    <button class="backup-button" onclick="solicitarBackup()">Iniciar Backup</button>
    
    <div class="overlay" id="overlay"></div>
    <div class="popup" id="popup">
        <h3>Autenticação</h3>
        <input type="text" id="username" placeholder="Usuário">
        <input type="password" id="password" placeholder="Senha">
        <button onclick="realizarBackup()">Confirmar</button>
        <button onclick="fecharPopup()">Cancelar</button>
    </div>

    <!-- Popup de Sucesso -->
    <div class="success-popup" id="success-popup">
        <h3>Backup realizado com sucesso!</h3>
        <button onclick="window.location.href='index.php'">Voltar ao Início</button>
    </div>
</body>
</html>
