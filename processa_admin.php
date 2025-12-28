<?php
session_start();
require_once 'database/connection.php';

if (!isset($_SESSION['idU']) || $_SESSION['userType'] !== "Administrador") {
    header("Location: index.php");
    exit();
}

$db = getDatabaseConnection();
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

if (empty($action)) {
    header("Location: admin.php");
    exit();
}

try {
    // 1. Associar Dosímetro pela primeira vez
    if ($action === 'associar_dosimetro') {
        $idDA = $_POST['idDA'];
        $serial = trim($_POST['serial']);
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
        $dataHoje = date('Y-m-d H:i:s');
        if ($info['periodicity'] === 'Trimestral') {
            $next = date('Y-m-d H:i:s', strtotime("+90 days"));
        } else {
            $next = date('Y-m-d H:i:s', strtotime("+30 days"));
        }

        // C. Atualizar tabela DosimeterAssignment
        $stmtUpd = $db->prepare(" UPDATE DosimeterAssignment 
        SET dosimeterSerial = ?, assignmentDate = ?, nextReplacementDate = ?, status = 'Em_Uso'
        WHERE idDA = ?");
        $stmtUpd->execute([$serial, $dataHoje, $next, $idDA]);

        $db->commit();
        $_SESSION['message'] = "Dosímetro associado com sucesso!";
        $_SESSION['message_type'] = "success";
        header("Location: admin.php?tab=associacao");
        exit();
    }

    // 2. Trocar de dosimetro
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
        $dataHoje = date('Y-m-d H:i:s');
        if ($current['periodicity'] === 'Trimestral') {
            $newNext = date('Y-m-d H:i:s', strtotime("+90 days"));
        } else {
            $newNext = date('Y-m-d H:i:s', strtotime("+30 days"));
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
        $idCR = $_POST['idCR'];
        $decisao = $_POST['decisao'];
        $adminNote = isset($_POST['adminNote']) ? trim($_POST['adminNote']) : '';
        $adminId = $_SESSION['idU'];
        $now = date('Y-m-d H:i:s');

        $db->beginTransaction();
        $stmt = $db->prepare("SELECT * FROM ChangeRecord WHERE idCR = ?");
        $stmt->execute([$idCR]);
        $req = $stmt->fetch();

        if (!$req) {
            throw new Exception("Pedido não encontrado (ChangeRecord idCR={$idCR}).");
        }

        $finalStatus = ($decisao === 'aprovado') ? 'Concluido' : 'Rejeitado';
        $final = ($decisao === 'aprovado') ? ($req['requestType'] === 'Suspender' ? 'Suspenso' : 'Ativo') : null;
        $stmtUpd = $db->prepare("UPDATE ChangeRecord SET status = ?, idAdmin = ?, decisionDate = ?, finalStatus = ?, adminNote = ? WHERE idCR = ?");
        $stmtUpd->execute([$finalStatus, $adminId, $now, $final, $adminNote, $idCR]);

        if ($decisao === 'aprovado') {
            if ($req['requestType'] === 'Suspender') {
                $db->prepare("UPDATE ApprovedRequest SET status = 'Suspenso' WHERE idA = ?")->execute([$req['idA']]);
                $db->prepare("UPDATE DosimeterAssignment SET status = 'Suspenso', dosimeterSerial = NULL, assignmentDate = NULL, nextReplacementDate = NULL WHERE idA = ?")->execute([$req['idA']]);
            }
            elseif ($req['requestType'] === 'Ativar') {
                $db->prepare("UPDATE ApprovedRequest SET status = 'Ativo' WHERE idA = ?")->execute([$req['idA']]);
                $db->prepare("UPDATE DosimeterAssignment SET status = 'Por_Associar' WHERE idA = ?")->execute([$req['idA']]);
            }
        }
        $db->commit();
        $_SESSION['message'] = "Pedido processado.";
        $_SESSION['message_type'] = "success";
        header("Location: admin.php?tab=pedidos");
        exit();
    }

    // 5. Criar Utilizador
    elseif ($action === 'create_user') {
        $email = trim($_POST['email']);
        $phone = trim($_POST['phoneN']);
        $rawPass = $_POST['password'];
        $userType = $_POST['userType'];
        
        // A. Validar Email Único
        $stmtEmail = $db->prepare("SELECT idU FROM User WHERE email = ?");
        $stmtEmail->execute([$email]);
        if ($stmtEmail->fetch()) {
            throw new Exception("Erro: O email '$email' já está registado.");
        }

        // B. Validar Formato Telemóvel (+351...)
        if (!preg_match('/^\+[0-9]{11,15}$/', $phone)) {
            throw new Exception("Erro: O telemóvel deve estar no formato internacional (ex: +351912345678).");
        }

        // C. Hash da Password
        $hashedPass = password_hash($rawPass, PASSWORD_DEFAULT);

        // D. Validar campos extra se for Profissional de Saúde
        $profession = null;
        $department = null;
        if ($userType === 'Profissional de Saúde') {
            $profession = trim($_POST['profession']);
            $department = trim($_POST['department']);
            if (empty($profession) || empty($department)) {
                throw new Exception("Erro: Profissão e Departamento são obrigatórios para Profissionais de Saúde.");
            }
        }

        $db->beginTransaction();
        
        // Inserir User (userStatus = 1 ativo, profilePic é default na base)
        $stmt = $db->prepare("INSERT INTO User (name, surname, email, password, phoneN, birthDate, sex, userType) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['name'], $_POST['surname'], $email, $hashedPass, $phone, $_POST['birthDate'], $_POST['sex'], $_POST['userType']]);
        $idU = $db->lastInsertId();

        // Inserir HealthProfessional se necessário
        if ($userType === 'Profissional de Saúde') {
            $stmtHP = $db->prepare("INSERT INTO HealthProfessional (idU, profession, department) VALUES (?, ?, ?)");
            $stmtHP->execute([$idU, $profession, $department]);
        }

        $db->commit();
        $_SESSION['message'] = "Utilizador criado!";
        $_SESSION['message_type'] = "success";
        header("Location: admin.php?tab=users");
        exit();
    }
    // 5. Ativar/Desativar user
    elseif ($action === 'toggle_status') {
        $idU = $_POST['idU'];
        $novoEstado = ($_POST['currentStatus'] == 1) ? 0 : 1;
        $dataHoje = date('Y-m-d H:i:s');

        $db->beginTransaction();

        $db->prepare("UPDATE User SET userStatus = ? WHERE idU = ?")->execute([$novoEstado, $idU]);

        if ($novoEstado == 0) {
            //Desativatar
            // 1º Suspender pedidos aprovados
            $db->prepare("UPDATE ApprovedRequest 
                          SET status = 'Suspenso'  
                          WHERE idR IN (SELECT idR FROM DosimeterRequest WHERE idU = ?)")
                ->execute([$idU]);

            // 2º Recolher Dosímetros Ativos
            $stmtReclaim = $db->prepare("UPDATE DosimeterAssignment 
                                         SET status = 'Suspenso', dosimeterSerial = NULL, assignmentDate = NULL, nextReplacementDate = NULL 
                                         WHERE idA IN (
                                             SELECT AR.idA 
                                             FROM ApprovedRequest AR
                                             JOIN DosimeterRequest DR ON AR.idR = DR.idR
                                             WHERE DR.idU = ?)");
            $stmtReclaim->execute([$idU]);
        } else {
            // Ativar
            // 1º Ativar pedidos aprovados associados a este user
            $db->prepare("UPDATE ApprovedRequest 
                          SET status = 'Ativo' 
                          WHERE idR IN (SELECT idR FROM DosimeterRequest WHERE idU = ?)")
                ->execute([$idU]);

            // 2º Mudar os DosimeterAssigment associado ao pedido/autorização deste user para 'Por_Associar'
            $stmtReactivate = $db->prepare("UPDATE DosimeterAssignment 
                          SET status = 'Por_Associar' 
                          WHERE idA IN (
                              SELECT AR.idA 
                              FROM ApprovedRequest AR
                              JOIN DosimeterRequest DR ON AR.idR = DR.idR
                              WHERE DR.idU = ?
                          )");
            $stmtReactivate->execute([$idU]);
        }

        $db->commit();
        
        $_SESSION['message'] = $msg;
        $_SESSION['message_type'] = "success";

        // Redirecionamento inteligente
        if (isset($_POST['source_page']) && $_POST['source_page'] === 'details') {
            header("Location: user_details.php?idU=" . $idU);
        } else {
            header("Location: admin.php?tab=users");
        }
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
    elseif ($action == 'decide_suspensao') header("Location: admin.php?tab=pedidos");
    elseif ($action == 'create_user' || $action === 'toggle_status') header("Location: admin.php?tab=users");
    else header("Location: admin.php");
    exit();
}
?>