<?php
session_start();

require_once("database/connection.php");
require_once("templates/header.php");
require_once("templates/nav.php");
require_once("templates/footer.php");

require_once("database/physicist_db.php");
require_once("templates/physicist_views.php");

if (!isset($_SESSION['idU'])) {
    $_SESSION['login_error'] = "Acesso negado. Por favor faça login.";
    header("Location: index.php");
    exit();
}

if ($_SESSION['userType'] !== "Físico Médico") {
    header("Location: index.php");
    exit();
}

$db = getDatabaseConnection();
$title = "Painel Físico Médico";
$meuIdU = $_SESSION['idU'];

$stmtChange = $db->prepare("SELECT idCR FROM ChangeRecord WHERE idUser = ? AND status = 'Pendente' LIMIT 1");
$stmtChange->execute([$meuIdU]);
$temPedidoPendente = $stmtChange->fetch() ? true : false;

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'gestao';
?>
<?php header_set($title); ?>

<body>
    <div class="page-wrapper">
        <?php nav_set(); ?>

        <main class="main-container">
            <h1 class="titulo mb2">Painel de Controlo – Física Médica</h1>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert-container <?= $_SESSION['message_type'] === 'success' ? 'alert-success' : 'alert-error' ?>">
                    <span><?= $_SESSION['message']; ?></span>
                </div>
                <?php 
                unset($_SESSION['message']); 
                unset($_SESSION['message_type']); 
                ?>
            <?php endif; ?>

            <div class="admin-tabs">
                <a href="physicist.php?tab=gestao" class="tab-link <?= $tab === 'gestao' ? 'active' : '' ?>">Gestão de Pedidos</a>
                <a href="physicist.php?tab=profissionais" class="tab-link <?= $tab === 'profissionais' ? 'active' : '' ?>">Profissionais Ativos</a>
                <a href="physicist.php?tab=meu_dosimetro" class="tab-link <?= $tab === 'meu_dosimetro' ? 'active' : '' ?>">O Meu Dosímetro</a>
                </div>

            <div class="tab-content mt2">
                <?php 
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $subtab = isset($_GET['subtab']) ? $_GET['subtab'] : 'info'; 

                switch ($tab) {
                    case 'gestao':
                        $pedidos = getPendingRequests($db);
                        if (isset($_GET['id_avaliar'])) {
                            renderReviewForm($_GET['id_avaliar']);
                        } else {
                            renderManagementDashboard($pedidos);
                            renderPendingRequestsTable($pedidos);
                        }
                        break;

                    case 'profissionais':
                        if (isset($_GET['id_detalhe'])) {
                            $idUser = $_GET['id_detalhe'];
                            $dadosUser = getUserFullDetails($db, $idUser); 
                            $historicoPedidos = getUserRequestHistory($db, $idUser);
                            $historicoDosimetros = getPhysicistDosimeterHistory($db, $idUser); 
                            renderProfessionalDetails($dadosUser, $historicoPedidos, $historicoDosimetros, $subtab);
                        } else {
                            $profissionais = getActiveProfessionals($db, $search); 
                            renderPhysicianUserList($profissionais, $search);
                        }
                        break;

                    case 'meu_dosimetro':
                        $meuPedido = getMyCurrentRequest($db, $_SESSION['idU']);
                        
                        $meuDosimetro = getPhysicistActiveDosimeters($db, $_SESSION['idU']);
                        
                        $meuHistorico = getPhysicistDosimeterHistory($db, $_SESSION['idU']);
                        
                        renderMyDosimeterArea($meuPedido, $meuDosimetro, $meuHistorico,$temPedidoPendente);
                        break;

                    case 'historico':
                        $history = getGlobalHistoryForPhysicist($db, $search);
                        renderGlobalHistoryTable($history, $search); 
                        break;
                }
                ?>
            </div>
        </main>

        <?php renderFooter(); ?>
    </div>
</body>
</html>