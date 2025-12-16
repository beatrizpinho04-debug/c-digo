<?php
// processa_admin.php
session_start();
require_once 'database/connection.php';

// Segurança: Apenas Administradores
if (!isset($_SESSION['idU']) || $_SESSION['userType'] !== "Administrador") {
    header("Location: index.php");
    exit();
}

$db = getDatabaseConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- AÇÃO: ASSOCIAR DOSÍMETRO ---
    if (isset($_POST['action']) && $_POST['action'] === 'associar_dosimetro') {
        
        $idDA = $_POST['idDA'];
        $serial = trim($_POST['serial']);
        // Receber notas (pode estar vazio)
        $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
        
        if (empty($serial)) {
            $_SESSION['message'] = "Erro: Número de série é obrigatório.";
            $_SESSION['message_type'] = "error";
            header("Location: admin.php?tab=associacao");
            exit();
        }

        try {
            $db->beginTransaction();

            // 1. Descobrir a periodicidade para calcular a próxima troca
            $stmtInfo = $db->prepare("
                SELECT AR.periodicity 
                FROM DosimeterAssignment DA 
                JOIN ApprovedRequest AR ON DA.idA = AR.idA 
                WHERE DA.idDA = ?
            ");
            $stmtInfo->execute([$idDA]);
            $info = $stmtInfo->fetch();

            if (!$info) throw new Exception("Atribuição não encontrada.");

            // Calcular datas
            $dataHoje = date('Y-m-d');
            $dataProxima = date('Y-m-d'); 

            if ($info['periodicity'] === 'Mensal') {
                $dataProxima = date('Y-m-d', strtotime("+1 month"));
            } elseif ($info['periodicity'] === 'Trimestral') {
                $dataProxima = date('Y-m-d', strtotime("+3 months"));
            }

            // 2. Atualizar DosimeterAssignment (Incluindo NOTES)
            $stmtUpd = $db->prepare("
                UPDATE DosimeterAssignment 
                SET dosimeterSerial = ?, 
                    assignmentDate = ?, 
                    nextReplacementDate = ?, 
                    status = 'Em_Uso',
                    notes = ? 
                WHERE idDA = ?
            ");
            // Adicionado $notes aos parâmetros
            $stmtUpd->execute([$serial, $dataHoje, $dataProxima, $notes, $idDA]);

            // 3. Inserir no Histórico
            $stmtIdA = $db->prepare("SELECT idA FROM DosimeterAssignment WHERE idDA = ?");
            $stmtIdA->execute([$idDA]);
            $resIdA = $stmtIdA->fetch();

            $stmtHist = $db->prepare("
                INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, insertDate)
                VALUES (?, ?, ?)
            ");
            $stmtHist->execute([$resIdA['idA'], $serial, date('Y-m-d H:i:s')]);

            $db->commit();

            $_SESSION['message'] = "Dosímetro associado com sucesso!";
            $_SESSION['message_type'] = "success";

        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['message'] = "Erro ao associar: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }

        header("Location: admin.php?tab=associacao");
        exit();
    }
}
?>