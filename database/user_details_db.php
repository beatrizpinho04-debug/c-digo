<?php
require_once 'connection.php';

function getUserFullDetails($db, $idU) {
    $stmt = $db->prepare("SELECT * FROM User WHERE idU = ?");
    $stmt->execute([$idU]);
    $user = $stmt->fetch();
    if (!$user) return null;

    $extra = [];
    if ($user['userType'] === 'Profissional de Saúde') {
        $s = $db->prepare("SELECT profession, department FROM HealthProfessional WHERE idU = ?");
        $s->execute([$idU]);
        $extra = $s->fetch();
    } elseif ($user['userType'] === 'Físico Médico') {
        $extra = ['profession' => 'Físico Médico', 'department' => 'Física Médica'];
    }
    return array_merge($user, $extra ? $extra : []);
}

function getUserRequests($db, $idU) {
    $sql = "SELECT 
                DR.idR, DR.requestDate, DR.pratica, DR.decisionMade,
                AR.approvalDate, AR.dosimeterType, AR.periodicity, AR.riskCategory, AR.status as ar_status,
                U_AP.name as ap_name, U_AP.surname as ap_surname, U_AP.email as ap_email,
                RR.rejectionDate, RR.comment as rejectionComment,
                U_REJ.name as rej_name, U_REJ.surname as rej_surname, U_REJ.email as rej_email
            FROM DosimeterRequest DR
            LEFT JOIN ApprovedRequest AR ON DR.idR = AR.idR
            LEFT JOIN RejectedRequest RR ON DR.idR = RR.idR
            LEFT JOIN User U_AP ON AR.idP = U_AP.idU
            LEFT JOIN User U_REJ ON RR.idP = U_REJ.idU
            WHERE DR.idU = ? 
            ORDER BY DR.requestDate DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute([$idU]);
    return $stmt->fetchAll();
}

function getUserDosimeterHistory($db, $idU) {
    $sql = "SELECT DAH.dosimeterSerial, DAH.assignmentDate, DAH.removalDate, 'Histórico' as estado
            FROM DosimeterAssignmentHistory DAH
            JOIN ApprovedRequest AR ON DAH.idA = AR.idA
            JOIN DosimeterRequest DR ON AR.idR = DR.idR
            WHERE DR.idU = ?
            UNION ALL
            SELECT DA.dosimeterSerial, DA.assignmentDate, NULL as removalDate, 'Ativo' as estado
            FROM DosimeterAssignment DA
            JOIN ApprovedRequest AR ON DA.idA = AR.idA
            JOIN DosimeterRequest DR ON AR.idR = DR.idR
            WHERE DR.idU = ? AND DA.status = 'Em_Uso'
            ORDER BY assignmentDate DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute([$idU, $idU]);
    return $stmt->fetchAll();
}

function getUserChanges($db, $idU) {
    $sql = "SELECT CR.*, 
                   U_ADM.name as admin_name, U_ADM.surname as admin_surname, U_ADM.email as admin_email
            FROM ChangeRecord CR
            LEFT JOIN User U_ADM ON CR.idAdmin = U_ADM.idU
            WHERE CR.idUser = ? 
            ORDER BY CR.requestDate DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute([$idU]);
    return $stmt->fetchAll();
}
?>