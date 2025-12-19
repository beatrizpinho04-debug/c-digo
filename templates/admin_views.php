<?php
function renderAdminTabs($currentTab) {
    ?>
    <div class="admin-tabs">
        <a href="admin.php?tab=associacao" class="tab-link <?php echo $currentTab === 'associacao' ? 'active' : ''; ?>">Associação de Dosímetros</a>
        <a href="admin.php?tab=gestao" class="tab-link <?php echo $currentTab === 'gestao' ? 'active' : ''; ?>">Gestão de Dosímetros</a>
        <a href="admin.php?tab=pedidos" class="tab-link <?php echo $currentTab === 'pedidos' ? 'active' : ''; ?>">Pedidos de Suspensão/Ativação</a>
        <a href="admin.php?tab=users" class="tab-link <?php echo $currentTab === 'users' ? 'active' : ''; ?>">Utilizadores</a>
    </div>
    <?php
}

// 1. Associação de Dosimetros
function renderAssociationTable($pendingData) {
    ?>
    <div class="card">
        <div class="mb1_5">
            <h2 class="titulo-separador">Associação de Dosímetros</h2>
            <p class="subtítulo">Dosímetros ainda por associar a pedidos aprovados</p>
        </div>

        <?php if (empty($pendingData)): ?>
            <div class="alert-container alert-success">✅ Tudo em dia!</div>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Profissional</th>
                            <th>Email</th>
                            <th>Tipo de dosímetro</th>
                            <th>Periocidade</th>
                            <th>Tipo de Risco</th>
                            <th class="txt-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingData as $row): ?>
                            <tr>
                                <td><span class="nome-tab"><?php echo htmlspecialchars($row['name'] . ' ' . $row['surname']); ?></span></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['dosimeterType']); ?></td>
                                <td><?php echo htmlspecialchars($row['periodicity']); ?></td>
                                <td><span class="role-badge"><?php echo htmlspecialchars($row['riskCategory']); ?></span></td>
                                <td class="txt-right">
                                    <a href="admin.php?tab=associacao&associar=<?php echo $row['idDA']; ?>&user=<?php echo urlencode($row['name'] . ' ' . $row['surname']); ?>" 
                                       class="btn btn-primary"> Associar
                                    </a>
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

// 2. Gestão de Dosímetros
function renderManagementTab($stats, $activeDosimeters, $searchTerm) {
    ?>
    <div class="profile-form-grid mb2">
        <div class="card card-stat-primary">
            <p class="subtítulo">Dosímetros recolhidos entre <?php echo $stats['periodo_analise']; ?>:</p>
            <h2 class="titulo stat-number text-primary"><?php echo $stats['enviados']; ?></h2>
        </div>
        <div class="card card-stat-green">
            <p class="subtítulo">Quantidade de dosímetros necessários para <?php echo $stats['mes_abastecimento']; ?>:</p>
            <h2 class="titulo stat-number text-green"><?php echo $stats['pedir']; ?></h2>
        </div>
    </div>

    <div class="card">
        <div class="mb1 header-flex">
            <h2 class="titulo-separador">Dosímetros Ativos</h2>
            
            <form action="admin.php" method="GET" class="search-form">
                <input type="hidden" name="tab" value="gestao">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Nome, email ..." class="profile-input input-search">
                <button type="submit" class="btn btn-primary">🔍</button>
            </form>
        </div>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Profissional</th>
                        <th>Email</th>
                        <th>Dosímetro</th>
                        <th>Associado em</th>
                        <th>Próxima Troca</th>
                        <th class="txt-right">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($activeDosimeters)): ?>
                        <tr><td colspan="5" class="text-center">Nenhum resultado encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($activeDosimeters as $row): 
                            $isLate = strtotime($row['nextReplacementDate']) < time();
                            $dateClass = $isLate ? 'cell-date-late' : '';
                        ?>
                            <tr>
                                <td><span class="nome-tab"><?php echo htmlspecialchars($row['name'] . ' ' . $row['surname']); ?></span></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['dosimeterSerial']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['assignmentDate'])); ?></td>
                                <td class="<?php echo $dateClass; ?>"><?php echo date('d/m/Y', strtotime($row['nextReplacementDate'])); ?></td>
                                <td class="txt-right">
                                    <a href="admin.php?tab=gestao&action=swap&idDA=<?php echo $row['idDA']; ?>&name=<?php echo urlencode($row['name']); ?>" class="btn btn-cancel btn-sm">Trocar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

// 3. Pedidos de Suspensão/Ativação
function renderRequestsTab($requests) {
    ?>
    <div class="card">
        <h2 class="titulo-separador mb1">Pedidos Pendentes</h2>
        <?php if (empty($requests)): ?>
             <p class="subtítulo">Não existem pedidos pendentes.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Utilizador</th>
                            <th>Tipo</th>
                            <th>Data</th>
                            <th>Justificação</th>
                            <th class="txt-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><span class="nome-tab"><?php echo htmlspecialchars($req['name'].' '.$req['surname']); ?></span></td>
                            <td>
                                <span class="role-badge <?php echo $req['requestType'] == 'Suspender' ? 'badge-red' : 'badge-green'; ?>">
                                    <?php echo htmlspecialchars($req['requestType']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($req['requestDate'])); ?></td>
                            <td class="subtítulo"><?php echo htmlspecialchars($req['justification']); ?></td>
                            <td class="txt-right">
                                <a href="processa_admin.php?action=decide_suspensao&idCR=<?php echo $req['idCR']; ?>&decisao=aprovado" class="btn btn-primary btn-sm">✔</a>
                                <a href="processa_admin.php?action=decide_suspensao&idCR=<?php echo $req['idCR']; ?>&decisao=rejeitado" class="btn btn-cancel btn-sm">✖</a>
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

// 4. Utilizadores
function renderUsersTab($users, $searchTerm) {
    ?>
    <div class="card mb2">
        <div class="mb1 header-flex">
            <a href="admin.php?tab=users&action=create" class="btn btn-primary" style="text-decoration:none;">+ Novo Utilizador</a>
            <form action="admin.php" method="GET" class="search-form">
                <input type="hidden" name="tab" value="users">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Nome, Email..." class="profile-input input-search">
                <button type="submit" class="btn btn-primary">🔍</button>
            </form>
        </div>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Profissão / Tipo</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th class="txt-right">Ver</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u): 
                    $prof = $u['profession'] ? $u['profession'] : $u['userType'];
                    $activeText = $u['userStatus'] ? 'Ativo' : 'Inativo';
                    $badgeStyle = $u['userStatus'] ? 'alert-success' : 'alert-error';
                ?>
                    <tr>
                        <td><span class="nome-tab"><?php echo htmlspecialchars($u['name'].' '.$u['surname']); ?></span></td>
                        <td><?php echo htmlspecialchars($prof); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><span class="role-badge <?php echo $badgeStyle; ?>"><?php echo $activeText; ?></span></td>
                        <td class="txt-right">
                            <a href="admin.php?tab=users&action=view&idU=<?php echo $u['idU']; ?>" class="btn btn-header">👁️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

//Modal para associar um dosimetro a um user
function renderAssociateForm($idDA, $userName) {
    ?>
    <div class="modal-overlay-php">
        <div class="modal-box-php">
            <h3 class="titulo">Associar Dosímetro</h3>
            <p class="subtítulo mb1">Insira os dados para <?php echo htmlspecialchars($userName); ?></p>

            <form action="processa_admin.php" method="POST">
                <input type="hidden" name="action" value="associar_dosimetro">
                <input type="hidden" name="idDA" value="<?php echo $idDA; ?>">
                <label class="profile-label">Número de Série</label>
                <input type="text" name="serial" class="profile-input mb1" required autofocus>
                <label class="profile-label">Notas (Opcional)</label>
                <textarea name="notes" class="profile-input mb1"></textarea>
                <div class="modal-actions">
                    <a href="admin.php?tab=associacao" class="btn btn-cancel">Cancelar</a>
                    <button type="submit" class="btn btn-save-profile">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
    <?php
}

// Modal para trocar o dosimetro a um user
function renderSwapModal($idDA, $name) {
    ?>
    <div class="modal-overlay">
        <div class="modal-box">
            <h3 class="titulo">Trocar Dosímetro</h3>
            <p class="subtítulo mb1">Profissional: <?php echo htmlspecialchars($name); ?></p>
            <form action="processa_admin.php" method="POST">
                <input type="hidden" name="action" value="trocar_dosimetro">
                <input type="hidden" name="idDA" value="<?php echo $idDA; ?>">
                
                <label class="profile-label">Novo Nº Série</label>
                <input type="text" name="newSerial" class="profile-input mb1" required>
                
                <div class="modal-actions">
                    <a href="admin.php?tab=gestao" class="btn btn-cancel">Cancelar</a>
                    <button type="submit" class="btn btn-save-profile">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
    <?php
}

//Modal para criar um user
function renderCreateUserModal() {
    ?>
    <div class="modal-overlay">
        <div class="modal-box">
            <h3 class="titulo mb1">Novo Utilizador</h3>
            <form action="processa_admin.php" method="POST">
                <input type="hidden" name="action" value="create_user">
                
                <div class="profile-form-grid">
                    <div>
                        <label class="profile-label">Nome</label>
                        <input type="text" name="name" class="profile-input" required>
                    </div>
                    <div>
                        <label class="profile-label">Apelido</label>
                        <input type="text" name="surname" class="profile-input" required>
                    </div>
                    <div class="g2">
                        <label class="profile-label">Email</label>
                        <input type="email" name="email" class="profile-input" required>
                    </div>
                    <div>
                        <label class="profile-label">Password</label>
                        <input type="password" name="password" class="profile-input" required>
                    </div>
                    <div>
                        <label class="profile-label">Telemóvel</label>
                        <input type="text" name="phoneN" class="profile-input" required>
                    </div>
                    <div>
                        <label class="profile-label">Data Nasc.</label>
                        <input type="date" name="birthDate" class="profile-input" required>
                    </div>
                    <div>
                        <label class="profile-label">Sexo</label>
                        <select name="sex" class="profile-input">
                            <option value="Male">Masculino</option>
                            <option value="Female">Feminino</option>
                            <option value="Other">Outro</option>
                        </select>
                    </div>
                    <div class="g2">
                        <label class="profile-label">Tipo de Utilizador</label>
                        <select name="userType" class="profile-input">
                            <option value="Profissional de Saúde">Profissional de Saúde</option>
                            <option value="Físico Médico">Físico Médico</option>
                            <option value="Administrador">Administrador</option>
                        </select>
                    </div>

                    <div class="g2 hp-only-fields">
                        <p class="hp-note">Preencha apenas se for Profissional de Saúde:</p>
                        <div class="profile-form-grid" style="grid-template-columns: 1fr 1fr; gap:1rem;">
                            <div><label class="profile-label">Profissão</label><input type="text" name="profession" class="profile-input"></div>
                            <div><label class="profile-label">Departamento</label><input type="text" name="department" class="profile-input"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-actions">
                    <a href="admin.php?tab=users" class="btn btn-cancel">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Criar</button>
                </div>
            </form>
        </div>
    </div>
    <?php
}

//Modal para ver as informações de um user
function renderUserDetailModal($user) {
    if (!$user) return;
    $isActive = $user['active'] == 1;
    ?>
    <div class="modal-overlay">
        <div class="modal-box">
            <div class="modal-user-header mb1">
                <h3 class="titulo"><?php echo htmlspecialchars($user['name'].' '.$user['surname']); ?></h3>
                <span class="role-badge <?php echo $isActive?'alert-success':'alert-error'; ?>">
                    <?php echo $isActive?'Ativo':'Inativo'; ?>
                </span>
            </div>
            
            <div class="profile-form-grid mb1_5">
                <div><label class="profile-label">Email</label><input disabled value="<?php echo $user['email']; ?>" class="profile-input"></div>
                <div><label class="profile-label">Telemóvel</label><input disabled value="<?php echo $user['phoneN']; ?>" class="profile-input"></div>
                <div><label class="profile-label">Tipo</label><input disabled value="<?php echo $user['userType']; ?>" class="profile-input"></div>
                <?php if(isset($user['profession'])): ?>
                    <div><label class="profile-label">Profissão</label><input disabled value="<?php echo $user['profession']; ?>" class="profile-input"></div>
                <?php endif; ?>
            </div>

            <div class="modal-user-buttons">
                <button class="btn btn-full btn-cancel">📜 Ver Histórico Pedidos</button>
                <button class="btn btn-full btn-cancel">☢️ Ver Histórico Dosímetros</button>
            </div>

            <div class="modal-actions">
                <a href="admin.php?tab=users" class="btn btn-header">Fechar</a>
                
                <form action="processa_admin.php" method="POST" style="margin-left:auto;">
                    <input type="hidden" name="action" value="toggle_status">
                    <input type="hidden" name="idU" value="<?php echo $user['idU']; ?>">
                    <input type="hidden" name="currentStatus" value="<?php echo $user['active']; ?>">
                    <?php if($isActive): ?>
                        <button type="submit" class="btn btn-primary" style="background:var(--red);">Desativar Conta</button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary" style="background:var(--green);">Reativar Conta</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <?php
}

?>