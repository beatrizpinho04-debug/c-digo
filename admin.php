<?php
session_start();
    // 1. Verifica se existe sessão ativa de um administrador 
    if (!isset($_SESSION['idU'])) {
        // Se não houver sessão, manda para o login com um erro
        $_SESSION['login_error'] = "Acesso negado. Por favor faça login.";
        header("Location: index.php");
        exit();
    }

    // 2. Verifica se o tipo de utilizador é Administrador
    if ($_SESSION['userType'] !== "Administrador") {
        header("Location: index.php");
        exit();
    }
    // 2. Definir Título da Página
    $title = "Administrador";

    // 3. Include do HEAD (CSS e metadados)
    include 'templates/header.php';
?>
<body>
    <div class="page-wrapper">
        <?php include 'templates/nav.php'; ?>
        <h1>Sucesso – Administrador</h1>
        <?php include 'templates/footer.php'; ?>