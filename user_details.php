<?php
session_start();
require_once "database/connection.php";
require_once "database/user_details_db.php";
require_once "templates/header.php";
require_once "templates/nav.php";
require_once "templates/footer.php";
require_once "templates/user_details_view.php";

if (!isset($_SESSION['idU']) || $_SESSION['userType'] !== "Administrador") {
    header("Location: index.php"); exit();
}

$idU = isset($_GET['idU']) ? $_GET['idU'] : 0;
$db = getDatabaseConnection();
$user = getUserFullDetails($db, $idU);

if (!$user) {
    echo "<div class='page-wrapper'><main class='main-container'><p>Utilizador não encontrado.</p></main></div>";
    exit();
}

// Define a aba ativa (default: info)
$subtab = isset($_GET['subtab']) ? $_GET['subtab'] : 'info';

$title = "Detalhes: " . $user['name']. " " . $user['surname'];
header_set($title);
?>
<body>
    <div class="page-wrapper">
        <?php nav_set(); ?>

        <main class="main-container">
            <div class="profile-header-flex mb1_5">
                <div class="user-area">
                    <a href="admin.php?tab=users" class="btn voltar" title="Voltar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12 19-7-7 7-7"></path>
                            <path d="M19 12H5"></path>
                        </svg>
                    </a>
                </div>
            </div>
            
            <?php 
            // 1. Renderizar Cabeçalho do User
            renderUserHeaderCard($user);

            // 2. Renderizar Abas de Navegação
            renderUserTabs($idU, $subtab);
            ?>

            <div class="card">
                <?php 
                if ($subtab === 'info') {
                    // Passamos o user que já temos
                    renderUserInfoTab($user);
                } 
                elseif ($subtab === 'pedidos') {
                    $requests = getUserRequests($db, $idU);
                    renderUserRequestsTab($requests);
                } 
                elseif ($subtab === 'dosimetros') {
                    $history = getUserDosimeterHistory($db, $idU);
                    renderUserDosimetersTab($history);
                } 
                elseif ($subtab === 'suspensoes') {
                    $changes = getUserChanges($db, $idU);
                    renderUserSuspensionsTab($changes);
                }
                ?>
            </div>

        </main>

        <?php renderFooter(); ?>
    </div>
</body>
</html>