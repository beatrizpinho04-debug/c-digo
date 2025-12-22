<?php
session_start();
require_once "database/connection.php";
require_once "database/admin_db.php";
require_once "templates/header.php";
require_once "templates/nav.php";
require_once "templates/footer.php";
require_once "templates/admin_views.php";

if (!isset($_SESSION['idU']) || $_SESSION['userType'] !== "Administrador") {
    header("Location: index.php");
    exit();
}

$db = getDatabaseConnection();
$title = "Administração";
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'associacao';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';

header_set($title);
?>
<body>
    <div class="page-wrapper">
        <?php nav_set(); ?>

        <main class="main-container">
            <h1 class="titulo mb2">Administração</h1>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert-container <?php echo ($_SESSION['message_type'] == 'success') ? 'alert-success' : 'alert-error'; ?>">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                </div>
            <?php endif; ?>

            <?php renderAdminTabs($tab); ?>

            <?php 
            // 1. Associação de Dosímetros
            if ($tab === 'associacao') {
                $pendingList = getPendingAssociations($db, $search);
                renderAssociationTable($pendingList, $search);
                if (isset($_GET['associar'])) {
                    $userToAssociate = isset($_GET['user']) ? $_GET['user'] : 'Utilizador';
                    renderAssociateForm($_GET['associar'], $userToAssociate);
                }
            } 
            // 2. Gestão de Dosímetros 
            elseif ($tab === 'gestao') {
                $stats = getDosimeterStats($db);
                $activeList = getActiveDosimeters($db, $search);
                renderManagementTab($stats, $activeList, $search);
                
                if ($action === 'swap' && isset($_GET['idDA'])) {
                    renderSwapModal($_GET['idDA'], isset($_GET['name']) ? $_GET['name'] : '');
                }
            }
            // 3. Histórico de Dosímetros
            elseif ($tab === 'historico') {
                $history = getGlobalDosimeterHistory($db, $search);
                renderHistoryTab($history, $search);
            }
            // 4. Pedidos de Suspensão/Ativação
            elseif ($tab === 'pedidos') {
                $reqs = getPendingChangeRequests($db, $search);
                renderRequestsTab($reqs, $search);

                if (isset($_GET['decidir'])) {
                    renderDecisionModal($_GET['decidir'], $_GET['user'], $_GET['type']);
                }
            }
            // 4. Utilizadores
            elseif ($tab === 'users') {
                $users = getAllUsers($db, $search);
                renderUsersTab($users, $search);
                
                if ($action === 'create') {
                    renderCreateUserModal();
                }
            }
            ?>
        </main>

        <?php renderFooter(); ?>
    </div>
</body>
</html>