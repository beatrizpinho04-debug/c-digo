<?php
require_once 'connection.php';

// Obter perfil do profissional
function getHPProfile($db, $idUser) {
    $stmt = $db->prepare("SELECT profession, department FROM HealthProfessional WHERE idU = ?");
    $stmt->execute([$idUser]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obter último pedido (para a Dashboard)
function getLastRequest($db, $idUser) {
    $sql = "SELECT dr.pratica, dr.decisionMade, ar.status as stAp, ar.approvalDate, 
            u.name, u.surname, rr.comment as motRej, 
            da.dosimeterSerial, da.assignmentDate, da.nextReplacementDate, da.status as stDos,
            ar.riskCategory, ar.dosimeterType, ar.periodicity
            FROM DosimeterRequest dr
            LEFT JOIN ApprovedRequest ar ON dr.idR = ar.idR
            LEFT JOIN RejectedRequest rr ON dr.idR = rr.idR
            LEFT JOIN User u ON (ar.idP = u.idU OR rr.idP = u.idU)
            LEFT JOIN DosimeterAssignment da ON ar.idA = da.idA
            WHERE dr.idU = :id ORDER BY dr.requestDate DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $idUser]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obter todos os pedidos
function getAllRequests($db, $idUser) {
    $sql = "SELECT dr.requestDate, dr.pratica, dr.decisionMade, ar.status as stAp, ar.approvalDate, 
            ar.riskCategory, ar.dosimeterType, rr.comment as motRej, u.name, u.surname
            FROM DosimeterRequest dr
            LEFT JOIN ApprovedRequest ar ON dr.idR = ar.idR
            LEFT JOIN RejectedRequest rr ON dr.idR = rr.idR
            LEFT JOIN User u ON (ar.idP = u.idU OR rr.idP = u.idU)
            WHERE dr.idU = :id ORDER BY dr.requestDate DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $idUser]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obter histórico de dosímetros
function getDosimeterHistory($db, $idUser) {
    $sql = "SELECT da.dosimeterSerial, da.assignmentDate as dI, da.nextReplacementDate as dF_prev, NULL as dF_real, da.status as st, 1 as ord
            FROM DosimeterAssignment da 
            JOIN ApprovedRequest ar ON da.idA = ar.idA 
            JOIN DosimeterRequest dr ON ar.idR = dr.idR  
            WHERE dr.idU = :id                           
            
            UNION ALL
            
            SELECT dah.dosimeterSerial, dah.assignmentDate as dI, NULL as dF_prev, dah.removalDate as dF_real, 'Devolvido' as st, 2 as ord
            FROM DosimeterAssignmentHistory dah 
            JOIN ApprovedRequest ar ON dah.idA = ar.idA 
            JOIN DosimeterRequest dr ON ar.idR = dr.idR  
            WHERE dr.idU = :id                           
            
            ORDER BY ord ASC, dI DESC";
            
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $idUser]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obter histórico de alterações
function getChangeHistory($db, $idUser) {
    $sql = "SELECT cr.*, u.name, u.surname FROM ChangeRecord cr 
            LEFT JOIN User u ON cr.idAdmin = u.idU 
            WHERE cr.idUser = :id ORDER BY cr.requestDate DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $idUser]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>