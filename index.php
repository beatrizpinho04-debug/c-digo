<?php
session_start();
require_once("templates/header.php");
require_once("templates/footer.php");

//Se o utilizador já estiver logado, redireciona-o automaticamente para a página certa, sem mostrar esta página de login.
if (isset($_SESSION['idU'])) {
    switch ($_SESSION['userType']) {
        case "Administrador":
            header("Location: admin.php");
            exit();
        case "Físico Médico":
            header("Location: physicist.php");
            exit();
        case "Profissional de Saúde":
            header("Location: HP.php");
            exit();
    }
}

//Mensagens de erro (vindas do LogIn.php)
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Para não aparecer sempre
}

//Nome da página
$title = "Sistema de Gestão de Dosimetros";
?>
<?php header_set(); ?>

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
                    <form action="LogIn.php" method="POST" class="form-group">
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
                        <!-- Submit Botão -->
                        <button type="submit" class="btn btn-primary btn-full">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <?php renderFooter(); ?>
    </div>
</body>

</html>