<?php
session_start();
require_once("database/connection.php");
$db = getDatabaseConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $meuIdU = $_SESSION['idU'];

    try {
        if ($action === 'evaluate_professional') {
            $idR = $_POST['idR'];
            $outcome = $_POST['outcome'];
            $comment = trim($_POST['comment'] ?? '');

            if ($outcome === 'reject' && empty($comment)) {
                $_SESSION['message'] = "Erro: Tem de deixar um comentário para poder rejeitar o pedido!";
                $_SESSION['message_type'] = "error";
                header("Location: physicist.php?tab=gestao&id_avaliar=" . $idR);
                exit();
            }
            
            $riskCategory = $_POST['riskCategory'] ?? '';
            if (strpos($riskCategory, 'A') !== false) $riskCategory = 'A';
            elseif (strpos($riskCategory, 'B') !== false) $riskCategory = 'B';

            $stmt = $db->prepare("UPDATE DosimeterRequest SET decisionMade = 1 WHERE idR = ?");
            $stmt->execute([$idR]);

            if ($outcome === 'approve') {
                $stmt = $db->prepare("INSERT INTO ApprovedRequest (idR, idP, periodicity, riskCategory, dosimeterType, approvalDate, status) 
                                      VALUES (?, ?, ?, ?, ?, DATE('now'), 'Ativo')");
                $stmt->execute([
                    $idR, 
                    $meuIdU, 
                    $_POST['periodicity'], 
                    $riskCategory, 
                    $_POST['dosimeterType']
                ]);
                $_SESSION['message'] = "Pedido aprovado com sucesso!";
            } else {
                $_SESSION['message'] = "Pedido rejeitado.";
            }
        } 
        
        elseif ($action === 'auto_request') {
            $riskCategory = $_POST['riskCategory'] ?? '';
            if (strpos($riskCategory, 'A') !== false) $riskCategory = 'A';
            elseif (strpos($riskCategory, 'B') !== false) $riskCategory = 'B';

            $stmt1 = $db->prepare("INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (?, ?, DATE('now'), 1)");
            $stmt1->execute([$meuIdU, $_POST['pratica']]);
            $idR = $db->lastInsertId();

            $stmt2 = $db->prepare("INSERT INTO ApprovedRequest (idR, idP, periodicity, riskCategory, dosimeterType, approvalDate, status) 
                                  VALUES (?, ?, ?, ?, ?, DATE('now'), 'Ativo')");
            $stmt2->execute([
                $idR, 
                $meuIdU, 
                $_POST['periodicity'], 
                $riskCategory, 
                $_POST['dosimeterType']
            ]);
            $_SESSION['message'] = "O seu dosímetro foi ativado!";
        }

        elseif ($action === 'request_change') {
            $typeRaw = $_POST['type'];
            
            $requestType = ($typeRaw === 'suspensao') ? 'Suspender' : 'Ativar';

            $stmtA = $db->prepare("
                SELECT ar.idA 
                FROM ApprovedRequest ar
                JOIN DosimeterRequest dr ON ar.idR = dr.idR
                WHERE dr.idU = ? AND ar.status = 'Ativo'
                LIMIT 1
            ");
            $stmtA->execute([$meuIdU]);
            $rowA = $stmtA->fetch();
            $idA_do_dosimetro = $rowA['idA'] ?? null;

            if (!$idA_do_dosimetro) {
                throw new Exception("Não foi encontrado um dosímetro ativo para realizar esta alteração.");
            }
            
            $stmt = $db->prepare("
                INSERT INTO ChangeRecord (idUser, idA, requestType, message, requestDate, status) 
                VALUES (?, ?, ?, ?, DATE('now'), 'Pendente')
            ");
            
            $stmt->execute([
                $meuIdU, 
                $idA_do_dosimetro, 
                $requestType, 
                $_POST['message']
            ]);
            
            $_SESSION['message'] = "Pedido de $requestType enviado com sucesso para análise.";
        
        }

        if (!isset($_SESSION['message_type'])) {
            $_SESSION['message_type'] = "success";
        }

    } catch (Exception $e) {
        $_SESSION['message'] = "Erro: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }

    $target = ($action === 'evaluate_professional') ? 'gestao' : 'meu_dosimetro';
    header("Location: physicist.php?tab=" . $target);
    exit();
}