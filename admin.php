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
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="icon" type="image/png" href="css\icon.svg">
</head>

<body>
    <h1>Sucesso – Administrador</h1>
</body>

</html>