<?php
session_start();
require_once("database/connection.php");
require_once("database/users.php");

$db = getDatabaseConnection(); // Ligar à BD

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Chamar a função de verificação (que está no users.php)
    $user = userLogin($db, $email, $password);

    if ($user) {
        // Sucesso no login
        $_SESSION['idU'] = $user['idU'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['userType'] = $user['userType'];

        // Redirecionamento consoante o tipo de utilizador
        switch ($user["userType"]) {
            case "Administrador": 
                header("Location: admin.php"); 
                break;
            case "Físico Médico": 
                header("Location: physicist.php"); 
                break;
            case "Profissional de Saúde": 
                header("Location: HP.php"); 
                break;
            default:
                $_SESSION['login_error'] = "Erro: Tipo de utilizador desconhecido.";
                header("Location: index.php");
        }
        exit();

    } else {
        // Falha no login
        $_SESSION['login_error'] = "Email ou palavra-passe incorretos!";
        header("Location: index.php");
        exit();
    }
}
?>