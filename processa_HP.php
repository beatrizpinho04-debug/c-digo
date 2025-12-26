<?php
session_start();
require_once "database/connection.php";

// 1. SEGURANÇA
if (!isset($_SESSION['idU']) || $_SESSION['userType'] !== "Profissional de Saúde") {
    header("Location: index.php");
    exit();
}

$pdo = getDatabaseConnection();
$idUsuario = $_SESSION['idU'];

// 2. RECEBER AÇÃO
$action = isset($_POST['action']) ? $_POST['action'] : '';

if (empty($action)) {
    header("Location: HP.php");
    exit();
}

try {
    // ----------------------------------------------------------------
    // CASO 1: NOVO PEDIDO
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
    // CASO 2: SUSPENDER / ATIVAR
    // ----------------------------------------------------------------
    elseif ($action === 'suspender_pedido' || $action === 'ativar_pedido') {
        
        $tipo = ($action === 'suspender_pedido') ? 'Suspender' : 'Ativar';
        $msg = 'Solicitação de ' . strtolower($tipo) . ' efetuada pelo utilizador.';
        
        // --- PASSO 1: Obter o idA (ID da Atribuição/Aprovação) atual do utilizador ---
        // Precisamos saber QUAL dosímetro/pedido estamos a suspender.
        // Vamos buscar o último pedido Aprovado (ApprovedRequest) deste utilizador.
        $sqlGetIdA = "SELECT ar.idA 
                      FROM ApprovedRequest ar
                      JOIN DosimeterRequest dr ON ar.idR = dr.idR
                      WHERE dr.idU = :idU
                      ORDER BY ar.approvalDate DESC 
                      LIMIT 1";
                      
        $stmtGet = $pdo->prepare($sqlGetIdA);
        $stmtGet->execute(['idU' => $idUsuario]);
        $result = $stmtGet->fetch(PDO::FETCH_ASSOC);

        // Se não houver nenhum pedido aprovado, não podemos criar um registo vinculado
        if (!$result || empty($result['idA'])) {
            $_SESSION['message'] = "Erro: Não foi encontrado nenhum pedido ativo para associar a esta ação.";
            $_SESSION['message_type'] = "error";
            header("Location: HP.php?tab=dashboard");
            exit();
        }

        $idA = $result['idA'];

        // --- PASSO 2: Inserir com o idA ---
        // Adicionámos o campo 'idA' na query e nos valores
        // Nota: Substituí DATETIME('now') por NOW() para maior compatibilidade, 
        // mas se for SQLite puro mantenha DATETIME('now')
        
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