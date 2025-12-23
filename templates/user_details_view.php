<?php
// 1. Card principal com info do user
function renderUserHeaderCard($user) {
    if (!$user) return;
    $isActive = $user['userStatus'] == 1;
    $prof = isset($user['profession']) ? $user['profession'] : $user['userType'];
    ?>
    <div class="card mb2">
        <div class="header-flex">
            <div class="foto-circle-large">
                <img src="<?php echo htmlspecialchars($user['profilePic']); ?>" alt="Foto">
            </div>
            
            <div class="user-details-info">
                <div class="user-title-row">
                    <h2 class="titulo"><?php echo htmlspecialchars($user['name'].' '.$user['surname']); ?></h2>
                    <span class="role-badge <?php echo $isActive?'alert-success':'alert-error'; ?>">
                        <?php echo $isActive?'Ativo':'Inativo'; ?>
                    </span>
                </div>
                <p class="subtitulo"><?php echo htmlspecialchars($prof); ?> | <?php echo htmlspecialchars($user['email']); ?></p>
            </div>

            <form action="processa_admin.php" method="POST">
                <input type="hidden" name="action" value="toggle_status">
                <input type="hidden" name="idU" value="<?php echo $user['idU']; ?>">
                <input type="hidden" name="currentStatus" value="<?php echo $user['userStatus']; ?>">
                <input type="hidden" name="source_page" value="details"> 
                <?php if($isActive): ?>
                    <button type="submit" class="btn btn-no">Desativar Utilizador</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-save-profile">Ativar Utilizador</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php
}

// 2. Abas de navegação
function renderUserTabs($idU, $currentTab) {
    ?>
    <div class="admin-tabs mb2">
        <a href="user_details.php?idU=<?php echo $idU; ?>&subtab=info" class="tab-link <?php echo $currentTab == 'info' ? 'active' : ''; ?>">Informações</a>
        <a href="user_details.php?idU=<?php echo $idU; ?>&subtab=pedidos" class="tab-link <?php echo $currentTab == 'pedidos' ? 'active' : ''; ?>">Pedidos</a>
        <a href="user_details.php?idU=<?php echo $idU; ?>&subtab=dosimetros" class="tab-link <?php echo $currentTab == 'dosimetros' ? 'active' : ''; ?>">Histórico Dosímetros</a>
        <a href="user_details.php?idU=<?php echo $idU; ?>&subtab=suspensoes" class="tab-link <?php echo $currentTab == 'suspensoes' ? 'active' : ''; ?>">Histórico Suspensões/Ativações</a>
    </div>
    <?php
}

// 3. Dados Pessoais
function renderUserInfoTab($user) {
    $traducao = [
        'Female' => 'Feminino',
        'Male'   => 'Masculino',
        'Other'  => 'Outro'
    ];
    $sexoPT = isset($traducao[$user['sex']]) ? $traducao[$user['sex']] : $user['sex'];
    ?>
    <h3 class="titulo-separador mb1">Dados Pessoais</h3>
    <div class="profile-form-grid">
        <div><label class="profile-label">Nome Completo</label><input disabled value="<?php echo htmlspecialchars($user['name'].' '.$user['surname']); ?>" class="profile-input"></div>
        <div><label class="profile-label">Email</label><input disabled value="<?php echo htmlspecialchars($user['email']); ?>" class="profile-input"></div>
        <div><label class="profile-label">Telemóvel</label><input disabled value="<?php echo htmlspecialchars($user['phoneN']); ?>" class="profile-input"></div>
        <div><label class="profile-label">Data Nasc.</label><input disabled value="<?php echo date('d/m/Y', strtotime($user['birthDate'])); ?>" class="profile-input"></div>
        <div><label class="profile-label">Sexo</label><input disabled value="<?php echo htmlspecialchars($sexoPT); ?>" class="profile-input"></div>
        <div><label class="profile-label">Tipo</label><input disabled value="<?php echo htmlspecialchars($user['userType']); ?>" class="profile-input"></div>
        <?php if(isset($user['department'])): ?>
            <div><label class="profile-label">Departamento</label><input disabled value="<?php echo htmlspecialchars($user['department']); ?>" class="profile-input"></div>
        <?php endif; ?>
        <?php if(isset($user['profession'])): ?>
            <div><label class="profile-label">Profissão</label><input disabled value="<?php echo htmlspecialchars($user['profession']); ?>" class="profile-input"></div>
        <?php endif; ?>
    </div>
    <?php
}

// 4. Pedidos
function renderUserRequestsTab($requests) {
    if (empty($requests)) { echo "<p class='text-center com-cinza'>Sem pedidos registados.</p>"; return; }
    ?>
    <h3 class="titulo-separador mb1">Histórico de Pedidos</h3>
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
                <?php foreach ($requests as $r): 
                    if (!$r['decisionMade']) {
                        $status = 'Pendente'; $badge = 'badge-gray';
                    } elseif ($r['approvalDate']) {
                        $status = 'Aprovado'; $badge = 'alert-success';
                    } else {
                        $status = 'Rejeitado'; $badge = 'alert-error';
                    }
                ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($r['requestDate'])); ?></td>
                    <td><?php echo htmlspecialchars($r['pratica']); ?></td>
                    <td><span class="role-badge <?php echo $badge; ?>"><?php echo $status; ?></span></td>
                    <td>
                        <?php if ($status === 'Aprovado'): ?>
                            <?php echo htmlspecialchars($r['ap_name'].' '.$r['ap_surname']); ?><br>
                            <?php echo htmlspecialchars($r['ap_email']); ?>
                        
                        <?php elseif ($status === 'Rejeitado'): ?>
                            <?php echo htmlspecialchars($r['rej_name'].' '.$r['rej_surname']); ?><br>
                            <?php echo htmlspecialchars($r['rej_email']); ?>
                        <?php else: ?>
                            --
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($status === 'Aprovado'): ?>
                            <?php echo date('d/m/Y', strtotime($r['approvalDate'])); ?>
                        <?php elseif ($status === 'Rejeitado'): ?>
                            <?php echo date('d/m/Y', strtotime($r['rejectionDate'])); ?>
                        <?php else: ?>
                            --
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($status === 'Aprovado'): ?>
                            <strong>Tipo de dosímetro: </strong> <?php echo htmlspecialchars($r['dosimeterType']); ?><br>
                            <strong>Categoria de Risco: </strong> <?php echo htmlspecialchars($r['riskCategory']); ?><br> 
                            <strong>Períocidade: </strong><?php echo htmlspecialchars($r['periodicity']); ?>

                        <?php elseif ($status === 'Rejeitado'): ?>
                            <strong>Motivo: </strong><?php echo htmlspecialchars($r['rejectionComment']); ?>
                        <?php else: ?>
                            ---
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($status === 'Aprovado'): ?>
                        <?php
                        $statusBadge = ($r['ar_status'] === 'Ativo') ? 'alert-success' : 'alert-error';
                        ?>
                        <span class="role-badge <?php echo $statusBadge; ?>">
                            <?php echo htmlspecialchars($r['ar_status']); ?>
                        </span>

                        <?php elseif ($status === 'Rejeitado'): ?> 
                            ---    
                        <?php else: ?>
                            <span class="role-badge badge-gray">Pendente</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// 5. Histórico de Dosímetros do user
function renderUserDosimetersTab($history, $idU, $searchTerm) {
    ?>
    <div class="mb1 header-flex">
        <h3 class="titulo-separador">Histórico de Equipamentos</h3>
        <form action="user_details.php" method="GET" class="search-form">
            <input type="hidden" name="idU" value="<?php echo $idU; ?>">
            <input type="hidden" name="subtab" value="dosimetros">
            <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Pesquisar..." class="profile-input input-search">
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </button>
        </form>
    </div>

    <?php if (empty($history)): ?>
        <?php if (!empty($searchTerm)): ?>
             <p class="text-center msg-nav">Sem resultados para "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>".</p>
        <?php else: ?>
            <p class="text-center com-cinza">Sem histórico de dosímetros.</p>
        <?php endif; ?>
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
                <?php foreach ($history as $h): $active = ($h['estado'] === 'Ativo'); ?>
                <tr class="<?php echo $active ? 'row-highlight' : ''; ?>">
                    <td><?php echo htmlspecialchars($h['dosimeterSerial']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($h['assignmentDate'])); ?></td>
                    <td><?php echo $active ? '---' : date('d/m/Y', strtotime($h['removalDate'])); ?></td>
                    <td>
                        <span class="role-badge <?php echo $active ? 'alert-success' : 'badge-gray'; ?>">
                            <?php echo $active ? 'Em Uso' : 'Recolhido'; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    <?php
}

// 6. Histórico de Suspensões e Ativações do user
function renderUserSuspensionsTab($changes, $idU, $searchTerm) {
    ?>
    <div class="mb1 header-flex">
        <h3 class="titulo-separador">Registo de Alterações de Estado</h3>
        <form action="user_details.php" method="GET" class="search-form">
            <input type="hidden" name="idU" value="<?php echo $idU; ?>">
            <input type="hidden" name="subtab" value="suspensoes">
            <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Pesquisar..." class="profile-input input-search">
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </button>
        </form>
    </div>

    <?php if (empty($changes)): ?>
        <?php if (!empty($searchTerm)): ?>
             <p class="text-center msg-nav">Sem resultados para "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>".</p>
        <?php else: ?>
            <p class="text-center com-cinza">Sem histórico de alterações.</p>
        <?php endif; ?>
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
                <?php foreach ($changes as $c): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($c['requestDate'])); ?></td>
                    
                    <td>
                        <?php 
                        $badgeType = ($c['requestType'] == 'Ativar') ? 'badge-green' : 'badge-red';
                        ?>
                        <span class="role-badge <?php echo $badgeType; ?>">
                            <?php echo htmlspecialchars($c['requestType']); ?>
                        </span>
                    </td>

                    <td><?php echo htmlspecialchars($c['message']); ?></td>
                    <td>
                        <?php if($c['status']=='Concluido'): ?>
                            <span class="role-badge alert-success">Aprovado</span>
                        <?php elseif($c['status']=='Rejeitado'): ?>
                            <span class="role-badge alert-error">Rejeitado</span>
                        <?php else: ?>
                            <span class="role-badge badge-gray">Pendente</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if($c['idAdmin']): ?>
                            <?php echo htmlspecialchars($c['admin_name'].' '.$c['admin_surname']); ?><br>
                            <?php echo htmlspecialchars($c['admin_email']); ?>
                        <?php else: ?>
                            --
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php echo ($c['decisionDate']) ? date('d/m/Y', strtotime($c['decisionDate'])) : '--'; ?>
                    </td>

                    <td class="subtitulo">
                        <?php echo htmlspecialchars($c['adminNote'] ? $c['adminNote'] : '--'); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    <?php
}
?>