<?php
session_start();

require_once("templates/header.php");
require_once("templates/nav.php");
require_once("templates/footer.php");

//Verifica se existe sessão ativa
if (!isset($_SESSION['idU'])) {
    // Se não houver sessão, manda para o login com um erro
    $_SESSION['login_error'] = "Acesso negado. Por favor faça login.";
    header("Location: index.php");
    exit();
}

//Verifica se o tipo de utilizador é Administrador
if ($_SESSION['userType'] !== "Administrador") {
    header("Location: index.php");
    exit();
}
//Título da Página
$title = "Administrador";
?>
<?php header_set(); ?>

<body>
    <div class="page-wrapper">
        <?php nav_set(); ?>
        <h1>Sucesso – Administrador</h1>
        <?php renderFooter(); ?>
    </div>
</body>

</html>