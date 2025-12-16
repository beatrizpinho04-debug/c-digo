<?php
session_start();
require_once("templates/header.php");
require_once("templates/nav.php");
require_once("templates/footer.php");
// 1. Verifica se existe sessão ativa de um fi
if (!isset($_SESSION['idU'])) {
    // Se não houver sessão, manda para o login com um erro
    $_SESSION['login_error'] = "Acesso negado. Por favor faça login.";
    header("Location: index.php");
    exit();
}
// 2. Verifica se o tipo de utilizador é Físico Médico
if ($_SESSION['userType'] !== "Físico Médico") {
    header("Location: index.php");
    exit();
}
//Definir Título da Página
$title = "Físico Médico";
?>
<?php header_set(); ?>

<body>
    <div class="page-wrapper">
        <?php nav_set(); ?>
        <h1>Sucesso – Físico Médico</h1>
        <?php renderFooter(); ?>
    </div>
</body>

</html>