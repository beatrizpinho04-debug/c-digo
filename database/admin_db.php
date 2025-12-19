<?php
require_once 'connection.php';

// 1. ASSOCIAÇÃO: Pedidos aprovados sem dosímetro
function getPendingAssociations($db) {
    $sql = "SELECT DA.idDA, U.name, U.surname, U.email, AR.dosimeterType, AR.periodicity, AR.riskCategory
            FROM DosimeterAssignment DA
            JOIN ApprovedRequest AR ON DA.idA = AR.idA
            JOIN DosimeterRequest DR ON AR.idR = DR.idR
            JOIN User U ON DR.idU = U.idU
            WHERE DA.status = 'Por_Associar'
            ORDER BY AR.approvalDate ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// 2. Gestão de Dosimetros: Contagem de dosimetros para pedir e trocar
function getDosimeterStats($db) {
    $diaAtual = date('d');
 
    if ($diaAtual > 20) {
        $dataInicioAnalise = date('Y-m-21');
        $dataFimAnalise = date('Y-m-20', strtotime('+1 month'));
        $mesAbastecimento = date('F', strtotime('+2 months'));
        $inicioProxMes = date('Y-m-01', strtotime('+2 months'));
        $fimProxMes = date('Y-m-t', strtotime('+2 months'));
    } else {
        $dataInicioAnalise = date('Y-m-21', strtotime('-1 month'));
        $dataFimAnalise = date('Y-m-20');
        $mesAbastecimento = date('F', strtotime('+1 month'));
        $inicioProxMes = date('Y-m-01', strtotime('+1 month'));
        $fimProxMes = date('Y-m-t', strtotime('+1 month'));
    }

    $sqlAnalise = "SELECT COUNT(*) FROM DosimeterAssignmentHistory WHERE insertDate BETWEEN ? AND ?";
    $stmt = $db->prepare($sqlAnalise);
    $stmt->execute([$dataInicioAnalise, $dataFimAnalise]);
    $enviadosAnalise = $stmt->fetchColumn();

    $sqlPedir = "SELECT COUNT(*) FROM DosimeterAssignment 
                 WHERE status = 'Em_Uso' AND nextReplacementDate BETWEEN ? AND ?";
    $stmt2 = $db->prepare($sqlPedir);
    $stmt2->execute([$inicioProxMes, $fimProxMes]);
    $aPedir = $stmt2->fetchColumn();

  
    $mesesPT = ['January'=>'Janeiro','February'=>'Fevereiro','March'=>'Março','April'=>'Abril','May'=>'Maio','June'=>'Junho','July'=>'Julho','August'=>'Agosto','September'=>'Setembro','October'=>'Outubro','November'=>'Novembro','December'=>'Dezembro'];
    $mesNome = isset($mesesPT[$mesAbastecimento]) ? $mesesPT[$mesAbastecimento] : $mesAbastecimento;

    return [
        'enviados' => $enviadosAnalise,
        'pedir' => $aPedir,
        'periodo_analise' => date('d/m', strtotime($dataInicioAnalise)) . ' a ' . date('d/m', strtotime($dataFimAnalise)),
        'mes_abastecimento' => $mesNome
    ];
}

// 2. Gestão de Dosimetros: Lista de só pessoas com pedidos/aprovações Ativas (com filtro de pesquisa)
function getActiveDosimeters($db, $search = '') {
    $sql = "SELECT DA.idDA, DA.dosimeterSerial, DA.assignmentDate, DA.nextReplacementDate, U.name, U.surname, U.email, AR.dosimeterType
            FROM DosimeterAssignment DA
            JOIN ApprovedRequest AR ON DA.idA = AR.idA
            JOIN DosimeterRequest DR ON AR.idR = DR.idR
            JOIN User U ON DR.idU = U.idU
            WHERE DA.status = 'Em_Uso'";
    
    $params = [];
    if (!empty($search)) {
        $sql .= " AND (U.name LIKE ? OR U.surname LIKE ? OR U.email LIKE ? OR DA.dosimeterSerial LIKE ?)";
        $term = "%$search%";
        $params = [$term, $term, $term, $term];
    }
    
    $sql .= " ORDER BY DA.nextReplacementDate ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// 3. Pedidos de Suspensão/Ativação
function getPendingChangeRequests($db) {
    $sql = "SELECT CR.*, U.name, U.surname, U.email 
            FROM ChangeRecord CR
            JOIN User U ON CR.idUser = U.idU
            WHERE CR.status = 'Pendente' ORDER BY CR.requestDate ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// 4. Utilizadores: Listar todos os users (com filtro de pesquisa)
function getAllUsers($db, $search = '') {
    $sql = "SELECT U.*, HP.profession, HP.department 
            FROM User U
            LEFT JOIN HealthProfessional HP ON U.idU = HP.idU
            WHERE 1=1"; 
            
    $params = [];
    if (!empty($search)) {
        $sql .= " AND (U.name LIKE ? OR U.surname LIKE ? OR U.email LIKE ? OR HP.profession LIKE ?)";
        $term = "%$search%";
        $params = [$term, $term, $term, $term];
    }
    
    $sql .= " ORDER BY U.name ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// 4. Utilizadores: Detalhes de cada user
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

?>