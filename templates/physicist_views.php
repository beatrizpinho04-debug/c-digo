<?php

/**
 * 1. GESTÃO DE PEDIDOS
 */
function renderPendingRequestsTable($pedidos) { ?>
    <div class="card">
        <h2 class="titulo-separador mb1">Pedidos de Profissionais de Saúde</h2>
        
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Profissional</th>
                        <th>Departamento</th>
                        <th>Prática</th>
                        <th class="txt-right">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pedidos)): ?>
                        <tr><td colspan="4" class="text-center msg-nav">Não existem pedidos por avaliar.</td></tr>
                    <?php else: foreach ($pedidos as $p): ?>
                        <tr>
                            <td>
                                <span class="nome-tab"><?= htmlspecialchars($p['name'] . ' ' . $p['surname']); ?></span>
                            </td>
                            <td><?= htmlspecialchars($p['department']); ?></td>
                            <td><?= htmlspecialchars($p['pratica']); ?></td>
                            <td class="txt-right">
                                <a href="physicist.php?tab=gestao&id_avaliar=<?= $p['idR']; ?>" class="btn btn-primary btn-sm">
                                    Avaliar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php }

/**
 * 2. FORMULÁRIO DE DECISÃO
 */
function renderReviewForm($idR) { ?>
    <div class="card">
        <h2 class="titulo-separador mb2">Avaliação Técnica do Pedido #<?= htmlspecialchars($idR) ?></h2>
        
        <form action="./process_physicist_decision.php" method="POST">
            <input type="hidden" name="idR" value="<?= htmlspecialchars($idR) ?>">
            <input type="hidden" name="action" value="evaluate_professional">

            <div class="profile-form-grid mb2">
                <div class="form-group">
                    <label class="profile-label">Periodicidade de Leitura</label>
                    <select name="periodicity" class="profile-input" required>
                        <option value="Mensal">Mensal</option>
                        <option value="Trimestral">Trimestral</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="profile-label">Categoria de Risco</label>
                    <select name="riskCategory" class="profile-input" required>
                        <option value="Categoria A">Categoria A</option>
                        <option value="Categoria B">Categoria B</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="profile-label">Tipo de Dosímetro</label>
                    <select name="dosimeterType" class="profile-input" required>
                        <option value="Corpo Inteiro">Corpo Inteiro</option>
                        <option value="Extremidade">Extremidade</option>
                    </select>
                </div>
            </div>

            <div class="form-group mb2">
                <label class="profile-label text-red">Justificação / Comentários Técnicos</label>
                <textarea name="comment" class="profile-input" rows="3" placeholder="Obrigatório caso pretenda rejeitar o pedido..."></textarea>
            </div>

            <div class="modal-actions">
                <a href="physicist.php?tab=gestao" class="btn btn-cancel">Voltar</a>
                <div class="action-buttons">
                    <button type="submit" name="outcome" value="reject" class="btn btn-no">Rejeitar Pedido</button>
                    <button type="submit" name="outcome" value="approve" class="btn btn-save-profile">Aprovar e Ativar</button>
                </div>
            </div>
        </form>
    </div>
<?php }

/**
 * 3. PROFISSIONAIS ATIVOS
 */
function renderProfessionalDetails($u, $pedidos, $histDosimetros, $subtab = 'info') {
    if (!$u) {
        echo "<div class='alert-error'>Utilizador não encontrado.</div>";
        return;
    }

    $idU = $u['idU'];
    $nomeCompleto = htmlspecialchars($u['name'] . ' ' . $u['surname']);
    ?>
    <div class="card">
        <div class="header-flex mb2">
            <div class="header-flex-left">
                <h2 class="titulo-separador">Detalhes do Profissional</h2>
                <p class="subtitulo"><?= $nomeCompleto ?></p>
            </div>
            <a href="physicist.php?tab=profissionais" class="btn btn-cancel">← Voltar</a>
        </div>

        <div class="admin-tabs mb2">
            <a href="physicist.php?tab=profissionais&id_detalhe=<?= $idU ?>&subtab=info" 
               class="tab-link <?= $subtab === 'info' ? 'active' : '' ?>">Informações</a>
            <a href="physicist.php?tab=profissionais&id_detalhe=<?= $idU ?>&subtab=pedidos" 
               class="tab-link <?= $subtab === 'pedidos' ? 'active' : '' ?>">Pedidos</a>
            <a href="physicist.php?tab=profissionais&id_detalhe=<?= $idU ?>&subtab=historico_dos" 
               class="tab-link <?= $subtab === 'historico_dos' ? 'active' : '' ?>">Histórico Dosímetros</a>
        </div>

        <div class="tab-content">
            <?php if ($subtab === 'info'): ?>
                <div class="profile-form-grid">
                    <div>
                        <p class="profile-label">Email</p>
                        <p class="mb1"><strong><?= htmlspecialchars($u['email']) ?></strong></p>
                        
                        <p class="profile-label">Telemóvel</p>
                        <p class="mb1"><?= htmlspecialchars($u['phoneN'] ?? 'N/A') ?></p>
                    </div>
                    <div>
                        <p class="profile-label">Profissão</p>
                        <p class="mb1"><?= htmlspecialchars($u['profession'] ?? 'Físico Médico') ?></p>
                        
                        <p class="profile-label">Departamento</p>
                        <p class="mb1"><?= htmlspecialchars($u['department'] ?? 'Física Médica') ?></p>
                    </div>
                </div>

            <?php elseif ($subtab === 'pedidos'): ?>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Data Pedido</th>
                                <th>Prática</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pedidos)): ?>
                                <tr><td colspan="3" class="text-center">Sem pedidos registados.</td></tr>
                            <?php else: foreach ($pedidos as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['requestDate']) ?></td>
                                    <td><?= htmlspecialchars($p['pratica']) ?></td>
                                    <td>
                                        <?php 
                                        $badge = $p['decisionMade'] == 0 ? 'badge-blue' : ($p['approvedID'] ? 'alert-success' : 'alert-error');
                                        $texto = $p['decisionMade'] == 0 ? 'Pendente' : ($p['approvedID'] ? 'Aprovado' : 'Rejeitado');
                                        ?>
                                        <span class="role-badge <?= $badge ?>"><?= $texto ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($subtab === 'historico_dos'): ?>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nº Série</th>
                                <th>Atribuído em</th>
                                <th>Troca / Fim</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($histDosimetros)): ?>
                                <tr><td colspan="3" class="text-center">Este utilizador ainda não teve dosímetros associados.</td></tr>
                            <?php else: foreach ($histDosimetros as $d): ?>
                                <tr>
                                    <td><span class="nome-tab"><?= htmlspecialchars($d['serial'] ?? 'N/A') ?></span></td>
                                    <td><?= !empty($d['dateIn']) ? date('d/m/Y', strtotime($d['dateIn'])) : 'N/A' ?></td>
                                    <td>
                                        <?php if (empty($d['dateOut'])): ?>
                                            <span class="role-badge alert-success">Em Uso</span>
                                        <?php else: ?>
                                            <?= date('d/m/Y', strtotime($d['dateOut'])) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}


/**
 * 5. O MEU DOSÍMETRO (Versão Final: Respostas Fechadas + Justificação Aberta)
 */
function renderMyDosimeterArea($meuPedido, $meuDosimetro, $meuHistorico = []) { ?>
    <div class="summary-card">
        <div class="summary-header">
            <div class="summary-title-group">
                <span class="summary-label">O Meu Dosímetro</span>
                <h2 class="summary-value">
                    <?= $meuPedido ? htmlspecialchars($meuPedido['pratica']) : 'Solicitar Dosímetro' ?>
                </h2>
            </div>
            <?php if ($meuPedido): ?>
                <span class="role-badge <?= ($meuPedido['status'] === 'Ativo') ? 'badge-green' : 'badge-yellow' ?>">
                    <?= htmlspecialchars($meuPedido['status']) ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="summary-body">
            <?php if (!$meuPedido): ?>
                <div class="header-flex-left">
                    <p class="subtitulo mb1">Preencha os dados técnicos para solicitar o seu dosímetro.</p>
                    <form action="./process_physicist_decision.php" method="POST">
                        <input type="hidden" name="action" value="auto_request">
                        
                        <div class="form-group mb1">
                            <label class="profile-label">Prática Profissional:</label>
                            <input type="text" name="pratica" class="profile-input" required placeholder="Ex: Radioterapia">
                        </div>

                        <div class="profile-form-grid mb1">
                            <div class="form-group">
                                <label class="profile-label">Periodicidade:</label>
                                <select name="periodicity" class="profile-input" required>
                                    <option value="Mensal">Mensal</option>
                                    <option value="Trimestral">Trimestral</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="profile-label">Categoria de Risco:</label>
                                <select name="riskCategory" class="profile-input" required>
                                    <option value="Categoria A">Categoria A</option>
                                    <option value="Categoria B">Categoria B</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="profile-label">Tipo de Dosímetro:</label>
                                <select name="dosimeterType" class="profile-input" required>
                                    <option value="Corpo Inteiro">Corpo Inteiro</option>
                                    <option value="Extremidade">Extremidade</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-save-profile">Confirmar</button>
                    </form>
                </div>

            <?php else: ?>
                <div>
                    <span class="summary-label">Número de Série</span>
                    <p class="nome-tab" style="font-size: 1.25rem; font-family: monospace;">
                        <?= $meuDosimetro ? htmlspecialchars($meuDosimetro['dosimeterSerial']) : 'Pendente de Atribuição' ?>
                    </p>
                </div>
                
                <?php if ($meuDosimetro): ?>
                    <div>
                        <span class="summary-label">Ações Disponíveis</span>
                        <div class="action-buttons mt05">
                            <?php if ($meuPedido['status'] === 'Ativo'): ?>
                                <a href="physicist.php?tab=meu_dosimetro&solicitar=suspensao" class="btn btn-no btn-sm">Suspender</a>
                            <?php elseif ($meuPedido['status'] === 'Suspenso'): ?>
                                <a href="physicist.php?tab=meu_dosimetro&solicitar=reativacao" class="btn btn-primary btn-sm">Reativar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php if ($meuPedido): ?>
            <div class="summary-footer">
                <?php if ($meuDosimetro): ?>
                    <span class="tech-item">Próxima Troca Prevista: <strong><?= date('d/m/Y', strtotime($meuDosimetro['nextReplacementDate'])) ?></strong></span>
                <?php else: ?>
                    <span class="tech-item">Aguardando que o Administrador associe um equipamento físico ao seu pedido.</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['solicitar'])): ?>
        <div class="card mb2">
            <h3 class="titulo-separador mb1">Justificação de <?= ucfirst($_GET['solicitar']) ?></h3>
            <form action="process_physicist_decision.php" method="POST">
                <input type="hidden" name="action" value="request_change">
                <input type="hidden" name="type" value="<?= htmlspecialchars($_GET['solicitar']) ?>">
                <textarea name="message" class="profile-input mb1" rows="3" required placeholder="Escreva o motivo aqui..."></textarea>
                <div class="action-buttons">
                    <button type="submit" class="btn btn-save-profile">Enviar</button>
                    <a href="physicist.php?tab=meu_dosimetro" class="btn btn-cancel">Cancelar</a>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div class="card">
        <h3 class="titulo-separador mb1">Histórico de Atribuições</h3>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr><th>Nº Série</th><th>Atribuído em</th><th>Troca / Fim</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($meuHistorico)): ?>
                        <tr><td colspan="3" class="text-center msg-nav">Não existem registos anteriores.</td></tr>
                    <?php else: foreach ($meuHistorico as $h): ?>
                        <tr>
                            <td><span class="nome-tab"><?= htmlspecialchars($h['serial']) ?></span></td>
                            <td><?= date('d/m/Y', strtotime($h['dateIn'])) ?></td>
                            <td><?= $h['dateOut'] ? date('d/m/Y', strtotime($h['dateOut'])) : '<span class="role-badge alert-success">Em Uso</span>' ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php }
      

/**
 * 6. REGISTOS E ALTERAÇÕES (Histórico)
 */
function renderFullHistory($historicoDosimetros, $historicoAlteracoes) { ?>
    <div class="card mb2">
        <h3 class="titulo-separador mb1">Histórico de Dosímetros Associados</h3>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr><th>Série</th><th>Data Associação</th><th>Data Troca</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($historicoDosimetros)): ?>
                        <tr><td colspan="3" class="text-center msg-nav">Sem registos.</td></tr>
                    <?php else: foreach ($historicoDosimetros as $h): ?>
                        <tr>
                            <td><span class="serial-mono"><?= $h['serial'] ?></span></td>
                            <td><?= $h['dateIn'] ?></td>
                            <td><?= $h['dateOut'] ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h3 class="titulo-separador mb1">Histórico de Alterações</h3>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr><th>Tipo</th><th>Data Pedido</th><th>Estado Final</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($historicoAlteracoes)): ?>
                        <tr><td colspan="3" class="text-center msg-nav">Sem alterações registadas.</td></tr>
                    <?php else: foreach ($historicoAlteracoes as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['type']) ?></td>
                            <td><?= $a['requestDate'] ?></td>
                            <td>
                                <span class="role-badge badge-gray"><?= htmlspecialchars($a['finalStatus'] ?? 'Pendente') ?></span>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php }

/**
 * TAB: Profissionais Ativos - Listagem com Pesquisa
 */
function renderPhysicianUserList($profissionais, $searchTerm) { ?>
    <div class="card">
        <div class="mb1 header-flex">
            <div class="header-flex-left">
                <h2 class="titulo-separador">Profissionais e Físicos Ativos</h2>
            </div>
            <form action="physicist.php" method="GET" class="search-form">
                <input type="hidden" name="tab" value="profissionais">
                <input type="text" name="search" value="<?= htmlspecialchars($searchTerm); ?>" 
                       placeholder="Procurar..." class="profile-input input-search">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </button>
            </form>
        </div>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr><th>Nome</th><th>Email</th><th class="txt-right">Ação</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($profissionais)): ?>
                        <tr><td colspan="3" class="text-center msg-nav">Nenhum resultado.</td></tr>
                    <?php else: foreach ($profissionais as $p): ?>
                        <tr>
                            <td><span class="nome-tab"><?= htmlspecialchars($p['name'] . ' ' . $p['surname']); ?></span></td>
                            <td><?= htmlspecialchars($p['email']); ?></td>
                            <td class="txt-right">
                                <a href="physicist.php?tab=profissionais&id_detalhe=<?= $p['idU']; ?>" class="btn btn-ver btn-sm">Ver Detalhes</a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php }
function renderManagementDashboard($pedidos) { 
    $total = count($pedidos);
    ?>
    <div class="card mb2">
        <div class="header-flex">
            <div class="header-flex-left">
                <h3 class="profile-label">Resumo de Trabalho</h3>
                <p class="nome-tab">
                    <?php if ($total === 0): ?>
                        Não existem pedidos pendentes de avaliação.
                    <?php else: ?>
                        Tem <strong><?= $total ?></strong> <?= $total === 1 ? 'pedido a aguardar' : 'pedidos a aguardar' ?> a sua revisão técnica.
                    <?php endif; ?>
                </p>
            </div>
            <?php if ($total > 0): ?>
                <span class="role-badge badge-purple">
                    <?= $total ?> Pedidos
                </span>
            <?php endif; ?>
        </div>
    </div>
<?php }
