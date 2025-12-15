<?php
// processa_perfil.php (Na raiz 'código/')

require_once 'database/connection.php'; 
session_start();

if (!isset($_SESSION['idU'])) {
    header("Location: LogIn.php");
    exit();
}

$db = getDatabaseConnection();
$idU = $_SESSION['idU'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $surname = htmlspecialchars($_POST['surname']);
    $birthDate = htmlspecialchars($_POST['birthDate']);
    $sex = htmlspecialchars($_POST['sex']);
    $phoneN = htmlspecialchars($_POST['phoneN']);
    
    $password_sql = "";
    $params = [$name, $surname, $birthDate, $sex, $phoneN];
    
    // Password
    if (!empty($_POST['password'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_sql = ", password = ?";
        $params[] = $password_hash;
    }

    // Imagem
    $image_sql = "";
    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] == 0) {
        // Guarda na pasta 'foto' que está na raiz
        $target_dir = "foto/"; 
        
        $file_extension = pathinfo($_FILES["profilePic"]["name"], PATHINFO_EXTENSION);
        $new_filename = $idU . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Caminho na BD
        $db_path = "foto/" . $new_filename;

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($file_extension), $allowed)) {
            if (move_uploaded_file($_FILES["profilePic"]["tmp_name"], $target_file)) {
                $image_sql = ", profilePic = ?";
                $params[] = $db_path;
                $_SESSION['profilePic'] = $db_path; // Atualiza sessão
            }
        }
    }

    $params[] = $idU;

    // Atualiza tabela User
    $sql = "UPDATE User SET name = ?, surname = ?, birthDate = ?, sex = ?, phoneN = ? $password_sql $image_sql WHERE idU = ?";
    
    $stmt = $db->prepare($sql);
    
    if ($stmt->execute($params)) {
        $_SESSION['name'] = $name;
        $_SESSION['surname'] = $surname;
        $_SESSION['message'] = "Perfil atualizado com sucesso!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Erro ao atualizar perfil.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: perfil.php");
    exit();
}
?>