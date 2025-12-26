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

            <div class="admin-tabs">
                <a href="physicist.php?tab=gestao" class="tab-link <?= $tab === 'gestao' ? 'active' : '' ?>">Gestão de Pedidos</a>
                <a href="physicist.php?tab=profissionais" class="tab-link <?= $tab === 'profissionais' ? 'active' : '' ?>">Profissionais Ativos</a>
                <a href="physicist.php?tab=meu_dosimetro" class="tab-link <?= $tab === 'meu_dosimetro' ? 'active' : '' ?>">O Meu Dosímetro</a>
                <a href="physicist.php?tab=historico" class="tab-link <?= $tab === 'historico' ? 'active' : '' ?>">Histórico de Dosímetros</a>
            </div>

            <div class="tab-content mt2">
                <?php 
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $subtab = isset($_GET['subtab']) ? $_GET['subtab'] : 'info'; // Para as abas de detalhe
                switch ($tab) {
                    case 'gestao':
                        if (isset($_GET['id_avaliar'])) {
                            renderReviewForm($_GET['id_avaliar']);
                        } else {
                            $pedidos = getPendingRequests($db);
                            renderPendingRequestsTable($pedidos);
                        }
                        break;

                    case 'profissionais':
                        if (isset($_GET['id_detalhe'])) {
            $idUser = $_GET['id_detalhe'];
            
            // Buscamos todos os dados necessários para as 3 abas de detalhes
            $dadosUser = getUserFullDetails($db, $idUser); 
            $historicoPedidos = getUserRequestHistory($db, $idUser);
            $historicoDosimetros = getPhysicistDosimeterHistory($db, $idUser); 
            
            // Chamamos a função de detalhes passando a $subtab (info, pedidos ou historico_dos)
            renderProfessionalDetails($dadosUser, $historicoPedidos, $historicoDosimetros, $subtab);
        } else {
            // Se não estamos a ver detalhes, mostramos a lista com suporte a PESQUISA
            $profissionais = getActiveProfessionals($db, $search); 
            renderPhysicianUserList($profissionais, $search);
        }
        break;

                    case 'meu_dosimetro':
                        $meuPedido = getMyCurrentRequest($db, $_SESSION['idU']);
                        $meuDosimetro = getPhysicistActiveDosimeters($db, $_SESSION['idU']);
                        renderMyDosimeterArea($meuPedido, $meuDosimetro);
                        break;

                    case 'historico':
        // Agora o Físico vê o histórico GLOBAL com pesquisa, igual ao Admin
        $history = getGlobalHistoryForPhysicist($db, $search);
        
        // Chamamos a função de visualização passando os dados e o termo de pesquisa
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