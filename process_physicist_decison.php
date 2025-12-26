<?php
session_start();
require_once("database/connection.php");
// Não precisamos do physicist_db.php aqui porque vamos fazer os comandos SQL diretos para ser mais rápido
$db = getDatabaseConnection();

// Proteção: só físicos podem executar estas ações
if (!isset($_SESSION['idU']) || $_SESSION['userType'] !== "Físico Médico") {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // AÇÃO 1: Avaliar pedido de um Profissional de Saúde
    if ($action === 'evaluate_professional') {
        $idR = $_POST['idR'];
        $outcome = $_POST['outcome']; 
        
        if ($outcome === 'approve') {
            // 1. Marca como decidido
            $stmt = $db->prepare("UPDATE DosimeterRequest SET decisionMade = 1 WHERE idR = ?");
            $stmt->execute([$idR]);

            // 2. Insere na ApprovedRequest usando as tuas colunas
            $stmt = $db->prepare("INSERT INTO ApprovedRequest (idR, periodicity, riskCategory, dosimeterType, approvalDate, status) 
                                  VALUES (?, ?, ?, ?, DATE('now'), 'Ativo')");
            $stmt->execute([$idR, $_POST['periodicity'], $_POST['riskCategory'], $_POST['dosimeterType']]);
        } else {
            // Rejeitar: Apenas marca como decidido (podes adicionar uma tabela de rejeitados se quiseres)
            $stmt = $db->prepare("UPDATE DosimeterRequest SET decisionMade = 1 WHERE idR = ?");
            $stmt->execute([$idR]);
        }
    }

    // AÇÃO 2: Criar pedido automático para o próprio Físico (idU e pratica)
    elseif ($action === 'auto_request') {
        $idU = $_SESSION['idU'];
        $pratica = $_POST['pratica']; // Nome da tua coluna
        $periodicity = $_POST['periodicity'];

        try {
            $db->beginTransaction();
            // Insere pedido já decidido
            $stmt = $db->prepare("INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (?, ?, DATE('now'), 1)");
            $stmt->execute([$idU, $pratica]);
            $idR = $db->lastInsertId();

            // Aprovação imediata
            $stmt = $db->prepare("INSERT INTO ApprovedRequest (idR, periodicity, riskCategory, dosimeterType, approvalDate, status) 
                                  VALUES (?, ?, 'Categoria A', 'Corpo Inteiro', DATE('now'), 'Ativo')");
            $stmt->execute([$idR, $periodicity]);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['message'] = "Erro ao processar auto-pedido.";
            $_SESSION['message_type'] = "error";
            header("Location: physicist.php");
            exit();
        }
    }

    // AÇÃO 3: Pedir suspensão/reativação (idUser e requestType)
    elseif ($action === 'request_change') {
        $requestType = $_POST['type']; // 'suspensao' ou 'reativacao'
        $message = $_POST['message'];
        $idUser = $_SESSION['idU'];

        $stmt = $db->prepare("INSERT INTO ChangeRecord (idUser, requestType, message, requestDate, status) 
                              VALUES (?, ?, ?, DATE('now'), 'Pendente')");
        $stmt->execute([$idUser, $requestType, $message]);
    }

    // Redireciona com a tua lógica de mensagens
    $_SESSION['message'] = "Ação realizada com sucesso!";
    $_SESSION['message_type'] = "success";
    header("Location: physicist.php");
    exit();
}