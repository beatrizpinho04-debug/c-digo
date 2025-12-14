<?php
session_start();
require_once('templates/cabecalho.php');

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

// 3. Definir valores para o cabeçalho
$surname = isset($_SESSION['surname']) ? $_SESSION['surname'] : '';
$pic = isset($_SESSION['profilePic']) ? $_SESSION['profilePic'] : '';

// 4. Chamar o Cabeçalho
output_header("Administrador", $_SESSION['userType'], $_SESSION['name'], $surname, $pic);
?>
    <h1>Sucesso – Administrador</h1>