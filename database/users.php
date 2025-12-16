<?php
function userLogin($db, $email, $password) {
    $stmt = $db->prepare('SELECT idU, userType, name, surname, password, profilePic, userStatus FROM User WHERE email = ?');
    $stmt->execute(array($email));
    $user = $stmt->fetch();

    // Se o user existir e a password bater certa
    if ($user && password_verify($password, $user['password'])) {
        return $user; 
    } else {
        return false; 
    }
}
?>