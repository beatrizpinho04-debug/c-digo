<?php
require_once 'connection.php';

// 1. ASSOCIAÇÃO: Pedidos aprovados sem dosímetro
function getPendingAssociations($db, $search = '') {
    $sql = "SELECT DA.idDA, U.name, U.surname, U.email, AR.dosimeterType, AR.periodicity, AR.riskCategory, DR.pratica
            FROM DosimeterAssignment DA
            JOIN ApprovedRequest AR ON DA.idA = AR.idA
            JOIN DosimeterRequest DR ON AR.idR = DR.idR
            JOIN User U ON DR.idU = U.idU
            WHERE DA.status = 'Por_Associar'
              AND U.userStatus = 1
              AND AR.status = 'Ativo'";

    $params = [];
    if (!empty($search)) {
        $sql .= " AND (
                    U.name LIKE ? OR 
                    U.surname LIKE ? OR 
                    (U.name || ' ' || U.surname) LIKE ? OR 
                    U.email LIKE ? OR 
                    DR.pratica LIKE ?
                  )";
        $term = "%$search%";
        $params = [$term, $term, $term, $term, $term];
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// 2. Gestão de Dosimetros: Contagem de dosimetros para pedir e trocar
function getDosimeterStats($db) {
    $diaAtual = date('d');
 
    if ($diaAtual > 20) {
        //Lógica: 
        //Se hoje for 21/12, vão para análise os dosimetros recolhidos desde 21/12 a 20/01
        $dataInicioAnalise = date('Y-m-21');
        $dataFimAnalise = date('Y-m-20', strtotime('+1 month'));
        // O próximo mês de abastecimento é Janeiro, por isso calcula todos os dosimetros que são esperados ser trocados
        $mesAbastecimento = date('F', strtotime('+1 month'));
        $inicioProxMes = date('Y-m-01', strtotime('+1 month'));
        $fimProxMes = date('Y-m-t', strtotime('+1 month'));
    } else {
        //Lógica:
        //Se hoje for 15/12, vão para análise os dosimetros recolhidos desde 21/11 a 20/12
        $dataInicioAnalise = date('Y-m-21', strtotime('-1 month'));
        $dataFimAnalise = date('Y-m-20');
        // O próximo mês de abastecimento continua a ser Janeiro, por isso calcula todos os dosimetros que são esperados ser trocados
        $mesAbastecimento = date('F', strtotime('+1 month'));
        $inicioProxMes = date('Y-m-01', strtotime('+1 month'));
        $fimProxMes = date('Y-m-t', strtotime('+1 month'));
    }

    // Contagem de quantos dosímetros vão ser recolhidos, contando com os users inativos ou com autorizações suspensas
    $sqlAnalise = "SELECT COUNT(*) FROM DosimeterAssignmentHistory WHERE removalDate BETWEEN ? AND ?";
    $stmt = $db->prepare($sqlAnalise);
    $stmt->execute([$dataInicioAnalise, $dataFimAnalise]);
    $enviadosAnalise = $stmt->fetchColumn();

    // Contagem de quantos dosímetros vão necessários pedir, não contando com os users inativos ou com autorizações suspensas
    $sqlPedir = "SELECT COUNT(*) 
                 FROM DosimeterAssignment DA
                 JOIN ApprovedRequest AR ON DA.idA = AR.idA
                 JOIN DosimeterRequest DR ON AR.idR = DR.idR
                 JOIN User U ON DR.idU = U.idU
                 WHERE DA.status = 'Em_Uso' 
                   AND U.userStatus = 1       
                   AND AR.status = 'Ativo'    
                   AND DA.nextReplacementDate BETWEEN ? AND ?";
    $stmt2 = $db->prepare($sqlPedir);
    $stmt2->execute([$inicioProxMes, $fimProxMes]);
    $aPedir = $stmt2->fetchColumn();

  
    $mesesPT = ['January'=>'janeiro','February'=>'fevereiro','March'=>'março','April'=>'abril','May'=>'maio','June'=>'junho','July'=>'julho','August'=>'agosto','September'=>'setembro','October'=>'outubro','November'=>'novembro','December'=>'dezembro'];
    $mesNome = isset($mesesPT[$mesAbastecimento]) ? $mesesPT[$mesAbastecimento] : $mesAbastecimento;

    return [
        'enviados' => $enviadosAnalise,
        'pedir' => $aPedir,
        'periodo_analise' => date('d/m', strtotime($dataInicioAnalise)) . ' a ' . date('d/m', strtotime($dataFimAnalise)),
        'mes_abastecimento' => $mesNome
    ];
}

// 2. Gestão de Dosimetros: Lista de só pessoas com pedidos/aprovações ativas (com filtro de pesquisa)
function getActiveDosimeters($db, $search = '') {
    $sql = "SELECT DA.idDA, DA.dosimeterSerial, DA.assignmentDate, DA.nextReplacementDate, U.name, U.surname, U.email, AR.dosimeterType, DR.pratica
            FROM DosimeterAssignment DA
            JOIN ApprovedRequest AR ON DA.idA = AR.idA
            JOIN DosimeterRequest DR ON AR.idR = DR.idR
            JOIN User U ON DR.idU = U.idU
            WHERE DA.status = 'Em_Uso'
              AND U.userStatus = 1 
              AND AR.status = 'Ativo'";
    
    $params = [];
    if (!empty($search)) {
        $sql .= " AND (U.name LIKE ? OR U.surname LIKE ? OR (U.name || ' ' || U.surname) LIKE ? OR U.email LIKE ? OR DA.dosimeterSerial LIKE ?)";
        $term = "%$search%";
        $params = [$term, $term, $term, $term, $term];
    }
    
    $sql .= " ORDER BY DA.nextReplacementDate ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// 3. Histórico de dosimetros 
function getGlobalDosimeterHistory($db, $search = '') {
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

    $sql .= "ORDER BY DAH.removalDate DESC NULLS FIRST";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// 4. Pedidos de Suspensão/Ativação
function getPendingChangeRequests($db, $search = '') {
    $sql = "SELECT CR.*, U.name, U.surname, U.email 
            FROM ChangeRecord CR
            JOIN User U ON CR.idUser = U.idU
            WHERE CR.status = 'Pendente'
              AND U.userStatus = 1";

    $params = [];
    if (!empty($search)) {
        $sql .= " AND (
                    U.name LIKE ? OR 
                    U.surname LIKE ? OR 
                    (U.name || ' ' || U.surname) LIKE ? OR 
                    U.email LIKE ?
                  )";
        $term = "%$search%";
        $params = [$term, $term, $term, $term];
    }

    $sql .= " ORDER BY CR.requestDate ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// 5. Utilizadores: Listar todos os users (com filtro de pesquisa)
function getAllUsers($db, $search = '') {
    $sql = "SELECT U.*, HP.profession, HP.department 
            FROM User U
            LEFT JOIN HealthProfessional HP ON U.idU = HP.idU
            WHERE 1=1"; 
            
    $params = [];
    if (!empty($search)) {
        $sql .= " AND (U.name LIKE ? OR U.surname LIKE ? OR (U.name || ' ' || U.surname) LIKE ? OR U.email LIKE ? OR HP.profession LIKE ?)";
        $term = "%$search%";
        $params = [$term, $term, $term, $term, $term];
    }
    
    $sql .= " ORDER BY U.name ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// 6. Utilizadores: Detalhes de cada user
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