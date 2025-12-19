<?php
session_start();
require_once 'database/connection.php';

// Segurança: Apenas Administradores
if (!isset($_SESSION['idU']) || $_SESSION['userType'] !== "Administrador") {
    header("Location: index.php");
    exit();
}

$db = getDatabaseConnection();

// Obter a ação (pode vir de POST ou GET)
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// Se não houver ação, manda de volta para o início
if (empty($action)) {
    header("Location: admin.php");
    exit();
}

try {
    // 1. ASSOCIAR DOSÍMETRO (Primeira vez)
    if ($action === 'associar_dosimetro') {
        $idDA = $_POST['idDA'];
        $serial = trim($_POST['serial']);
        $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

        if (empty($serial)) throw new Exception("Número de série é obrigatório.");

        $db->beginTransaction();

        $stmtInfo = $db->prepare("SELECT DA.idA, AR.periodicity
        FROM DosimeterAssignment DA 
        JOIN ApprovedRequest AR ON DA.idA = AR.idA 
        WHERE DA.idDA = ?");
        $stmtInfo->execute([$idDA]);
        $info = $stmtInfo->fetch();

        if (!$info) throw new Exception("Atribuição não encontrada.");

        // B. Calcular a data quando se faz a mudança + da periocidade)
        $dataHoje = date('Y-m-d');
        if ($info['periodicity'] === 'Trimestral') {
            $next = date('Y-m-d', strtotime("+90 days"));
        } else {
            $next = date('Y-m-d', strtotime("+30 days"));
        }

        // C. Atualizar tabela DosimeterAssignment
        $stmtUpd = $db->prepare(" UPDATE DosimeterAssignment 
        SET dosimeterSerial = ?, assignmentDate = ?, nextReplacementDate = ?, status = 'Em_Uso', notes = ?
        WHERE idDA = ?");
        $stmtUpd->execute([$serial, $dataHoje, $next, $notes, $idDA]);

        $db->commit();
        $_SESSION['message'] = "Dosímetro associado com sucesso!";
        $_SESSION['message_type'] = "success";
        header("Location: admin.php?tab=associacao");
        exit();
    }

    // 2. Trocar dosimetro
    elseif ($action === 'trocar_dosimetro') {
        $idDA = $_POST['idDA'];
        $newSerial = trim($_POST['newSerial']);
        if (empty($newSerial)) throw new Exception("Novo serial obrigatório.");

        $db->beginTransaction();

        // Buscar info atual
        $stmt = $db->prepare(" SELECT DA.idA, AR.periodicity 
        FROM DosimeterAssignment DA 
        JOIN ApprovedRequest AR ON DA.idA = AR.idA 
        WHERE DA.idDA = ?");
        $stmt->execute([$idDA]);
        $current = $stmt->fetch();

        // Recalcular datas
        $dataHoje = date('Y-m-d');
        if ($current['periodicity'] === 'Trimestral') {
            $newNext = date('Y-m-d', strtotime("+90 days"));
        } else {
            $newNext = date('Y-m-d', strtotime("+30 days"));
        }

        // Atualizar tabela DosimeterAssignment
        $stmtUpd = $db->prepare("UPDATE DosimeterAssignment 
        SET dosimeterSerial = ?, 
        assignmentDate = ?, 
        nextReplacementDate = ?
        WHERE idDA = ?");
        $stmtUpd->execute([$newSerial, $dataHoje, $newNext, $idDA]);

        $db->commit();
        $_SESSION['message'] = "Troca registada com sucesso!";
        $_SESSION['message_type'] = "success";
        header("Location: admin.php?tab=gestao");
        exit();
    }
    // 3. Suspender ou ativar pedido/autorização
    elseif ($action === 'decide_suspensao') {
        $idCR = $_GET['idCR'];
        $decisao = $_GET['decisao'];
        $adminId = $_SESSION['idU'];
        $now = date('Y-m-d H:i:s');

        $db->beginTransaction();
        $stmt = $db->prepare("SELECT * FROM ChangeRecord WHERE idCR = ?");
        $stmt->execute([$idCR]);
        $req = $stmt->fetch();

        if ($req) {
            $finalStatus = ($decisao === 'aprovado') ? 'Concluído' : 'Rejeitado';
            $stmt = $db->prepare("UPDATE ChangeRecord SET status = ?, idAdmin = ?, decisionDate = ? WHERE idCR = ?");
            $stmt->execute([$finalStatus, $adminId, $now, $idCR]);

            if ($decisao === 'aprovado') {
                if ($req['requestType'] === 'Suspender') {
                    $db->prepare("UPDATE ApprovedRequest SET status = 'Suspenso' WHERE idA = ?")->execute([$req['idA']]);
                    $db->prepare("UPDATE DosimeterAssignment SET status = 'Suspenso' WHERE idA = ?")->execute([$req['idA']]);
                    
                    $stmtS = $db->prepare("SELECT dosimeterSerial FROM DosimeterAssignment WHERE idA = ?");
                    $stmtS->execute([$req['idA']]);
                    $serial = $stmtS->fetchColumn();
                    if($serial) {
                        $db->prepare("INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, insertDate) VALUES (?, ?, ?)")
                           ->execute([$req['idA'], $serial . ' (Suspenso)', $now]);
                    }

                } elseif ($req['requestType'] === 'Ativar') {
                    $db->prepare("UPDATE ApprovedRequest SET status = 'Ativo' WHERE idA = ?")->execute([$req['idA']]);
                    $db->prepare("UPDATE DosimeterAssignment SET status = 'Por_Associar' WHERE idA = ?")->execute([$req['idA']]);
                }
            }
        }
        $db->commit();
        $_SESSION['message'] = "Pedido processado.";
        $_SESSION['message_type'] = "success";
        header("Location: admin.php?tab=pedidos");
        exit();
    }
    // 3. Criar Utilizador
    elseif ($action === 'create_user') {
        $pass = $_POST['password']; 

        $db->beginTransaction();
        $stmt = $db->prepare("INSERT INTO User (name, surname, email, password, phoneN, birthDate, sex, userType, userStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$_POST['name'], $_POST['surname'], $_POST['email'], $pass, $_POST['phoneN'], $_POST['birthDate'], $_POST['sex'], $_POST['userType']]);
        $idU = $db->lastInsertId();

        // Inserir nas tabelas específicas
        if ($_POST['userType'] === 'Profissional de Saúde') {
            $stmt = $db->prepare("INSERT INTO HealthProfessional (idU, profession, department) VALUES (?, ?, ?)");
            $stmt->execute([$idU, $_POST['profession'], $_POST['department']]);
        }
        $db->commit();
        $_SESSION['message'] = "Utilizador criado!";
        $_SESSION['message_type'] = "success";
        header("Location: admin.php?tab=users");
        exit();
    }
    // 4. Ativar/Desativar User
    elseif ($action === 'toggle_status') {
        $newStatus = $_POST['currentStatus'] == 1 ? 0 : 1;
        $stmt = $db->prepare("UPDATE User SET userStatus = ? WHERE idU = ?");
        $stmt->execute([$newStatus, $_POST['idU']]);
        
        $_SESSION['message'] = "Estado do utilizador alterado.";
        $_SESSION['message_type'] = "success";
        header("Location: admin.php?tab=users");
        exit();
    }

    
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    $_SESSION['message'] = "Erro: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
    if ($action == 'associar_dosimetro') header("Location: admin.php?tab=associacao");
    elseif ($action == 'trocar_dosimetro') header("Location: admin.php?tab=gestao");
    elseif ($action == 'create_user' || $action == 'toggle_status') header("Location: admin.php?tab=users");
    else header("Location: admin.php");
    exit();
}
?>