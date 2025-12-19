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

?>
<?php header_set(); ?>

<body>
<div class="page-wrapper">

    <?php nav_set(); ?>

    <main class="main-container health-page">

        <!-- ESTADO DO PEDIDO -->
        <div class="card card-stat-primary mb2">
            <h2 class="titulo-separador">Estado do pedido</h2>

            <p class="nome mb05">Pedido ativo</p>
            <span class="etiqueta badge-purple mb1">Dosímetro associado</span>

            <p class="subtítulo">
                Pedido aprovado em: <strong>12/03/2025</strong>
            </p>

            <div class="profile-actions">
                <button class="btn btn-cancel">Pedir suspensão</button>
            </div>
        </div>

        <!-- DADOS DO PEDIDO -->
        <div class="card1">
            <div class="card1-header">
            <h3 class="card1-title">Dados do pedido</h3>
            <p class="subtítulo">Informação associada ao pedido</p>
        </div>
            <div class="card1-content">
            <p><strong>Departamento:</strong> Técnico de Radiologia</p>
            <p><strong>N.º identicação profissional:</strong> 123456</p>
         <p><strong>Data de início:</strong> 01/04/2025</p>
        </div>
</div>


        <!-- DOSÍMETRO -->
        <div class="card1 card-stat-green">
            <div class="card1-header">
                <h3 class="card1-title">Dosímetro atual</h3>
            </div>
            <div class="card1-content">
                <p><strong>Código:</strong> D-45892</p>
                <p><strong>Associado em:</strong> 20/11/2025</p>
                <p><strong>Próxima troca:</strong> 20/12/2025</p>
            </div>
        </div>

        <!-- HISTÓRICO -->
        <div class="card1">
            <div class="card1-header">
                <h3 class="card1-title">Histórico</h3>
            </div>
            <div class="card1-content">
                <ul class="subtítulo">
                    <li>01/02/2025 – Pedido submetido</li>
                    <li>12/03/2025 – Pedido aprovado</li>
                    <li>20/11/2025 – Dosímetro associado</li>
                </ul>
            </div>
        </div>

    </main>

    <?php renderFooter(); ?>

</div>
</body>
</html>