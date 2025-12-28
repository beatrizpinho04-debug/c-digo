<?php
session_start();
require_once("database/connection.php");
$db = getDatabaseConnection();

// Proteção: só físicos podem executar estas ações
if (!isset($_SESSION['idU']) || $_SESSION['userType'] !== "Físico Médico") {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // AÇÃO 1: Avaliar pedido de um Profissional de Saúde (Já tinhas e está correto)
    if ($action === 'evaluate_professional') {
        $idR = $_POST['idR'];
        $outcome = $_POST['outcome']; 
        
        if ($outcome === 'approve') {
            $stmt = $db->prepare("UPDATE DosimeterRequest SET decisionMade = 1 WHERE idR = ?");
            $stmt->execute([$idR]);

            $stmt = $db->prepare("INSERT INTO ApprovedRequest (idR, periodicity, riskCategory, dosimeterType, approvalDate, status) 
                                  VALUES (?, ?, ?, ?, DATE('now'), 'Ativo')");
            $stmt->execute([$idR, $_POST['periodicity'], $_POST['riskCategory'], $_POST['dosimeterType']]);
        } else {
            $stmt = $db->prepare("UPDATE DosimeterRequest SET decisionMade = 1 WHERE idR = ?");
            $stmt->execute([$idR]);
        }
    }

    // AÇÃO 2: Criar pedido automático para o próprio Físico
    elseif ($action === 'auto_request') {
        $idU = $_SESSION['idU'];
        $pratica = $_POST['pratica']; 
        $periodicity = $_POST['periodicity']; // Vem do <select> fechado

        try {
            $db->beginTransaction();
            // 1. Insere pedido já marcado como decidido
            $stmt = $db->prepare("INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (?, ?, DATE('now'), 1)");
            $stmt->execute([$idU, $pratica]);
            $idR = $db->lastInsertId();

            // 2. Aprovação imediata (Físico Médico tem autoridade)
            // Definimos valores padrão para Categoria e Tipo, que o Admin pode ajustar depois se necessário
            $stmt = $db->prepare("INSERT INTO ApprovedRequest (idR, periodicity, riskCategory, dosimeterType, approvalDate, status) 
                                  VALUES (?, ?, 'Categoria A', 'Corpo Inteiro', DATE('now'), 'Ativo')");
            $stmt->execute([$idR, $periodicity]);
            
            $db->commit();
            $_SESSION['message'] = "Dosímetro ativado com sucesso! O Administrador irá atribuir o número de série brevemente.";
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['message'] = "Erro ao processar o seu pedido automático.";
            $_SESSION['message_type'] = "error";
            header("Location: physicist.php?tab=meu_dosimetro");
            exit();
        }
    }

    // AÇÃO 3: Pedir suspensão/reativação (Com Justificação Aberta)
    elseif ($action === 'request_change') {
        $typeRaw = $_POST['type']; // 'suspensao' ou 'reativacao'
        $message = $_POST['message']; // A caixa de texto aberta
        $idUser = $_SESSION['idU'];

        // Normalizamos o nome para o Administrador ver bonito (Suspensão ou Reativação)
        $requestType = ($typeRaw === 'suspensao') ? 'Suspensão' : 'Reativação';

        try {
            $stmt = $db->prepare("INSERT INTO ChangeRecord (idUser, requestType, message, requestDate, status) 
                                  VALUES (?, ?, ?, DATE('now'), 'Pendente')");
            $stmt->execute([$idUser, $requestType, $message]);
            $_SESSION['message'] = "Pedido de $requestType enviado para análise do Administrador.";
        } catch (Exception $e) {
            $_SESSION['message'] = "Erro ao enviar pedido de alteração.";
            $_SESSION['message_type'] = "error";
        }
    }

    // Redirecionamento final
if (!isset($_SESSION['message_type'])) {
        $_SESSION['message_type'] = "success";
    }

    // Se a ação foi avaliar um profissional, volta para a gestão. 
    // Caso contrário, vai para "o meu dosímetro".
    $targetTab = ($action === 'evaluate_professional') ? 'gestao' : 'meu_dosimetro';
    
    header("Location: physicist.php?tab=" . $targetTab);
    exit();
}