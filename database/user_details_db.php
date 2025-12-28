<?php
require_once 'connection.php';

//Lógica para as datas
function prepareDateSearchUser($search) {
    if (empty($search)) return "%$search%";

    $dateString = mb_strtolower($search, 'UTF-8');
  
    $meses = [
        'janeiro' => ' 01 ', 'fevereiro' => ' 02 ', 'março' => ' 03 ', 'marco' => ' 03 ',
        'abril' => ' 04 ', 'maio' => ' 05 ', 'junho' => ' 06 ', 'julho' => ' 07 ',
        'agosto' => ' 08 ', 'setembro' => ' 09 ', 'outubro' => ' 10 ', 'novembro' => ' 11 ', 'dezembro' => ' 12 ',
        'jan' => ' 01 ', 'fev' => ' 02 ', 'mar' => ' 03 ', 'abr' => ' 04 ', 'mai' => ' 05 ', 'jun' => ' 06 ',
        'jul' => ' 07 ', 'ago' => ' 08 ', 'set' => ' 09 ', 'out' => ' 10 ', 'nov' => ' 11 ', 'dez' => ' 12 '
    ];
    foreach ($meses as $nome => $num) {
        $dateString = str_replace($nome, $num, $dateString);
    }

    $onlyNumbers = preg_replace('/[^0-9]/', ' ', $dateString);
    $parts = array_values(array_filter(explode(' ', $onlyNumbers)));
    $count = count($parts);

    if ($count == 3) {
        if ($parts[0] > 31) {
            $ano = $parts[0]; $mes = str_pad($parts[1], 2, '0', STR_PAD_LEFT); $dia = str_pad($parts[2], 2, '0', STR_PAD_LEFT);
            return "{$ano}-{$mes}-{$dia}%";
        } else {
            $dia = str_pad($parts[0], 2, '0', STR_PAD_LEFT); $mes = str_pad($parts[1], 2, '0', STR_PAD_LEFT); $ano = $parts[2];
            return "{$ano}-{$mes}-{$dia}%";
        }
    } elseif ($count == 2) {
        $dia = str_pad($parts[0], 2, '0', STR_PAD_LEFT); $mes = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
        return "%-{$mes}-{$dia}%";
    } elseif ($count == 1) {
        return "%" . $parts[0] . "%";
    }
    
    return "%$search%";
}

//1. Informações do user
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

//2. Histórico de pedidos do user
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

//3. Histórico de dosímetros do user
function getUserDosimeterHistory($db, $idU, $search = '') {
    $term = "%$search%";
    $dateTerm = prepareDateSearchUser($search);
    $estadoSearch = $term;
    if (stripos($search, 'Uso') !== false) {
        $estadoSearch = '%Ativo%';
    } elseif (stripos($search, 'Recolhido') !== false) {
        $estadoSearch = '%Histórico%';
    }

    $params = [];
    $sql1 = "SELECT DAH.dosimeterSerial, DAH.assignmentDate, DAH.removalDate, 'Histórico' as estado
             FROM DosimeterAssignmentHistory DAH
             JOIN ApprovedRequest AR ON DAH.idA = AR.idA
             JOIN DosimeterRequest DR ON AR.idR = DR.idR
             WHERE DR.idU = ?";
    
    array_push($params, $idU);

    if (!empty($search)) {
        $sql1 .= " AND (
            DAH.dosimeterSerial LIKE ? OR 
            DAH.assignmentDate LIKE ? OR
            DAH.removalDate LIKE ? OR
            'Histórico' LIKE ?
        )";
        array_push($params, $term, $dateTerm, $dateTerm, $estadoSearch);
    }
    $sql2 = "SELECT DA.dosimeterSerial, DA.assignmentDate, NULL as removalDate, 'Ativo' as estado
             FROM DosimeterAssignment DA
             JOIN ApprovedRequest AR ON DA.idA = AR.idA
             JOIN DosimeterRequest DR ON AR.idR = DR.idR
             WHERE DR.idU = ? AND DA.status = 'Em_Uso'";
    
    array_push($params, $idU);

    if (!empty($search)) {
        $sql2 .= " AND (
            DA.dosimeterSerial LIKE ? OR 
            DA.assignmentDate LIKE ? OR
            'Ativo' LIKE ?
        )";
        array_push($params, $term, $dateTerm, $estadoSearch);
    }
    $finalSql = $sql1 . " UNION ALL " . $sql2 . " ORDER BY assignmentDate DESC";
    $stmt = $db->prepare($finalSql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

//4. Histórico de alterações do user
function getUserChanges($db, $idU, $search = '') {
    $sql = "SELECT CR.*, 
                   U_ADM.name as admin_name, U_ADM.surname as admin_surname, U_ADM.email as admin_email
            FROM ChangeRecord CR
            LEFT JOIN User U_ADM ON CR.idAdmin = U_ADM.idU
            WHERE CR.idUser = ?";
            
    $params = [$idU];

    if (!empty($search)) {
        $term = "%$search%";
        $dateTerm = prepareDateSearchUser($search);

        $sql .= " AND (
            CR.requestType LIKE ? OR
            CR.status LIKE ? OR
            CR.adminNote LIKE ? OR
            U_ADM.name LIKE ? OR
            U_ADM.surname LIKE ? OR
            U_ADM.email LIKE ? OR
            CR.requestDate LIKE ? OR
            CR.decisionDate LIKE ?
        )";
        array_push($params, $term, $term, $term, $term, $term, $term, $dateTerm, $dateTerm);
    }

    $sql .= " ORDER BY CR.requestDate DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
?>