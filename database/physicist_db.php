<?php

// 1. Busca pedidos pendentes de Profissionais de Saúde 
function getPendingRequests($db) {
    $stmt = $db->prepare("SELECT DR.*, U.name, U.surname, HP.department 
                          FROM DosimeterRequest DR 
                          JOIN User U ON DR.idU = U.idU 
                          JOIN HealthProfessional HP ON U.idU = HP.idU
                          WHERE DR.decisionMade = 0 AND U.userType = 'Profissional de Saúde'");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 2. Busca dados completos de um utilizador
function getUserFullDetails($db, $idU) {
    $stmt = $db->prepare("SELECT U.*, HP.department, HP.profession 
                          FROM User U 
                          LEFT JOIN HealthProfessional HP ON U.idU = HP.idU 
                          WHERE U.idU = ?");
    $stmt->execute([$idU]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// 3. Busca o histórico de todos os pedidos de um profissional
function getUserRequestHistory($db, $idU) {
    $stmt = $db->prepare("SELECT DR.*, AR.idR as approvedID 
                          FROM DosimeterRequest DR 
                          LEFT JOIN ApprovedRequest AR ON DR.idR = AR.idR 
                          WHERE DR.idU = ? 
                          ORDER BY DR.requestDate DESC");
    $stmt->execute([$idU]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// 5. Busca o estado atual do pedido do próprio Físico
function getMyCurrentRequest($db, $idU) {
    $stmt = $db->prepare("SELECT AR.idR, AR.status, DR.pratica 
                          FROM ApprovedRequest AR
                          JOIN DosimeterRequest DR ON AR.idR = DR.idR
                          WHERE DR.idU = ? 
                          ORDER BY AR.approvalDate DESC LIMIT 1");
    $stmt->execute([$idU]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// 6. Busca os dados do dosímetro ativo (Físico ou Profissional)
function getPhysicistActiveDosimeters($db, $idU) {
    $stmt = $db->prepare("SELECT DA.dosimeterSerial, DA.assignmentDate, DA.nextReplacementDate 
                          FROM DosimeterAssignment DA
                          JOIN ApprovedRequest AR ON DA.idA = AR.idR 
                          JOIN DosimeterRequest DR ON AR.idR = DR.idR
                          WHERE DR.idU = ? AND DA.status = 'Em_Uso'");
    $stmt->execute([$idU]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// 7. Busca o histórico de Dosímetros (Usado na aba Histórico)
function getPhysicistDosimeterHistory($db, $idU) {
    $stmt = $db->prepare("SELECT 
                            DA.dosimeterSerial as serial, 
                            DA.assignmentDate as dateIn, 
                            DA.nextReplacementDate as dateOut 
                          FROM DosimeterAssignment DA
                          JOIN ApprovedRequest AR ON DA.idA = AR.idR 
                          JOIN DosimeterRequest DR ON AR.idR = DR.idR
                          WHERE DR.idU = ?
                          ORDER BY DA.assignmentDate DESC");
    $stmt->execute([$idU]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 8. Busca o histórico de Alterações/Suspensões 
function getPhysicistRequestHistory($db, $idU) {
    $stmt = $db->prepare("SELECT 
                            requestType as type, 
                            requestDate, 
                            finalStatus 
                          FROM ChangeRecord 
                          WHERE idUser = ? 
                          ORDER BY requestDate DESC");
    $stmt->execute([$idU]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// 4. Busca os profissionais de saúde ativos (COM PESQUISA)
// 4. Busca os profissionais de saúde ativos (VERSÃO CORRIGIDA COM PESQUISA)
function getActiveProfessionals($db, $search = '') {
    $sql = "SELECT idU, name, surname, email FROM User 
            WHERE userType = 'Profissional de Saúde' AND userStatus = 1";
    
    $params = [];
    if (!empty($search)) {
        // O operador || serve para juntar nome e apelido no SQLite/PostgreSQL
        $sql .= " AND (name LIKE ? OR surname LIKE ? OR (name || ' ' || surname) LIKE ? OR email LIKE ?)";
        $term = "%$search%";
        $params = [$term, $term, $term, $term];
    }

    $sql .= " ORDER BY name ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// NOVA FUNÇÃO: Histórico Global para o Físico (Igual ao Admin mas sem suspensões)
function getGlobalHistoryForPhysicist($db, $search = '') {
    // Reutilizamos a lógica da Beatriz para ser consensual
    $sql = "SELECT DAH.dosimeterSerial, DAH.assignmentDate, DAH.removalDate, 
                   U.name, U.surname, U.email, 'Histórico' as estado
            FROM DosimeterAssignmentHistory DAH
            JOIN ApprovedRequest AR ON DAH.idA = AR.idA
            JOIN DosimeterRequest DR ON AR.idR = DR.idR
            JOIN User U ON DR.idU = U.idU
            WHERE 1=1";

    $params = [];
    if (!empty($search)) {
        $sql .= " AND (U.name LIKE ? OR U.surname LIKE ? OR (U.name || ' ' || U.surname) LIKE ? OR U.email LIKE ? OR DAH.dosimeterSerial LIKE ?)";
        $term = "%$search%";
        $params = array_merge($params, [$term, $term, $term, $term, $term]);
    }

    $sql .= " UNION ALL ";

    $sql .= "SELECT DA.dosimeterSerial, DA.assignmentDate, NULL as removalDate, 
                    U.name, U.surname, U.email, 'Ativo' as estado
             FROM DosimeterAssignment DA
             JOIN ApprovedRequest AR ON DA.idA = AR.idA
             JOIN DosimeterRequest DR ON AR.idR = DR.idR
             JOIN User U ON DR.idU = U.idU
             WHERE DA.status = 'Em_Uso'";

    if (!empty($search)) {
        $sql .= " AND (U.name LIKE ? OR U.surname LIKE ? OR (U.name || ' ' || U.surname) LIKE ? OR U.email LIKE ? OR DA.dosimeterSerial LIKE ?)";
        $params = array_merge($params, [$term, $term, $term, $term, $term]);
    }

    $sql .= " ORDER BY assignmentDate DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>