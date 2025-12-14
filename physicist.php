<?php
session_start();

    // 1. Verifica se existe sessão ativa de um administrador 
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
    // header
    include 'templates/header.php';
?>
<body>
    <div class="page-wrapper">
        <?php include 'templates/nav.php'; ?>
        <h1>Sucesso – Físico Médico</h1>
        <?php include 'templates/footer.php'; ?>