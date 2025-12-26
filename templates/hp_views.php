<?php
// Helper de Cores
function getStatusClass($estado) {
    switch (strtolower(trim($estado))) {
        case 'ativo': return 'badge-green';
        case 'em uso': case 'aprovado': return 'badge-blue';
        case 'rejeitado': return 'badge-red';
        case 'pendente': return 'badge-yellow';
        default: return 'badge-gray';
    }
}

// 1. Renderizar as Abas
function renderHPTabs($currentTab) {
    ?>
    <div class="admin-tabs">
        <a href="HP.php?tab=dashboard" class="tab-link <?php echo $currentTab=='dashboard'?'active':''; ?>">Página Inicial</a>
        <a href="HP.php?tab=pedidos" class="tab-link <?php echo $currentTab=='pedidos'?'active':''; ?>">Todos os Pedidos</a>
        <a href="HP.php?tab=historico" class="tab-link <?php echo $currentTab=='historico'?'active':''; ?>">Histórico de Dosímetros</a>
        <a href="HP.php?tab=alteracoes" class="tab-link <?php echo $currentTab=='alteracoes'?'active':''; ?>">Alterações no Pedido</a>
    </div>
    <?php
}

// 2. Renderizar Dashboard (COM O NOVO DESIGN)
function renderDashboard($hp, $last) {
    $stDash = 'Novo';
    if ($last) {
        if ($last['decisionMade'] == 0) $stDash = 'Pendente';
        elseif ($last['stAp']) $stDash = 'Aprovado';
        else $stDash = 'Rejeitado';
    }
    ?>
    
    <?php if ($stDash === 'Novo' || $stDash === 'Rejeitado'): ?>
        <div class="card mb2">
            <div class="mb1 header-flex">
                <h2 class="titulo-separador">O Meu Perfil</h2>
            </div>
            <div class="hp-fields-grid">
                <div><label class="profile-label">Serviço</label><input type="text" class="profile-input" value="<?php echo htmlspecialchars($hp['department']??'N/D'); ?>" readonly disabled></div>
                <div><label class="profile-label">Função</label><input type="text" class="profile-input" value="<?php echo htmlspecialchars($hp['profession']??'N/D'); ?>" readonly disabled></div>
            </div>
        </div>
        <div class="card mb2 text-center" style="padding:3rem; border:2px dashed var(--border);">
            <h2 class="titulo mb1">Solicitar uso de Dosímetro</h2>
            <a href="HP.php?tab=dashboard&modal=abrir" class="btn btn-primary">+ Preencher Pedido</a>
        </div>
        <?php if ($stDash === 'Rejeitado'): ?>
            <div class="card mb2" style="border-left:5px solid var(--red);">
                <h2 class="titulo-separador mb1" style="color:var(--red);">Último Pedido Rejeitado</h2>
                <div class="hp-fields-grid">
                    <div><label class="profile-label">Prática</label><div class="stat-number" style="font-size:1.5rem;"><?php echo htmlspecialchars($last['pratica']); ?></div></div>
                    <div style="background:var(--red-light); padding:1rem; border-radius:6px;">
                        <label class="profile-label" style="color:var(--red);">Motivo</label>
                        <div style="color:var(--red); font-weight:600;"><?php echo htmlspecialchars($last['motRej']); ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($stDash === 'Pendente'): ?>
        <div class="card mb2">
            <h2 class="titulo-separador mb1">Pedido em Análise</h2>
            <div class="alert-container alert-info mb2">O seu pedido encontra-se a aguardar aprovação.</div>
            <div class="hp-fields-grid">
                <div style="grid-column: 1/-1;"><label class="profile-label">Prática Declarada</label><div class="stat-number"><?php echo htmlspecialchars($last['pratica']); ?></div></div>
                <div><label class="profile-label">Data Envio</label><div class="nome"><?php echo isset($last['requestDate']) ? date('d/m/Y', strtotime($last['requestDate'])) : '-'; ?></div></div>
                <div style="display:flex; align-items:center;"><span class="badge-yellow role-badge" style="font-size:1rem; padding:0.5rem 1rem;">Pendente</span></div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($stDash === 'Aprovado'): ?>
        
        <div class="page-header-row">
            <h2 class="titulo" style="margin:0;">O Meu Pedido Atual</h2>
            <?php if ($last['stAp'] === 'Ativo'): ?>
                <form action="processa_hp.php" method="POST" style="margin:0;">
                    <input type="hidden" name="action" value="suspender_pedido">
                    <button class="btn btn-cancel" style="color:var(--red); border-color:var(--red-light2);">Pedir Suspensão</button>
                </form>
            <?php elseif ($last['stAp'] === 'Suspenso'): ?>
                <form action="processa_hp.php" method="POST" style="margin:0;">
                    <input type="hidden" name="action" value="ativar_pedido">
                    <button class="btn btn-primary">Pedir Ativação</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="summary-card">
            <div class="summary-header">
                <div class="summary-title-group">
                    <span class="summary-label">Prática Declarada</span>
                    <span class="summary-value"><?php echo htmlspecialchars($last['pratica']); ?></span>
                </div>
                <div>
                    <span class="<?php echo getStatusClass($last['stAp']); ?> role-badge" style="font-size: 0.9rem; padding: 6px 12px;">
                        <?php echo htmlspecialchars($last['stAp']); ?>
                    </span>
                </div>
            </div>

            <div class="summary-body">
                <div>
                    <span class="profile-label">Aprovado Por</span>
                    <div class="nome" style="font-size:1rem;"><?php echo htmlspecialchars($last['name'].' '.$last['surname']); ?></div>
                </div>
                <div>
                    <span class="profile-label">Data de Aprovação</span>
                    <div class="nome" style="font-size:1rem; font-weight:500;"><?php echo date('d/m/Y', strtotime($last['approvalDate'])); ?></div>
                </div>
            </div>

            <div class="summary-footer">
                <span class="tech-item">Categoria: <strong><?php echo htmlspecialchars($last['riskCategory']); ?></strong></span>
                <span class="tech-item">Tipo: <strong><?php echo htmlspecialchars($last['dosimeterType']); ?></strong></span>
                <span class="tech-item">Periodicidade: <strong><?php echo htmlspecialchars($last['periodicity']); ?></strong></span>
            </div>
        </div>

        <div class="card mb2">
            <h3 class="titulo-separador mb1">Dosímetro Atribuído</h3>
            <div class="table-container">
                <table class="admin-table">
                    <thead><tr><th>Nº Série</th><th>Data Atribuição</th><th>Próxima Troca</th><th>Estado</th></tr></thead>
                    <tbody><tr>
                        <td>
                            <?php if($last['dosimeterSerial']): ?>
                                <strong class="serial-mono"><?php echo htmlspecialchars($last['dosimeterSerial']); ?></strong>
                            <?php else: ?>
                                <span class="text-muted" style="font-style:italic;">Por Associar</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $last['assignmentDate']?date('d/m/Y',strtotime($last['assignmentDate'])):'-'; ?></td>
                        <td><?php echo $last['nextReplacementDate']?"<span class='com-laranja'>".date('d/m/Y',strtotime($last['nextReplacementDate']))."</span>":'-'; ?></td>
                        <td><span class="<?php echo getStatusClass($last['stDos']??'Pendente'); ?> role-badge"><?php echo htmlspecialchars($last['stDos']??'Pendente'); ?></span></td>
                    </tr></tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
    <?php
}

// 3. Lista Pedidos
function renderOrdersList($pedidos) {
    ?>
    <div class="card mb2">
        <div class="mb1 header-flex">
            <h2 class="titulo-separador">Histórico Completo</h2>
            <span class="badge-gray role-badge"><?php echo count($pedidos); ?> Registos</span>
        </div>
        <div class="table-container">
            <table class="admin-table">
                <thead><tr><th>Data</th><th>Prática</th><th>Estado</th><th>Detalhes</th></tr></thead>
                <tbody>
                    <?php foreach ($pedidos as $p): 
                        if ($p['decisionMade'] == 0) { $est='Pendente'; $cls='badge-yellow'; }
                        elseif ($p['stAp']) { $est='Aprovado'; $cls='badge-blue'; }
                        else { $est='Rejeitado'; $cls='badge-red'; }
                    ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($p['requestDate'])); ?></td>
                        <td><?php echo htmlspecialchars($p['pratica']); ?></td>
                        <td><span class="<?php echo $cls; ?> role-badge"><?php echo $est; ?></span></td>
                        <td>
                            <?php if ($est==='Rejeitado'): ?> <span style="color:var(--red);">Motivo: <?php echo htmlspecialchars($p['motRej']); ?></span>
                            <?php elseif ($est==='Aprovado'): ?> <span class="com-cinza">Por: <?php echo htmlspecialchars($p['name'].' '.$p['surname']); ?></span>
                            <?php else: ?> - <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

// 4. Histórico
function renderHistoryTab($hist, $top) {
    ?>
    <?php if (($top['status'] ?? '') === 'Ativo'): ?>
        <div class="card mb2 card-stat-primary">
            <p class="com-laranja">Dosímetro em Uso</p>
            <div class="header-flex">
                <h2 class="stat-number text-primary"><?php echo htmlspecialchars($top['dosimeterSerial']??'---'); ?></h2>
                <div>
                    <span class="com-cinza">Trocar até:</span>
                    <strong class="com-laranja"><?php echo $top['nextReplacementDate'] ? date('d/m/Y', strtotime($top['nextReplacementDate'])) : '-'; ?></strong>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2 class="titulo-separador mb1">Histórico de Monitorização</h2>
        <div class="table-container">
            <table class="admin-table">
                <thead><tr><th>Dosímetro</th><th>Início</th><th>Fim</th><th>Estado</th></tr></thead>
                <tbody>
                    <?php foreach ($hist as $h): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($h['dosimeterSerial']??'A aguardar...'); ?></td>
                        <td><?php echo !empty($item['dI']) ? date('d/m/Y', strtotime($item['dI'])) : '-'; ?></td>
                        <td><?php echo $h['dF_real'] ? date('d/m/Y', strtotime($h['dF_real'])) : ($h['dF_prev'] ? "<span class='com-laranja'>".date('d/m/Y', strtotime($h['dF_prev']))."</span>" : "-"); ?></td>
                        <td><span class="<?php echo getStatusClass($h['st']=='Em_Uso'?'Em Uso':$h['st']); ?> role-badge"><?php echo htmlspecialchars($h['st']=='Em_Uso'?'Em Uso':$h['st']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

// 5. Alterações
function renderChangesTab($alt) {
    ?>
    <div class="card mb2">
        <h2 class="titulo-separador mb1">Suspensões e Ativações</h2>
        <div class="table-container">
            <table class="admin-table">
                <thead><tr><th>Data</th><th>Tipo</th><th>Mensagem</th><th>Estado</th></tr></thead>
                <tbody>
                    <?php foreach ($alt as $a): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($a['requestDate'])); ?></td>
                        <td><strong><?php echo htmlspecialchars($a['requestType']); ?></strong></td>
                        <td>
                            <div class="com-cinza">"<?php echo htmlspecialchars($a['message']); ?>"</div>
                            <?php if($a['adminNote']): ?><div class="com-verde" style="font-size:0.8rem;">Resp: <?php echo htmlspecialchars($a['adminNote']); ?></div><?php endif; ?>
                        </td>
                        <td><span class="<?php echo getStatusClass($a['status']); ?> role-badge"><?php echo $a['status']; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

// 6. Modal (Sem Scripts)
function renderRequestModal($profModal, $depModal, $isOpen) {
    if (!$isOpen) return;
    ?>
    <div class="modal-overlay">
        <div class="modal-box">
            <h3 class="titulo mb1">Solicitar Dosímetro</h3>
            <form action="processa_hp.php" method="POST">
                <input type="hidden" name="action" value="novo_pedido">
                <div class="profile-form-grid">
                    <div><label class="profile-label">Prática *</label><input type="text" name="pratica" class="profile-input" required></div>
                    <div class="hp-fields-grid">
                        <div><label class="profile-label">Departamento</label><input type="text" class="profile-input" value="<?php echo htmlspecialchars($depModal); ?>" readonly disabled></div>
                        <div><label class="profile-label">Função</label><input type="text" class="profile-input" value="<?php echo htmlspecialchars($profModal); ?>" readonly disabled></div>
                    </div>
                </div>
                <div class="modal-actions">
                    <a href="HP.php?tab=dashboard" class="btn btn-cancel">Cancelar</a>
                    <button type="submit" class="btn btn-save-profile">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
    <?php
}
?>