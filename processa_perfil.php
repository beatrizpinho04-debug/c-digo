<?php
require_once 'database/connection.php'; 
session_start();

if (!isset($_SESSION['idU'])) {
    header("Location: LogIn.php");
    exit();
}

$db = getDatabaseConnection();
$idU = $_SESSION['idU'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = trim(htmlspecialchars($_POST['name']));
    $surname = trim(htmlspecialchars($_POST['surname']));
    $birthDate = $_POST['birthDate'];
    $sex = $_POST['sex'];
    $phoneN = trim($_POST['phoneN']);
    
    if (empty($name) || empty($surname) || empty($birthDate)) {
        $_SESSION['message'] = "Erro: Nome, Apelido e Data de Nascimento são obrigatórios.";
        $_SESSION['message_type'] = "error";
        header("Location: perfil.php");
        exit();
    }
    if (!empty($phoneN)) {
        // metodo comum: ^ (inicio) \+ (sinal mais) [0-9]{9,15} (9 a 15 digitos) $ (fim)
        if (!preg_match('/^\+[0-9]{9,15}$/', $phoneN)) {
            $_SESSION['message'] = "Erro: O telemóvel deve começar por '+' seguido do indicativo e número (Ex: +351912345678).";
            $_SESSION['message_type'] = "error";
            header("Location: perfil.php");
            exit();
        }
    } else {
        $phoneN = null; 
    }

    $params = [$name, $surname, $birthDate, $sex, $phoneN];
    
    $password_sql = "";
    if (!empty($_POST['password'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_sql = ", password = ?";
        $params[] = $password_hash;
    }

    $image_sql = "";
    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] == 0) {
        $target_dir = "foto/"; 
        $file_extension = pathinfo($_FILES["profilePic"]["name"], PATHINFO_EXTENSION);
        $new_filename = $idU . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        $db_path = "foto/" . $new_filename;

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($file_extension), $allowed)) {
            if (move_uploaded_file($_FILES["profilePic"]["tmp_name"], $target_file)) {
                $image_sql = ", profilePic = ?";
                $params[] = $db_path;
                $_SESSION['profilePic'] = $db_path; 
            }
        }
    }

    $sql = "UPDATE User SET name = ?, surname = ?, birthDate = ?, sex = ?, phoneN = ? $password_sql $image_sql WHERE idU = ?";
    $params[] = $idU;
    
    $stmt = $db->prepare($sql);

    try {
        $db->beginTransaction();
        $stmt->execute($params);

        if ($_SESSION['userType'] === 'Profissional de Saúde' && isset($_POST['department'])) {
            $newDept = htmlspecialchars($_POST['department']);
            $stmtHP = $db->prepare("UPDATE HealthProfessional SET department = ? WHERE idU = ?");
            $stmtHP->execute([$newDept, $idU]);
        }

        $db->commit();

        $_SESSION['name'] = $name;
        $_SESSION['surname'] = $surname;
        $_SESSION['message'] = "Perfil atualizado com sucesso!";
        $_SESSION['message_type'] = "success";

    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['message'] = "Erro ao atualizar dados: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }

    header("Location: perfil.php");
    exit();
}
?>