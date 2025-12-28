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
    // ADICIONEI "u.email" NA LINHA ABAIXO
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


// Obter histórico de dosímetros
function getDosimeterHistory($db, $idUser) {
    $sql = "SELECT 
                dr.requestDate,
                dr.pratica,
                dr.decisionMade,
                ar.approvalDate,
                rr.comment AS motRej,
                -- Dados do Físico
                u.name AS fisico_name,
                u.email AS fisico_email,
                -- Estado calculado
                CASE 
                    WHEN dr.decisionMade = 0 THEN 'Pendente'
                    WHEN ar.idA IS NOT NULL THEN 'Aprovado' 
                    ELSE 'Rejeitado' 
                END as estado_final,
                -- Estado Ativo/Suspenso (para pedidos aprovados)
                ar.status AS status_ativo
            FROM DosimeterRequest dr
            LEFT JOIN ApprovedRequest ar ON dr.idR = ar.idR
            LEFT JOIN RejectedRequest rr ON dr.idR = rr.idR
            LEFT JOIN User u ON (ar.idP = u.idU OR rr.idP = u.idU)
            WHERE dr.idU = :idU
            ORDER BY dr.requestDate DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute(['idU' => $idUser]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obter histórico de alterações
function getChangeHistory($db, $idUser) {
    $sql = "SELECT 
                cr.requestDate,
                cr.requestType,
                cr.message,
                cr.adminNote,
                cr.status
            FROM ChangeRecord cr
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
    
    // fetchColumn() devolve o número exato (0 ou mais)
    return $stmt->fetchColumn() > 0;
}
?>