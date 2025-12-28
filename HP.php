<?php
session_start();
require_once "database/connection.php";
require_once "database/hp_db.php";
require_once "templates/header.php";
require_once "templates/nav.php";
require_once "templates/footer.php";
require_once "templates/hp_views.php"; 

if (!isset($_SESSION['idU']) || $_SESSION['userType'] !== "Profissional de Saúde") {
    header("Location: index.php");
    exit();
}

$db = getDatabaseConnection();
$title = "Profissional de Saúde";
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';

$modalType = isset($_GET['modal']) ? $_GET['modal'] : null;

$idUsuario = $_SESSION['idU'];

header_set($title);
?>
<body>
    <div class="page-wrapper">
        <?php nav_set(); ?>

        <main class="main-container">
            <h1 class="titulo mb2">Área do Profissional de Saúde</h1>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert-container <?php echo ($_SESSION['message_type'] == 'success') ? 'alert-success' : 'alert-error'; ?>">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                </div>
            <?php endif; ?>

            <?php renderHPTabs($tab); ?>

            <?php 
          
            
            if ($tab === 'dashboard') {

                $hp = getHPProfile($db, $idUsuario);
                $last = getLastRequest($db, $idUsuario);
                $temPedidoPendente = checkPendingChange($db, $idUsuario);
      
                renderDashboard($hp, $last, $temPedidoPendente);

                $profModal = $hp['profession'] ?? 'N/D';
                $depModal = $hp['department'] ?? 'N/D';
                
                renderRequestModal($profModal, $depModal, ($modalType === 'abrir'));

                if ($modalType === 'suspender' || $modalType === 'ativar') {
                    renderSuspensionModal($modalType);
                }
            } 
            elseif ($tab === 'historico') {
                $history = getDosimeterHistory($db, $idUsuario);
                $top = getLastRequest($db, $idUsuario); 
                renderHistoryTab($history, $top);
            }
            elseif ($tab === 'alteracoes') {
                $reqs = getChangeHistory($db, $idUsuario);
                renderChangesTab($reqs);
            }
            ?>
        </main>

        <?php require_once("templates/footer.php"); ?>
    </div>
</body>
</html>