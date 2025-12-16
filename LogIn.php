<?php
session_start();
require_once("database/connection.php");
require_once("database/users.php");

$db = getDatabaseConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $user = userLogin($db, $email, $password);

    if ($user) {
        if ($user['userStatus'] == 0) {
            $_SESSION['login_error'] = "A sua conta encontra-se desativada. Contacte o administrador.";
            header("Location: index.php");
            exit();
        }

        // Sucesso
        $_SESSION['idU'] = $user['idU'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['surname'] = $user['surname'];
        $_SESSION['userType'] = $user['userType'];
        $_SESSION['profilePic'] = $user['profilePic'];

        if ($user['userType'] === 'Profissional de Saúde') {
            $stmtHP = $db->prepare("SELECT profession FROM HealthProfessional WHERE idU = ?");
            $stmtHP->execute([$user['idU']]);
            $hpData = $stmtHP->fetch();

            if ($hpData && !empty($hpData['profession'])) {
                $_SESSION['roleLabel'] = $hpData['profession'];
            } else {
                $_SESSION['roleLabel'] = 'Profissional de Saúde';
            }
        } else {
            $_SESSION['roleLabel'] = $user['userType'];
        }

        // Redirecionamento
        switch ($user["userType"]) {
            case "Administrador": header("Location: admin.php"); break;
            case "Físico Médico": header("Location: physicist.php"); break;
            case "Profissional de Saúde": header("Location: HP.php"); break;
            default:
                $_SESSION['login_error'] = "Erro: Tipo de utilizador desconhecido.";
                header("Location: index.php");
        }
        exit();

    } else {
        $_SESSION['login_error'] = "Email ou palavra-passe incorretos!";
        header("Location: index.php");
        exit();
    }
}
?>