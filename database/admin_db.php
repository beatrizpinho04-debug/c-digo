<?php
require_once 'connection.php';

//Lógica para pesquisar datas
function prepareDateSearch($search) {
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
            $ano = $parts[0]; 
            $mes = str_pad($parts[1], 2, '0', STR_PAD_LEFT); 
            $dia = str_pad($parts[2], 2, '0', STR_PAD_LEFT);
            return "{$ano}-{$mes}-{$dia}%";
        } else {
            $dia = str_pad($parts[0], 2, '0', STR_PAD_LEFT); 
            $mes = str_pad($parts[1], 2, '0', STR_PAD_LEFT); 
            $ano = $parts[2];
            return "{$ano}-{$mes}-{$dia}%";
        }
    } elseif ($count == 2) {
        $dia = str_pad($parts[0], 2, '0', STR_PAD_LEFT); 
        $mes = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
        return "%-{$mes}-{$dia}%";
    } elseif ($count == 1) {
        return "%" . $parts[0] . "%";
    }
    
    return "%$search%";
}

// 1. Associação de Dosímetros: Pedidos aprovados e ativos sem dosímetro
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
        $term = "%$search%";
        $sql .= " AND (
                    U.name LIKE ? OR 
                    U.surname LIKE ? OR 
                    (U.name || ' ' || U.surname) LIKE ? OR 
                    U.email LIKE ? OR
                    AR.dosimeterType LIKE ?
                  )";
        array_push($params, $term, $term, $term, $term, $term);
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
        $term = "%$search%";
        $dateTerm = prepareDateSearch($search);

        $sql .= " AND (
            U.name LIKE ? OR 
            U.surname LIKE ? OR 
            (U.name || ' ' || U.surname) LIKE ? OR 
            U.email LIKE ? OR 
            DA.dosimeterSerial LIKE ? OR
            DA.assignmentDate LIKE ? OR        
            DA.nextReplacementDate LIKE ?      
        )";
        array_push($params, $term, $term, $term, $term, $term, $dateTerm, $dateTerm);
    }
    
    $sql .= " ORDER BY DA.nextReplacementDate ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// 3. Histórico de dosimetros 
function getGlobalDosimeterHistory($db, $search = '') {
    $term = "%$search%";
    $dateTerm = prepareDateSearch($search);
    $estadoSearch = (stripos($search, 'Uso') !== false) ? '%Ativo%' : $term;

    $sql = "SELECT DAH.dosimeterSerial, DAH.assignmentDate, DAH.removalDate, 
                   U.name, U.surname, U.email, 'Histórico' as estado
            FROM DosimeterAssignmentHistory DAH
            JOIN ApprovedRequest AR ON DAH.idA = AR.idA
            JOIN DosimeterRequest DR ON AR.idR = DR.idR
            JOIN User U ON DR.idU = U.idU
            WHERE 1=1";

    $params = [];
    if (!empty($search)) {
        $sql .= " AND (
                    U.name LIKE ? OR 
                    U.surname LIKE ? OR 
                    (U.name || ' ' || U.surname) LIKE ? OR 
                    U.email LIKE ? OR 
                    DAH.dosimeterSerial LIKE ? OR
                    DAH.assignmentDate LIKE ? OR
                    DAH.removalDate LIKE ? OR
                    'Histórico' LIKE ?
                )";
        $term = "%$search%";
        $params = array_merge($params, [$term, $term, $term, $term, $term, $dateTerm, $dateTerm, $estadoSearch]);
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
        $sql .= " AND (
                    U.name LIKE ? OR 
                    U.surname LIKE ? OR 
                    (U.name || ' ' || U.surname) LIKE ? OR 
                    U.email LIKE ? OR 
                    DA.dosimeterSerial LIKE ? OR
                    DA.assignmentDate LIKE ? OR
                    'Ativo' LIKE ?
                )";
        $params = array_merge($params, [$term, $term, $term, $term, $term, $dateTerm, $estadoSearch]);
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
        $term = "%$search%";
        $dateTerm = prepareDateSearch($search);

        $sql .= " AND (
            U.name LIKE ? OR 
            U.surname LIKE ? OR 
            (U.name || ' ' || U.surname) LIKE ? OR 
            U.email LIKE ? OR 
            CR.requestType LIKE ? OR
            CR.requestDate LIKE ?
        )";
        array_push($params, $term, $term, $term, $term, $term, $dateTerm);
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
        $term = "%$search%";
        $statusSearch = -1;
        if (stripos($search, 'Ativo') !== false && stripos($search, 'Inativo') === false) $statusSearch = 1;
        if (stripos($search, 'Inativo') !== false) $statusSearch = 0;

        $sql .= " AND (
            U.name LIKE ? OR 
            U.surname LIKE ? OR 
            (U.name || ' ' || U.surname) LIKE ? OR 
            U.email LIKE ? OR 
            U.userType LIKE ? OR
            HP.profession LIKE ? OR
            U.userStatus = ?
        )";
        array_push($params, $term, $term, $term, $term, $term, $term, $statusSearch);
    }
    
    $sql .= " ORDER BY U.name ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
// 5. Utilizadores: Detalhes de cada user
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