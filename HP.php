<?php
session_start();

    //Verifica se existe sessão ativa de um administrador 
    if (!isset($_SESSION['idU'])) {
        // Se não houver sessão, manda para o login com um erro
        $_SESSION['login_error'] = "Acesso negado. Por favor faça login.";
        header("Location: index.php");
        exit();
    }
    //Verifica se o tipo de utilizador é Profissional de Saúde
    if ($_SESSION['userType'] !== "Profissional de Saúde") {
        header("Location: index.php");
        exit();
    }
    //Definir Título da Página
    $title = "Profissional de Saúde";
    // header
    include 'templates/header.php';
?>
<body>
    <div class="page-wrapper">
        <?php include 'templates/nav.php'; ?>
        <h1>Sucesso – Profissional de Saúde</h1>
        <?php include 'templates/footer.php'; ?>
