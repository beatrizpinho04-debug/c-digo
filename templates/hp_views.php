<?php
function getStatusClass($estado) {
    switch (strtolower(trim($estado))) {
        case 'ativo': return 'badge-green';
        case 'em uso': case 'aprovado': return 'badge-blue';
        case 'rejeitado': return 'badge-red';
        case 'pendente': return 'badge-yellow';
        default: return 'badge-gray';
    }
}

// 1. Abas
function renderHPTabs($currentTab) {
    ?>
    <div class="admin-tabs">
        <a href="HP.php?tab=dashboard" class="tab-link <?php echo $currentTab=='dashboard'?'active':''; ?>">Página Inicial</a>
        <a href="HP.php?tab=pedidos" class="tab-link <?php echo $currentTab=='pedidos'?'active':''; ?>">Histórico de Pedidos</a>
        <a href="HP.php?tab=historico" class="tab-link <?php echo $currentTab=='historico'?'active':''; ?>">Histórico de Dosímetros</a>
        <a href="HP.php?tab=alteracoes" class="tab-link <?php echo $currentTab=='alteracoes'?'active':''; ?>">Alterações no Pedido</a>
    </div>
    <?php
}

// 2. Dashboard
function renderDashboard($hp, $last, $temPedidoPendente) {
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
                <div><label class="profile-label">Serviço</label><input type="text" class="profile-input" value="<?php echo htmlspecialchars($hp['profession']??'N/D'); ?>" readonly disabled></div>
                <div><label class="profile-label">Função</label><input type="text" class="profile-input" value="<?php echo htmlspecialchars($hp['department']??'N/D'); ?>" readonly disabled></div>
            </div>
        </div>
        
        <div class="card mb2 text-center card-dashed">
            <h2 class="titulo mb1">Solicitar uso de Dosímetro</h2>
            <a href="HP.php?tab=dashboard&modal=abrir" class="btn btn-primary">+ Preencher Pedido</a>
        </div>

        <?php if ($stDash === 'Rejeitado'): ?>
            <div class="card mb2 card-rejected">
                <h2 class="titulo-separador mb1 text-red">Último Pedido Rejeitado</h2>
                <div class="hp-fields-grid">
                    <div>
                        <label class="profile-label">Prática:</label>
                        <div class="stat-number"><?php echo htmlspecialchars($last['pratica']); ?></div>
                    </div>
                    <div class="rejected-box">
                        <label class="profile-label text-red">Motivo</label>
                        <div class="text-red font-bold"><?php echo htmlspecialchars($last['motRej']); ?></div>
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
                <div><label class="profile-label">Prática:</label><div class="stat-number"><?php echo htmlspecialchars($last['pratica']); ?></div></div>
                <div><label class="profile-label">Data Envio</label><div class="nome"><?php echo isset($last['requestDate']) ? date('d/m/Y', strtotime($last['requestDate'])) : '-'; ?></div></div>
                <div><span class="badge-yellow role-badge">Pendente</span></div>
            </div>
        </div>
    <?php endif; ?>
<?php if ($stDash === 'Aprovado'): ?>
        
        <div class="page-header-row mb1">
            <h2 class="titulo">O Meu Pedido Atual</h2>

            <?php if ($temPedidoPendente): ?>
            <div class="alert-container badge-yellow">
                Já existe um pedido a aguardar decisão do administrador.
            </div>
            <?php else: ?>
                <?php if ($last['stAp'] === 'Ativo'): ?>
                    <a href="HP.php?tab=dashboard&modal=suspender" class="btn btn-cancel">Pedir Suspensão</a>
                <?php elseif ($last['stAp'] === 'Suspenso'): ?>
                    <a href="HP.php?tab=dashboard&modal=ativar" class="btn btn-primary">Pedir Ativação</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="summary-card">
            <div class="summary-header">
                <div class="summary-title-group">
                    <span class="summary-label">Prática:</span>
                    <span class="summary-value"><?php echo htmlspecialchars($last['pratica']); ?></span>
                </div>
                <div>
                    <span class="<?php echo getStatusClass($last['stAp']); ?> role-badge">
                        <?php echo htmlspecialchars($last['stAp']); ?>
                    </span>
                </div>
            </div>

            <div class="summary-body">
                <div>
                    <span class="profile-label">Aprovado Por</span>
                    <div class="nome">
                        <?php echo htmlspecialchars($last['name'].' '.$last['surname']); ?>
                    </div>
                    <div class="com-cinza">
                        <?php echo htmlspecialchars($last['email'] ?? ''); ?>
                    </div>
                </div>
                <div>
                    <span class="profile-label">Data de Aprovação</span>
                    <div class="nome"><?php echo date('d/m/Y', strtotime($last['approvalDate'])); ?></div>
                </div>
                <div>
                    <span class="profile-label">Data do Último Pedido</span>
                    <div class="nome"><?php echo isset($last['requestDate']) ? date('d/m/Y', strtotime($last['requestDate'])) : 'N/A'; ?></div>
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
                                <span class="text-muted">Por Associar</span>
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

// 3. Lista de Todos os Pedidos 
function renderOrdersList($pedidos) {
    ?>
    <div class="card mb2">
        <div class="mb1 header-flex">
            <h2 class="titulo-separador">Lista de Pedidos</h2>
            <span class="badge-gray role-badge"><?php echo count($pedidos); ?> Registos</span>
        </div>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Data do pedido</th>
                        <th>Prática</th>
                        <th>Decisão</th>
                        <th>Físico</th>
                        <th>Data de decisão</th>
                        <th>Detalhes</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pedidos)): ?>
                        <tr><td colspan="7" class="msg-nav text-center">Nenhum registo encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pedidos as $p): 
                            if ($p['decisionMade'] == 0) { 
                                $decisaoTxt='Pendente'; $decisaoCls='badge-yellow'; 
                            } elseif ($p['stAp']) { 
                                $decisaoTxt='Aprovado'; $decisaoCls='badge-green'; 
                            } else { 
                                $decisaoTxt='Rejeitado'; $decisaoCls='badge-red'; 
                            }
                            $estadoBadge = '---';
                            if ($decisaoTxt === 'Aprovado') {
                                $stClass = getStatusClass($p['stAp']); 
                                $estadoBadge = '<span class="'.$stClass.' role-badge">'.htmlspecialchars($p['stAp']).'</span>';
                            }
                        ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($p['requestDate'])); ?></td>
                            
                            <td><?php echo htmlspecialchars($p['pratica']); ?></td>
                            
                            <td><span class="<?php echo $decisaoCls; ?> role-badge"><?php echo $decisaoTxt; ?></span></td>
                            
                            <td>
                                <?php if (!empty($p['name'])): ?>
                                    <span class="nome-tab"><?php echo htmlspecialchars($p['name'].' '.$p['surname']); ?></span><br>
                                    <span class="com-cinza"><?php echo htmlspecialchars($p['email']); ?></span>
                                <?php else: ?>
                                    <span class="com-cinza">---</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php echo !empty($p['approvalDate']) ? date('d/m/Y', strtotime($p['approvalDate'])) : '---'; ?>
                            </td>

                            <td>
                                <?php if ($decisaoTxt === 'Aprovado'): ?>
                                    <div style="line-height: 1.4;">
                                        <div><strong>Tipo de dosímetro:</strong> <?php echo htmlspecialchars($p['dosimeterType']); ?></div>
                                        <div><strong>Categoria de Risco:</strong> <?php echo htmlspecialchars($p['riskCategory']); ?></div>
                                        <div><strong>Periodicidade:</strong> <?php echo htmlspecialchars($p['periodicity']); ?></div>
                                    </div>
                                <?php elseif ($decisaoTxt === 'Rejeitado'): ?>
                                    <span class="text-red"><strong>Motivo:</strong> <?php echo htmlspecialchars($p['motRej']); ?></span>
                                <?php else: ?>
                                    <span class="com-cinza">A aguardar avaliação...</span>
                                <?php endif; ?>
                            </td>
                            
                            <td><?php echo $estadoBadge; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

// 4. Histórico de Dosímetros
function renderHistoryTab($hist, $top) {
    ?>
    <div class="card">
        <div class="mb1 header-flex">
            <h2 class="titulo-separador">Dosímetros Usados/Em Uso</h2>
        </div>

        <?php if (empty($hist)): ?>
            <p class="msg-nav text-center">Ainda não existem registos de dosímetros atribuídos.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nº Série</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hist as $row): 
                            $isAtivo = ($row['estado'] === 'Em Uso');
                            $badgeClass = $isAtivo ? 'badge-blue' : 'badge-gray';
                            $inicio = date('d/m/Y', strtotime($row['assignmentDate']));
                            $fim = $row['finalDate'] ? date('d/m/Y', strtotime($row['finalDate'])) : '-';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['dosimeterSerial']); ?></td>
                            
                            <td><?php echo $inicio; ?></td>
                            
                            <td><?php echo $fim; ?></td>
                            
                            <td>
                                <span class="role-badge <?php echo $badgeClass; ?>">
                                    <?php echo htmlspecialchars($row['estado']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

// 5. Alterações
function renderChangesTab($alt) {
    ?>
    <div class="card mb2">
        <h2 class="titulo-separador mb1">Registo de Alterações de Estado</h2>
        
        <?php if (empty($alt)): ?>
            <p class="msg-nav text-center">Nenhum registo de alteração encontrado.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Data Pedido</th>
                            <th>Tipo</th>
                            <th>Justificação</th>
                            <th>Decisão</th>
                            <th>Administrador</th>
                            <th>Data Decisão</th>
                            <th>Nota Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alt as $a): 
                            $dataPed = !empty($a['requestDate']) ? date('d/m/Y', strtotime($a['requestDate'])) : '-';
                            $dataDec = !empty($a['decisionDate']) ? date('d/m/Y', strtotime($a['decisionDate'])) : '--';
                            $tipoTxt = htmlspecialchars($a['requestType'] ?? 'N/D');
                            $tipoClass = ($tipoTxt === 'Suspender') ? 'badge-red' : 'badge-green';
                            $statusTxt = htmlspecialchars($a['status'] ?? 'Pendente');
                            $statusClass = 'badge-gray';
                            if ($statusTxt === 'Aprovado') $statusClass = 'badge-green';
                            elseif ($statusTxt === 'Rejeitado') $statusClass = 'badge-red';
                            elseif ($statusTxt === 'Pendente') $statusClass = 'badge-yellow';
                            $adminName = !empty($a['admin_name']) ? htmlspecialchars($a['admin_name'].' '.$a['admin_surname']) : '--';
                        ?>
                        <tr>
                            <td><?php echo $dataPed; ?></td>
                            
                            <td><span class="role-badge <?php echo $tipoClass; ?>"><?php echo $tipoTxt; ?></span></td>
                            
                            <td><?php echo htmlspecialchars($a['message'] ?? ''); ?></td>
                            
                            <td><span class="role-badge <?php echo $statusClass; ?>"><?php echo $statusTxt; ?></span></td>

                            <td><?php echo $adminName; ?></td>

                            <td><?php echo $dataDec; ?></td>

                            <td><?php echo htmlspecialchars($a['adminNote'] ?? '--'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

// 6. Modal Novo Pedido
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

// 7. Modal Suspender/Ativar
function renderSuspensionModal($type) {
    if ($type === 'suspender') {
        $titulo = "Suspender Monitorização";
        $subtitulo = "Indique o motivo para suspender o uso do dosímetro.";
        $actionValue = "suspender_pedido";
        $btnClass = "btn-no"; 
        $btnText = "Confirmar Suspensão";
    } else {
        $titulo = "Reativar Monitorização";
        $subtitulo = "Indique o motivo para retomar a monitorização.";
        $actionValue = "ativar_pedido";
        $btnClass = "btn-save-profile"; 
        $btnText = "Confirmar Ativação";
    }
    ?>
    <div class="modal-overlay">
        <div class="modal-box">
            <h3 class="titulo mb1"><?php echo $titulo; ?></h3>
            <p class="subtitulo mb1"><?php echo $subtitulo; ?></p>

            <form action="processa_hp.php" method="POST">
                <input type="hidden" name="action" value="<?php echo $actionValue; ?>">

                <div class="form-group mb1_5">
                    <label class="profile-label">Justificação (Obrigatório)</label>
                    <textarea 
                        name="motivo" 
                        class="profile-input" 
                        rows="3" 
                        required 
                        placeholder="Escreva aqui a justificação..."
                        autofocus
                    ></textarea>
                </div>

                <div class="modal-actions">
                    <a href="HP.php?tab=dashboard" class="btn btn-cancel">Cancelar</a>
                    <div>
                        <button type="submit" class="<?php echo 'btn ' . $btnClass; ?>">
                            <?php echo $btnText; ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
}
?>