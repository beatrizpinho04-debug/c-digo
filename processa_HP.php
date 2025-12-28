<?php
session_start();
require_once "database/connection.php";


if (!isset($_SESSION['idU']) || $_SESSION['userType'] !== "Profissional de Saúde") {
    header("Location: index.php");
    exit();
}

$pdo = getDatabaseConnection();
$idUsuario = $_SESSION['idU'];


$action = isset($_POST['action']) ? $_POST['action'] : '';

if (empty($action)) {
    header("Location: HP.php");
    exit();
}

try {
    // ----------------------------------------------------------------
    //                       NOVO PEDIDO
    // ----------------------------------------------------------------
    if ($action === 'novo_pedido') {
        $pratica = trim($_POST['pratica'] ?? '');
        
        if (empty($pratica)) {
            $_SESSION['message'] = "Erro: O campo Prática é obrigatório.";
            $_SESSION['message_type'] = "error";
            header("Location: HP.php?tab=dashboard&modal=abrir");
            exit();
        }

        $sql = "INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) 
                VALUES (:id, :pratica, DATETIME('now'), 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $idUsuario, 'pratica' => $pratica]);

        $_SESSION['message'] = "Pedido submetido com sucesso!";
        $_SESSION['message_type'] = "success";
        header("Location: HP.php?tab=dashboard");
        exit();
    }

    // ----------------------------------------------------------------
    //                       SUSPENDER / ATIVAR
    // ----------------------------------------------------------------
    elseif ($action === 'suspender_pedido' || $action === 'ativar_pedido') {
    
        require_once "database/hp_db.php"; 

        if (checkPendingChange($pdo, $idUsuario)) {
            $_SESSION['message'] = "Erro: Já tem um pedido pendente. Aguarde a decisão.";
            $_SESSION['message_type'] = "error";
            header("Location: HP.php?tab=dashboard");
            exit();
        }

        $tipo = ($action === 'suspender_pedido') ? 'Suspender' : 'Ativar';

        $motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';

        if (empty($motivo)) {
            $_SESSION['message'] = "Erro: A justificação é obrigatória. Por favor escreva o motivo.";
            $_SESSION['message_type'] = "error";
 
            $modalParam = ($action === 'suspender_pedido') ? 'suspender' : 'ativar';
            header("Location: HP.php?tab=dashboard&modal=" . $modalParam);
            exit(); 
        }

        $msg = $motivo; 
   
        $sqlGetIdA = "SELECT ar.idA 
                      FROM ApprovedRequest ar
                      JOIN DosimeterRequest dr ON ar.idR = dr.idR
                      WHERE dr.idU = :idU
                      ORDER BY ar.approvalDate DESC 
                      LIMIT 1";
                      
        $stmtGet = $pdo->prepare($sqlGetIdA);
        $stmtGet->execute(['idU' => $idUsuario]);
        $result = $stmtGet->fetch(PDO::FETCH_ASSOC);

        if (!$result || empty($result['idA'])) {
            $_SESSION['message'] = "Erro: Não foi encontrado nenhum pedido ativo para associar a esta ação.";
            $_SESSION['message_type'] = "error";
            header("Location: HP.php?tab=dashboard");
            exit();
        }

        $idA = $result['idA'];

        $sql = "INSERT INTO ChangeRecord (idUser, idA, requestType, message, requestDate, status) 
                VALUES (:id, :idA, :tipo, :msg, DATETIME('now'), 'Pendente')";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id'   => $idUsuario, 
            'idA'  => $idA,          
            'tipo' => $tipo, 
            'msg'  => $msg
        ]);

        $_SESSION['message'] = "Pedido de $tipo registado com sucesso!";
        $_SESSION['message_type'] = "success";
        header("Location: HP.php?tab=dashboard");
        exit();
    } 

} catch (Exception $e) {
    $_SESSION['message'] = "Erro: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
    header("Location: HP.php");
    exit();
}
?>