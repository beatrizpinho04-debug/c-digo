<?php
session_start();
require_once "database/connection.php";
require_once "database/admin_db.php";
require_once "templates/header.php";
require_once "templates/nav.php";
require_once "templates/footer.php";
// Carregamos a nova view específica para esta página
require_once "templates/user_details_view.php"; 

// 1. Segurança: Apenas Administrador
if (!isset($_SESSION['idU']) || $_SESSION['userType'] !== "Administrador") {
    header("Location: index.php");
    exit();
}

// 2. Validar ID
if (!isset($_GET['idU'])) {
    header("Location: admin.php?tab=users");
    exit();
}

$db = getDatabaseConnection();
$idU = $_GET['idU'];
$subtab = isset($_GET['subtab']) ? $_GET['subtab'] : 'info';

// 3. Buscar Dados
$user = getUserFullDetails($db, $idU);

if (!$user) {
    echo "<div class='page-wrapper'><main class='main-container'><p>Utilizador não encontrado.</p><a href='admin.php?tab=users' class='btn btn-primary'>Voltar</a></main></div>";
    exit();
}

// 4. Buscar Dados da Aba
$tabData = [];
if ($subtab === 'pedidos') {
    $tabData = getUserRequests($db, $idU);
} elseif ($subtab === 'dosimetros') {
    $tabData = getUserDosimeterHistory($db, $idU);
} elseif ($subtab === 'suspensoes') {
    $tabData = getUserChanges($db, $idU);
}

// 5. Renderizar
$title = "Informações de " . $user['name'];
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
            // Chama a função que está no novo ficheiro de template
            renderUserDetailsPage($user, $subtab, $tabData); 
            ?>
        </main>

        <?php renderFooter(); ?>
    </div>
</body>
</html>