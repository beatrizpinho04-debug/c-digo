<?php
session_start();

require_once("database/connection.php");

$db = getDatabaseConnection();
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    //Procurar utilizador pelo email
    $stmt = $db->prepare("SELECT * FROM User WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user ||!password_verify($password, $user["password"])) {
        $_SESSION['login_error'] = "Email ou palavra-passe incorretos! Tente de novo.";
        header("Location: index.php");
        exit();
    } else { //Login bem-sucedido
        switch ($user["userType"]) {
            case "Administrador":
                header("Location: admin.php");
                exit();
            case "Físico Médico":
                header("Location: physicist.php");
                exit();
            case "Profissional de Saúde":
                header("Location: HP.php");
                exit();
            default:
                $_SESSION['login_error'] = "Erro interno: tipo de utilizador desconhecido.";
                header("Location: index.php");
                exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão de Dosimetros</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="icon" type="image/png" href="css\icon.svg">
</head>

<body>
    <?php if ($error): ?>
        <script>
            alert("<?php echo $error; ?>");
        </script>
    <?php endif; ?>
    <div class="page-wrapper">
        <div class="flex-center">
            <div class="container">
                <div class="text-center mb1">
                    <!-- Símbolo -->
                    <img src="css/icon.svg" width="64" height="64">
                    <!-- Título e Subtítulo -->
                    <h1 class="titulo mb05">Gestão de Dosímetros</h1>
                    <p class="subtítulo">Sistema de Gestão para Instituições de Saúde</p>
                </div>
                <div class="card">
                    <h2 class="entrar text-center mb1_5">Entrar</h2>
                    <form method="POST" class="login-form">
                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <!-- Password -->
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-full">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <footer class="footer mb2">Gestão de Dosimetros © 2025</footer>
    </div>
</body>

</html>