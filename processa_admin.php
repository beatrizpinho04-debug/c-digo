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
    // =========================================================
    // 1. ASSOCIAR DOSÍMETRO (Primeira vez)
    // =========================================================
    if ($action === 'associar_dosimetro') {
        $idDA = $_POST['idDA'];
        $serial = trim($_POST['serial']);
        $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

        if (empty($serial)) throw new Exception("Número de série é obrigatório.");

        $db->beginTransaction();

        // A. Buscar dados do pedido (precisamos do idA para o histórico e periodicidade para as datas)
        $stmtInfo = $db->prepare("
            SELECT DA.idA, AR.periodicity 
            FROM DosimeterAssignment DA 
            JOIN ApprovedRequest AR ON DA.idA = AR.idA 
            WHERE DA.idDA = ?
        ");
        $stmtInfo->execute([$idDA]);
        $info = $stmtInfo->fetch();

        if (!$info) throw new Exception("Atribuição não encontrada.");

        // B. Calcular datas (Hoje + Periodicidade)
        $dataHoje = date('Y-m-d');
        $days = ($info['periodicity'] === 'Trimestral') ? 90 : 30;
        $dataProxima = date('Y-m-d', strtotime("+$days days"));

        // C. Atualizar tabela DosimeterAssignment
        $stmtUpd = $db->prepare("
            UPDATE DosimeterAssignment 
            SET dosimeterSerial = ?, 
                assignmentDate = ?, 
                nextReplacementDate = ?, 
                status = 'Em_Uso', 
                notes = ?
            WHERE idDA = ?
        ");
        $stmtUpd->execute([$serial, $dataHoje, $dataProxima, $notes, $idDA]);

        // D. Inserir no Histórico (Nota: o teu SQL usa idA e insertDate)
        $stmtHist = $db->prepare("
            INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, insertDate)
            VALUES (?, ?, ?)
        ");
        $stmtHist->execute([$info['idA'], $serial, date('Y-m-d H:i:s')]);

        $db->commit();
        $_SESSION['message'] = "Dosímetro associado com sucesso!";
        $_SESSION['message_type'] = "success";
        header("Location: admin.php?tab=associacao");
        exit();
    }

    // =========================================================
    // 2. TROCAR DOSÍMETRO (Manutenção)
    // =========================================================
    elseif ($action === 'trocar_dosimetro') {
        $idDA = $_POST['idDA'];
        $newSerial = trim($_POST['newSerial']);
        
        if (empty($newSerial)) throw new Exception("Novo serial obrigatório.");

        $db->beginTransaction();

        // Buscar info atual
        $stmt = $db->prepare("
            SELECT DA.idA, AR.periodicity 
            FROM DosimeterAssignment DA 
            JOIN ApprovedRequest AR ON DA.idA = AR.idA 
            WHERE DA.idDA = ?
        ");
        $stmt->execute([$idDA]);
        $current = $stmt->fetch();

        // Inserir o NOVO serial no histórico
        // (Isto conta como uma "troca" nas estatísticas porque aparece com data de hoje)
        $stmtHist = $db->prepare("
            INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, insertDate)
            VALUES (?, ?, ?)
        ");
        $stmtHist->execute([$current['idA'], $newSerial, date('Y-m-d H:i:s')]);

        // Recalcular datas
        $dataHoje = date('Y-m-d');
        $days = ($current['periodicity'] === 'Trimestral') ? 90 : 30;
        $newNext = date('Y-m-d', strtotime("+$days days"));

        // Atualizar tabela principal
        $stmtUpd = $db->prepare("
            UPDATE DosimeterAssignment 
            SET dosimeterSerial = ?, 
                assignmentDate = ?, 
                nextReplacementDate = ?
            WHERE idDA = ?
        ");
        $stmtUpd->execute([$newSerial, $dataHoje, $newNext, $idDA]);

        $db->commit();
        $_SESSION['message'] = "Troca registada com sucesso!";
        $_SESSION['message_type'] = "success";
        header("Location: admin.php?tab=gestao");
        exit();
    }

    // =========================================================
    // 3. CRIAR UTILIZADOR
    // =========================================================
    elseif ($action === 'create_user') {
        // Nota: Em produção deves usar password_hash($_POST['password'], PASSWORD_DEFAULT)
        $pass = $_POST['password']; 

        $db->beginTransaction();

        // Inserir na tabela User (campo userStatus em vez de active)
        $stmt = $db->prepare("INSERT INTO User (name, surname, email, password, phoneN, birthDate, sex, userType, userStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([
            $_POST['name'], $_POST['surname'], $_POST['email'], 
            $pass, 
            $_POST['phoneN'], $_POST['birthDate'], $_POST['sex'], $_POST['userType']
        ]);
        $idU = $db->lastInsertId();

        // Inserir nas tabelas específicas
        if ($_POST['userType'] === 'Profissional de Saúde') {
            $stmt = $db->prepare("INSERT INTO HealthProfessional (idU, profession, department) VALUES (?, ?, ?)");
            $stmt->execute([$idU, $_POST['profession'], $_POST['department']]);
        } elseif ($_POST['userType'] === 'Físico Médico') {
            // Assumindo que a tabela Physicist existe
            $db->prepare("INSERT INTO Physicist (idU) VALUES (?)")->execute([$idU]);
        } elseif ($_POST['userType'] === 'Administrador') {
            // Assumindo que a tabela Admin existe
            $db->prepare("INSERT INTO Admin (idU) VALUES (?)")->execute([$idU]);
        }

        $db->commit();
        $_SESSION['message'] = "Utilizador criado!";
        $_SESSION['message_type'] = "success";
        header("Location: admin.php?tab=users");
        exit();
    }

    // =========================================================
    // 4. ATIVAR/DESATIVAR CONTA
    // =========================================================
    elseif ($action === 'toggle_status') {
        $newStatus = $_POST['currentStatus'] == 1 ? 0 : 1;
        // O campo correto no teu SQL é userStatus
        $stmt = $db->prepare("UPDATE User SET userStatus = ? WHERE idU = ?");
        $stmt->execute([$newStatus, $_POST['idU']]);
        
        $_SESSION['message'] = "Estado do utilizador alterado.";
        $_SESSION['message_type'] = "success";
        header("Location: admin.php?tab=users");
        exit();
    }

    // =========================================================
    // 5. DECISÃO DE SUSPENSÃO / ATIVAÇÃO
    // =========================================================
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
                    // 1. Suspender Pedido e Assignment
                    $db->prepare("UPDATE ApprovedRequest SET status = 'Suspenso' WHERE idA = ?")->execute([$req['idA']]);
                    $db->prepare("UPDATE DosimeterAssignment SET status = 'Suspenso' WHERE idA = ?")->execute([$req['idA']]);
                    
                    // 2. Registar no histórico para contar nas estatísticas de recolha
                    // Como a tabela History não tem data de fim, inserimos um registo novo a dizer "Suspenso"
                    $stmtS = $db->prepare("SELECT dosimeterSerial FROM DosimeterAssignment WHERE idA = ?");
                    $stmtS->execute([$req['idA']]);
                    $serial = $stmtS->fetchColumn();
                    
                    if($serial) {
                        $db->prepare("INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, insertDate) VALUES (?, ?, ?)")
                           ->execute([$req['idA'], $serial . ' (Suspenso)', $now]);
                    }

                } elseif ($req['requestType'] === 'Ativar') {
                    // 1. Ativar Pedido
                    $db->prepare("UPDATE ApprovedRequest SET status = 'Ativo' WHERE idA = ?")->execute([$req['idA']]);
                    // 2. Colocar em Por_Associar para o admin dar um novo dosímetro
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

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    $_SESSION['message'] = "Erro: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
    // Tenta voltar para a tab certa, senão vai para a home
    if ($action == 'associar_dosimetro') header("Location: admin.php?tab=associacao");
    elseif ($action == 'trocar_dosimetro') header("Location: admin.php?tab=gestao");
    elseif ($action == 'create_user' || $action == 'toggle_status') header("Location: admin.php?tab=users");
    else header("Location: admin.php");
    exit();
}
?>