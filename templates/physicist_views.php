<?php

/**
 * 1. GESTÃO DE PEDIDOS
 */
function renderPendingRequestsTable($pedidos) { ?>
    <div class="card">
        <h2 class="mb1">Pedidos de Profissionais de Saúde</h2>
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
                            <td><?= htmlspecialchars($p['name'] . ' ' . $p['surname']); ?></td>
                            <td><?= htmlspecialchars($p['department']); ?></td>
                            <td><?= htmlspecialchars($p['practice']); ?></td>
                            <td class="txt-right">
                                <a href="physicist.php?tab=gestao&id_avaliar=<?= $p['idR']; ?>" class="btn badge-purple">Avaliar</a>
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
        <div class="flex-between mb2">
            <h3 class="titulo-separador">Avaliar Pedido #<?= htmlspecialchars($idR) ?></h3>
            <a href="physicist.php?tab=gestao" class="btn btn-cancel">Voltar</a>
        </div>
        
        <form action="process_physicist_decision.php" method="POST">
            <input type="hidden" name="idR" value="<?= $idR ?>">
            <input type="hidden" name="action" value="evaluate_professional">

            <div class="form-group mb1">
                <label class="profile-label">Periodicidade (Obrigatório para Aprovação):</label>
                <select name="periodicity" class="profile-input">
                    <option value="Mensal">Mensal</option>
                    <option value="Trimestral">Trimestral</option>
                </select>
            </div>

            <div class="form-group mb1">
                <label class="profile-label">Categoria de Risco:</label>
                <select name="riskCategory" class="profile-input">
                    <option value="Categoria A">Categoria A</option>
                    <option value="Categoria B">Categoria B</option>
                </select>
            </div>

            <div class="form-group mb1">
                <label class="profile-label">Tipo de Dosímetro:</label>
                <input type="text" name="dosimeterType" class="profile-input" placeholder="Ex: Tórax, Anel, etc.">
            </div>

            <div class="form-group mb2">
                <label class="profile-label">Justificação (Obrigatório em caso de Rejeição):</label>
                <textarea name="comment" class="profile-input" rows="3" placeholder="Indique o motivo caso pretenda rejeitar..."></textarea>
            </div>

            <div class="action-buttons">
                <button type="submit" name="outcome" value="approve" class="btn btn-save-profile">Aprovar</button>
                <button type="submit" name="outcome" value="reject" class="btn btn-no">Rejeitar</button>
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
 * 5. O MEU DOSÍMETRO
 */
function renderMyDosimeterArea($meuPedido, $meuDosimetro) { ?>
    <div class="card">
        <h2 class="titulo mb1">O Meu Dosímetro</h2>
        
        <?php if (!$meuPedido): ?>
            <p class="subtitulo mb2">Solicite o seu dosímetro com aprovação imediata.</p>
            <form action="process_physicist_decision.php" method="POST">
                <input type="hidden" name="action" value="auto_request">
                <div class="form-group mb1">
                    <label class="profile-label">Prática:</label>
                    <input type="text" name="pratica" class="profile-input" required placeholder="Ex: Radioterapia">
                </div>
                <div class="form-group mb1">
                    <label class="profile-label">Periodicidade:</label>
                    <select name="periodicity" class="profile-input">
                        <option value="Mensal">Mensal</option>
                        <option value="Trimestral">Trimestral</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-save-profile">Ativar Dosímetro</button>
            </form>
            
        <?php else: ?>
            <div class="dashboard-grid mb2">
                <div class="dash-info-box">
                    <span class="dash-label">Estado</span>
                    <span class="role-badge <?= $meuPedido['status'] === 'Ativo' ? 'badge-green' : 'badge-yellow' ?>">
                        <?= htmlspecialchars($meuPedido['status']) ?>
                    </span>
                </div>
                <div class="dash-info-box">
                    <span class="dash-label">Prática</span>
                    <span class="dash-value"><?= htmlspecialchars($meuPedido['pratica'] ?? 'N/A') ?></span>
                </div>
                <?php if ($meuDosimetro): ?>
                    <div class="dash-info-box">
                        <span class="dash-label">Nº Série</span>
                        <span class="serial-mono"><?= htmlspecialchars($meuDosimetro['dosimeterSerial']) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="action-buttons">
                <?php if ($meuPedido['status'] === 'Ativo'): ?>
                    <a href="physicist.php?tab=meu_dosimetro&solicitar=suspensao" class="btn btn-no">Solicitar Suspensão</a>
                <?php elseif ($meuPedido['status'] === 'Suspenso'): ?>
                    <a href="physicist.php?tab=meu_dosimetro&solicitar=reativacao" class="btn btn-primary">Solicitar Reativação</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['solicitar'])): ?>
        <div class="card mt2">
            <h3 class="titulo-separador">Justificar Pedido de <?= ucfirst($_GET['solicitar']) ?></h3>
            <form action="process_physicist_decision.php" method="POST">
                <input type="hidden" name="action" value="request_change">
                <input type="hidden" name="type" value="<?= $_GET['solicitar'] ?>">
                <textarea name="message" class="profile-input" rows="4" required placeholder="Escreva o motivo aqui..."></textarea>
                <div class="action-buttons mt1">
                    <button type="submit" class="btn btn-save-profile">Enviar</button>
                    <a href="physicist.php?tab=meu_dosimetro" class="btn btn-cancel">Cancelar</a>
                </div>
            </form>
        </div>
    <?php endif; ?>
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
function renderGlobalHistoryTable($historyData, $searchTerm) { ?>
    <div class="card">
        <div class="mb1 header-flex">
            <div class="header-flex-left">
                <h2 class="titulo-separador">Histórico Global de Dosímetros</h2>
                <p class="subtitulo">Consulta de utilizações atuais e passadas de toda a instituição.</p>
            </div>
            <form action="physicist.php" method="GET" class="search-form">
                <input type="hidden" name="tab" value="historico">
                <input type="text" name="search" value="<?= htmlspecialchars($searchTerm); ?>" 
                       placeholder="Nome, Serial ou Email..." class="profile-input input-search">
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
                    <tr>
                        <th>Nº Série</th>
                        <th>Profissional</th>
                        <th>Email</th>
                        <th>Início</th>
                        <th>Fim / Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($historyData)): ?>
                        <tr><td colspan="5" class="text-center msg-nav">Nenhum registo encontrado.</td></tr>
                    <?php else: foreach ($historyData as $row): 
                        $isAtivo = ($row['estado'] === 'Ativo');
                    ?>
                        <tr>
                            <td><span class="serial-mono"><?= htmlspecialchars($row['dosimeterSerial']); ?></span></td>
                            <td><span class="nome-tab"><?= htmlspecialchars($row['name'] . ' ' . $row['surname']); ?></span></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td><?= date('d/m/Y', strtotime($row['assignmentDate'])); ?></td>
                            <td>
                                <?php if ($isAtivo): ?>
                                    <span class="role-badge alert-success">Em Uso (Ativo)</span>
                                <?php else: ?>
                                    <?= date('d/m/Y', strtotime($row['removalDate'])); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php }
?>