<?php
require_once 'connection.php';


function getHPProfile($db, $idUser) {
    $stmt = $db->prepare("SELECT profession, department FROM HealthProfessional WHERE idU = ?");
    $stmt->execute([$idUser]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function getLastRequest($db, $idUser) {
    $sql = "SELECT dr.pratica, dr.decisionMade, ar.status as stAp, ar.approvalDate, 
            u.name, u.surname, u.email, rr.comment as motRej, 
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

// Obter todos os pedidos (COM DETALHES COMPLETOS)
function getAllRequests($db, $idUser) {
    $sql = "SELECT 
                dr.requestDate, 
                dr.pratica, 
                dr.decisionMade, 
                ar.status as stAp, 
                ar.approvalDate,
                ar.riskCategory,
                ar.dosimeterType,
                ar.periodicity,
                u.name, 
                u.surname,
                u.email,
                rr.comment as motRej
            FROM DosimeterRequest dr
            LEFT JOIN ApprovedRequest ar ON dr.idR = ar.idR
            LEFT JOIN RejectedRequest rr ON dr.idR = rr.idR
            LEFT JOIN User u ON (ar.idP = u.idU OR rr.idP = u.idU)
            WHERE dr.idU = :id 
            ORDER BY dr.requestDate DESC";
            
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $idUser]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obter histórico de DOSÍMETROS FÍSICOS (Ativos + Histórico)
function getDosimeterHistory($db, $idUser) {
    $sql = "SELECT 
                da.dosimeterSerial, 
                da.assignmentDate, 
                da.nextReplacementDate as finalDate, 
                'Em Uso' as estado,
                1 as ordem
            FROM DosimeterAssignment da
            JOIN ApprovedRequest ar ON da.idA = ar.idA
            JOIN DosimeterRequest dr ON ar.idR = dr.idR
            WHERE dr.idU = :id

            UNION ALL

            SELECT 
                dah.dosimeterSerial, 
                dah.assignmentDate, 
                dah.removalDate as finalDate, 
                'Recolhido' as estado,
                2 as ordem
            FROM DosimeterAssignmentHistory dah
            JOIN ApprovedRequest ar ON dah.idA = ar.idA
            JOIN DosimeterRequest dr ON ar.idR = dr.idR
            WHERE dr.idU = :id

            ORDER BY ordem ASC, assignmentDate DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $idUser]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obter histórico de alterações
function getChangeHistory($db, $idUser) {
    $sql = "SELECT 
                cr.requestDate,
                cr.requestType,
                cr.message,
                cr.adminNote,
                cr.status,
                cr.decisionDate,
                u.name AS admin_name,
                u.surname AS admin_surname
            FROM ChangeRecord cr
            LEFT JOIN User u ON cr.idAdmin = u.idU
            WHERE cr.idUser = :idU
            ORDER BY cr.requestDate DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute(['idU' => $idUser]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function checkPendingChange($db, $idUser) {
    $sql = "SELECT COUNT(*) 
            FROM ChangeRecord 
            WHERE idUser = ? AND LOWER(TRIM(status)) = 'pendente'";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$idUser]);

    return $stmt->fetchColumn() > 0;
}
?>