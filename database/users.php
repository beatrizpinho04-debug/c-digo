<?php

function userLogin($db, $email, $password) {

    //Procura apenas pelo email.
    $stmt = $db->prepare('SELECT idU, userType, name, surname, password, profilePic FROM User WHERE email = ?');
    $stmt->execute(array($email));
    $user = $stmt->fetch();

    //Se o user existir, verifica se a password bate certa com o hash
    if ($user && password_verify($password, $user['password'])) {
        return $user; // Sucesso
    } else {
        return false; // Falha
    }
}
?>