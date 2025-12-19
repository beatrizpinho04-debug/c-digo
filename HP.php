<?php
session_start();
require_once("templates/header.php");
require_once("templates/nav.php");
require_once("templates/footer.php");

//Verifica se existe sessão ativa
if (!isset($_SESSION['idU'])) {
    $_SESSION['login_error'] = "Acesso negado. Por favor faça login.";
    header("Location: index.php");
    exit();
}
//Verifica se é Profissional de Saúde
if ($_SESSION['userType'] !== "Profissional de Saúde") {
    header("Location: index.php");
    exit();
}

$title = "Profissional de Saúde";
header_set(); 

?>

<body>
<div class="page-wrapper">

    <?php nav_set(); ?>

    <main class="main-container">

        <h1 class="titulo mb2">Área do Profissional de Saúde</h1>

        <div class="card mb2">
            <h2 class="nome mb1">O Meu Pedido Atual</h2>
            
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div class="info-grid" style="flex: 1;">
                    <div><span class="text-muted">Serviço:</span> <strong>Radiologia</strong></div>
                    <div><span class="text-muted">Função:</span> <strong>Técnico</strong></div>
                    <div><span class="text-muted">Estado:</span> <span class="badge-green role-badge">Ativo</span></div>
                </div>

                <div>
                    <button class="btn btn-cancel" style="border-color: var(--red); color: var(--red);">
                        Pedir Suspensão
                    </button>
                </div>
            </div>
            
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                <span class="text-muted" style="font-size: 0.9rem;">Observações: Trabalho com equipamentos de imagem</span>
            </div>
        </div>


        <div class="health-page">
            
            <div class="card">
                <h2 class="nome mb1">Dosímetros Atribuídos</h2>
                
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Período</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>DOS-001</strong></td>
                                <td>Desde 20/03/2025</td>
                                <td><span class="badge-blue role-badge">Em Uso</span></td>
                            </tr>
                            <tr>
                                <td>DOS-145</td>
                                <td>Set 2024 - Mar 2025</td>
                                <td><span class="badge-gray role-badge;">Devolvido</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h2 class="nome mb1">Histórico de Pedidos</h2>
                
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>12/03/2025</td>
                                <td><span class="badge-green role-badge">Aprovado</span></td>
                            </tr>
                            <tr>
                                <td>10/09/2024</td>
                                <td><span class="badge-red role-badge">Rejeitado</span></td>
                            </tr>
                            <tr>
                                <td>01/01/2024</td>
                                <td><span class="badge-purple role-badge">Concluído</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </main>

    <?php renderFooter(); ?>

</div>
</body>
</html>