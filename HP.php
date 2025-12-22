<?php
session_start();


require_once "database/connection.php"; 
$pdo = getDatabaseConnection(); 

// ====================================================================
// 1. LÓGICA DE FORMULÁRIO (Novo Pedido)
// ====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'novo_pedido') {
    if (!isset($_SESSION['idU'])) die("Erro: Sessão não iniciada.");
    
    $idUsuario = $_SESSION['idU'];
    $pratica = isset($_POST['pratica']) ? trim($_POST['pratica']) : '';
    
    if (!empty($pratica)) {
        try {
            $sqlInsert = "INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) 
                          VALUES (:id, :pratica, DATETIME('now'), 0)";
            $stmt = $pdo->prepare($sqlInsert);
            $stmt->execute(['id' => $idUsuario, 'pratica' => $pratica]);
            
            // Redireciona para a mesma página
            header("Location: ?tab=dashboard");
            exit();
        } catch (PDOException $e) {
            die("Erro ao gravar: " . $e->getMessage());
        }
    }
}

// ====================================================================
// 2. INCLUDES GERAIS
// ====================================================================
require_once "templates/header.php";
require_once "templates/nav.php";

// SEGURANÇA
if (!isset($_SESSION['idU']) || $_SESSION['userType'] !== "Profissional de Saúde") {
    header("Location: index.php"); 
    exit();
}

$idUsuario = $_SESSION['idU'];
$title = "Profissional de Saúde";
$currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';

// Função Helper de Cores
function obterClasseEstado($estado) {
    switch (strtolower(trim($estado))) {
        case 'ativo': return 'badge-green';
        case 'em uso': case 'aprovado': return 'badge-blue';
        case 'rejeitado': return 'badge-red';
        case 'pendente': return 'badge-yellow';
        default: return 'badge-gray';
    }
}

header_set($title); 
?>

<body>
<div class="page-wrapper">
    <?php nav_set(); ?>

    <main class="main-container">
        
        <h1 class="titulo mb2">Área do Profissional de Saúde</h1>

        <div class="tab-nav">
            <a href="?tab=dashboard" class="tab-link <?php echo $currentTab == 'dashboard' ? 'active' : ''; ?>">Página Inicial</a>
            <a href="?tab=pedidos" class="tab-link <?php echo $currentTab == 'pedidos' ? 'active' : ''; ?>">Todos os Pedidos</a>
            <a href="?tab=historico" class="tab-link <?php echo $currentTab == 'historico' ? 'active' : ''; ?>">Histórico de Dosímetros</a>
            <a href="?tab=alteracoes" class="tab-link <?php echo $currentTab == 'alteracoes' ? 'active' : ''; ?>">Alterações no Pedido</a>
        </div>

        <?php
        // INCLUDES DAS TABS
        switch ($currentTab) {
            case 'pedidos':
                require_once "HP_tab2.php"; 
                break;
            case 'historico':
                require_once "HP_tab3.php"; 
                break;
            case 'alteracoes':
                require_once "HP_tab4.php"; 
                break;
            case 'dashboard':
            default:
                require_once "HP_tab1.php"; 
                break;
        }
        ?>

    </main>
    <?php require_once("templates/footer.php"); ?>
</div>

<script>
    function abrirModal() {
        var m = document.getElementById('modalPedido');
        if(m) m.style.display = 'flex';
    }
    function fecharModal() {
        var m = document.getElementById('modalPedido');
        if(m) m.style.display = 'none';
    }
    window.onclick = function(event) {
        var modal = document.getElementById('modalPedido');
        if (event.target == modal) modal.style.display = "none";
    }
</script>
</body>
</html>