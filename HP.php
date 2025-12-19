<?php
session_start();
require_once("templates/header.php");
require_once("templates/nav.php");
require_once("templates/footer.php");

//Verifica se existe sessão ativa
if (!isset($_SESSION['idU'])) {
    // Se não houver sessão, manda para o login com um erro
    $_SESSION['login_error'] = "Acesso negado. Por favor faça login.";
    header("Location: index.php");
    exit();
}
//Verifica se o tipo de utilizador é Profissional de Saúde
if ($_SESSION['userType'] !== "Profissional de Saúde") {
    header("Location: index.php");
    exit();
}
//Título da Página
$title = "Profissional de Saúde";

// ================== DADOS TEMPORÁRIOS (MOCK) ==================

// Pedido atual (null = sem pedido)
$currentRequest = [
    'estado' => 'Aprovado',
    'data_submissao' => '2025-03-12',
    'servico' => 'Radiologia',
    'funcao' => 'Técnico de Radiologia',
    'observacoes' => 'Trabalho com equipamentos de imagem'
];

// Histórico de pedidos
$requestHistory = [
    ['id' => 1, 'data_submissao' => '2024-09-10', 'estado' => 'Rejeitado'],
    ['id' => 2, 'data_submissao' => '2025-03-12', 'estado' => 'Aprovado']
];

// Histórico de dosímetros
$dosimeterHistory = [
    ['codigo' => 'DOS-001', 'data_inicio' => '2025-03-20', 'data_fim' => null],
    ['codigo' => 'DOS-145', 'data_inicio' => '2024-09-15', 'data_fim' => '2025-03-19']
];

// Histórico ativo / suspenso
$statusHistory = [
    ['estado' => 'Ativo', 'data' => '2025-03-20'],
    ['estado' => 'Suspenso', 'data' => '2024-12-01'],
    ['estado' => 'Ativo', 'data' => '2024-09-15']
];


?>
<?php header_set(); ?>

<body>
<div class="page-wrapper">

    <?php nav_set(); ?>

    <main class="main-container">

    <h1 class="titulo mb2">Área do Profissional de Saúde</h1>

    <!-- ESTADO DO PEDIDO -->
    <div class="card1 mb2">
    <div class="card1-header">
        <h2 class="card1-title">Dados do Pedido</h2>
    </div>
    <div class="card1-content">
        <div class="info-grid">
            <div><strong>Serviço:</strong> Radiologia</div>
            <div><strong>Função:</strong> Técnico de Radiologia</div>
            <div class="info-full">
                <strong>Observações:</strong> Trabalho com equipamentos de imagem
            </div>
        </div>
    </div>



    <!-- DOSÍMETRO -->
   <div class="card1 mb2">
    <div class="card1-header">
        <h2 class="card1-title">Histórico de Dosímetros</h2>
    </div>
    <div class="card1-content">
        <div class="list-item">
            <span class="nome-tab">DOS-001</span>
            <span>Desde 2025-03-20</span>
            <span class="badge-blue etiqueta">Atual</span>
        </div>

        <div class="list-item">
            <span class="nome-tab">DOS-145</span>
            <span>2024-09-15 → 2025-03-19</span>
        </div>
    </div>



    <!-- HISTÓRICOS -->
        <div class="card1 mb2">
        <div class="card1-header">
            <h2 class="card1-title">Histórico de Pedidos</h2>
        </div>
        <div class="card1-content">
            <div class="list-item">
                <span>2024-09-10</span>
                <span class="badge-red etiqueta">Rejeitado</span>
                <a href="#" class="text-primary">Ver detalhes</a>
            </div>

            <div class="list-item">
                <span>2025-03-12</span>
                <span class="badge-blue etiqueta">Aprovado</span>
                <a href="#" class="text-primary">Ver detalhes</a>
            </div>
        </div>



    </main>


    <?php renderFooter(); ?>

</div>
</body>
</html>