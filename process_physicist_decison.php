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
    
    // 1. Atualiza sempre que a decisão foi tomada
    $stmt = $db->prepare("UPDATE DosimeterRequest SET decisionMade = 1 WHERE idR = ?");
    $stmt->execute([$idR]);

    if ($outcome === 'approve') {
        // 2. Insere na ApprovedRequest com os dados do formulário
        $stmt = $db->prepare("INSERT INTO ApprovedRequest (idR, periodicity, riskCategory, dosimeterType, approvalDate, status) 
                              VALUES (?, ?, ?, ?, DATE('now'), 'Ativo')");
        
        // ORDEM: idR, periodicity, riskCategory, dosimeterType
        $stmt->execute([
            $idR, 
            $_POST['periodicity'], 
            $_POST['riskCategory'], 
            $_POST['dosimeterType']
        ]);
        $_SESSION['message'] = "Pedido aprovado com sucesso!";
    } else {
        $_SESSION['message'] = "Pedido rejeitado.";
    }
}

    // AÇÃO 2: Criar pedido automático para o próprio Físico
    elseif ($action === 'auto_request') {
        $idU = $_SESSION['idU'];
        $pratica = $_POST['pratica'] ?? 'Geral'; 
        $periodicity = $_POST['periodicity'] ?? 'Mensal';
        $riskCategory = $_POST['riskCategory'] ?? 'Categoria B';
        $dosimeterType = $_POST['dosimeterType'] ?? 'Corpo Inteiro';

        try {
            // Passo 1: Criar o registo na DosimeterRequest
            $stmt1 = $db->prepare("INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (?, ?, DATE('now'), 1)");
            $stmt1->execute([$idU, $pratica]);
            
            // Obter o ID gerado
            $idR = $db->lastInsertId();

            // Passo 2: Criar o registo na ApprovedRequest
            $stmt2 = $db->prepare("INSERT INTO ApprovedRequest (idR, periodicity, riskCategory, dosimeterType, approvalDate, status) 
                                  VALUES (?, ?, ?, ?, DATE('now'), 'Ativo')");
            
            $stmt2->execute([$idR, $periodicity, $riskCategory, $dosimeterType]);
            
            $_SESSION['message'] = "Dosímetro solicitado com sucesso!";
            $_SESSION['message_type'] = "success";

        } catch (PDOException $e) {
            // Se der erro aqui, vamos imprimir no ecrã para veres
            die("Erro no SQL: " . $e->getMessage());
        }
        
        header("Location: physicist.php?tab=meu_dosimetro");
        exit();
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