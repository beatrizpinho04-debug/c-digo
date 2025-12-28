<?php
session_start();

// Importação dos componentes base
require_once("database/connection.php");
require_once("templates/header.php");
require_once("templates/nav.php");
require_once("templates/footer.php");

// Importação dos componentes específicos do Físico
require_once("database/physicist_db.php");
require_once("templates/physicist_views.php");

// 1. Verifica se existe sessão ativa
if (!isset($_SESSION['idU'])) {
    $_SESSION['login_error'] = "Acesso negado. Por favor faça login.";
    header("Location: index.php");
    exit();
}

// 2. Verifica se o tipo de utilizador é Físico Médico
if ($_SESSION['userType'] !== "Físico Médico") {
    header("Location: index.php");
    exit();
}

$db = getDatabaseConnection();
$title = "Painel Físico Médico";

// Lógica de navegação por abas
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'gestao'; // Ajustado para a aba padrão correta
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
                        // Incluir a mini-dashboard que criámos
                        $pedidos = getPendingRequests($db);
                        if (isset($_GET['id_avaliar'])) {
                            renderReviewForm($_GET['id_avaliar']);
                        } else {
                            renderManagementDashboard($pedidos); // Adicionado aqui
                            renderPendingRequestsTable($pedidos);
                        }
                        break;

                    case 'profissionais':
                        // ... (teu código de profissionais igual) ...
                        break;

                    case 'meu_dosimetro':
                        // IMPORTANTE: Verificar se estas funções no physicist_db.php 
                        // retornam NULL caso não existam registos.
                        $meuPedido = getMyCurrentRequest($db, $_SESSION['idU']);
                        
                        // Esta função deve retornar o dosímetro APENAS se tiver serial atribuído
                        $meuDosimetro = getPhysicistActiveDosimeters($db, $_SESSION['idU']);
                        
                        $meuHistorico = getPhysicistDosimeterHistory($db, $_SESSION['idU']);
                        
                        renderMyDosimeterArea($meuPedido, $meuDosimetro, $meuHistorico);
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