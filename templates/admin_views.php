<?php
//Abas
function renderAdminTabs($currentTab) {
    ?>
    <div class="admin-tabs">
        <a href="admin.php?tab=associacao" class="tab-link <?php echo $currentTab === 'associacao' ? 'active' : ''; ?>">Associação de Dosímetros</a>
        <a href="admin.php?tab=gestao" class="tab-link <?php echo $currentTab === 'gestao' ? 'active' : ''; ?>">Gestão de Dosímetros</a>
        <a href="admin.php?tab=historico" class="tab-link <?php echo $currentTab === 'historico' ? 'active' : ''; ?>">Histórico de Dosímetros</a>
        <a href="admin.php?tab=pedidos" class="tab-link <?php echo $currentTab === 'pedidos' ? 'active' : ''; ?>">Pedidos de Suspensão/Ativação</a>
        <a href="admin.php?tab=users" class="tab-link <?php echo $currentTab === 'users' ? 'active' : ''; ?>">Utilizadores</a>
    </div>
    <?php
}

// 1. Associação de Dosimetros
function renderAssociationTable($pendingData, $searchTerm) {
    ?>
    <div class="card">
        <div class="mb1 header-flex">
            <div class="header-flex-left">
                <h2 class="titulo-separador">Associação de Dosímetros</h2>
                <p class="subtitulo">Dosímetros ainda por associar</p>
            </div>
            <form action="admin.php" method="GET" class="search-form">
                <input type="hidden" name="tab" value="associacao">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Pesquisar..." class="profile-input input-search">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </button>
            </form>
        </div>

        <?php if (empty($pendingData)): ?>
            <?php if (!empty($searchTerm)): ?>
                <p class="text-center msg-nav">
                    Nenhum resultado encontrado para "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>".
                </p>
            <?php else: ?>
                <p class="text-center msg-nav">Tudo em dia! Não existem associações pendentes.</p>
            <?php endif; ?>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Profissional</th>
                            <th>Prática</th>
                            <th>Tipo de dosímetro</th>
                            <th>Periocidade</th>
                            <th>Tipo de Risco</th>
                            <th class="txt-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingData as $row): ?>
                            <tr>
                                <td>
                                    <span class="nome-tab"><?php echo htmlspecialchars($row['name'] . ' ' . $row['surname']); ?></span><br>
                                    <?php echo htmlspecialchars($row['email']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['pratica']); ?></td>
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
//1. Associação de Dosímetros: Modal para associar um dosimetro a um user
function renderAssociateForm($idDA, $userName) {
    ?>
    <div class="modal-overlay">
        <div class="modal-box">
            <h3 class="titulo">Associar Dosímetro</h3>
            <p class="subtitulo mb1">Insira os dados para <?php echo htmlspecialchars($userName); ?></p>

            <form action="processa_admin.php" method="POST">
                <input type="hidden" name="action" value="associar_dosimetro">
                <input type="hidden" name="idDA" value="<?php echo $idDA; ?>">
                <label class="profile-label">Número de Série</label>
                <input type="text" name="serial" class="profile-input mb1" required autofocus>
                <div class="modal-actions">
                    <a href="admin.php?tab=associacao" class="btn btn-cancel">Cancelar</a>
                    <button type="submit" class="btn btn-save-profile">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
    <?php
}

// 2. Gestão de Dosímetros
function renderManagementTab($stats, $activeDosimeters, $searchTerm) {
    ?>
    <div class="profile-form-grid mb2">
        <div class="card card-stat-primary">
            <p class="com-laranja">Dosímetros recolhidos entre <?php echo $stats['periodo_analise']; ?></p>
            <h2 class="stat-number text-primary"><?php echo $stats['enviados']; ?></h2>
        </div>
        <div class="card card-stat-green">
            <p class="com-verde">Quantidade de dosímetros necessários para <?php echo $stats['mes_abastecimento']; ?>:</p>
            <h2 class=" stat-number text-green"><?php echo $stats['pedir']; ?></h2>
        </div>
    </div>

    <div class="card">
        <div class="mb1 header-flex">
            <h2 class="titulo-separador header-flex-left">Dosímetros Ativos</h2>
            <form action="admin.php" method="GET" class="search-form">
                <input type="hidden" name="tab" value="gestao">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Pesquisar..." class="profile-input input-search">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </button>
            </form>
        </div>
        <?php if (empty($activeDosimeters)): ?>
            <?php if (!empty($searchTerm)): ?>
                <p class="text-center msg-nav">
                    Nenhum resultado encontrado para "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>".
                </p>
            <?php else: ?>
                <p class="text-center msg-nav">Não existem dosímetros ativos no momento.</p>
            <?php endif; ?>
        <?php else: ?>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Profissional</th>
                        <th>Prática</th>
                        <th>Dosímetro</th>
                        <th>Associado em</th>
                        <th>Próxima Troca</th>
                        <th class="txt-right">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activeDosimeters as $row): 
                        $isLate = strtotime($row['nextReplacementDate']) < time();
                        $dateClass = $isLate ? 'cell-date-late' : '';
                    ?>
                        <tr>
                            <td>
                                <span class="nome-tab"><?php echo htmlspecialchars($row['name'] . ' ' . $row['surname']); ?></span><br>
                                <?php echo htmlspecialchars($row['email']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['pratica']); ?></td>
                            <td><?php echo htmlspecialchars($row['dosimeterSerial']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['assignmentDate'])); ?></td>
                            <td class="<?php echo $dateClass; ?>"><?php echo date('d/m/Y', strtotime($row['nextReplacementDate'])); ?></td>
                            <td class="txt-right">
                                <a href="admin.php?tab=gestao&action=swap&idDA=<?php echo $row['idDA']; ?>&name=<?php echo urlencode($row['name']); ?>" class="btn btn-primary">Trocar</a>
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
// 2. Gestão de Dosímetros: Modal para trocar o dosimetro a um user
function renderSwapModal($idDA, $name) {
    ?>
    <div class="modal-overlay">
        <div class="modal-box">
            <h3 class="titulo">Trocar Dosímetro</h3>
            <p class="subtitulo mb1">Profissional: <?php echo htmlspecialchars($name); ?></p>
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

// 3. Histórico de Dosímetros
function renderHistoryTab($historyData, $searchTerm) {
    ?>
    <div class="card">
        <div class="mb1 header-flex">
            <div class="header-flex-left">
                <h2 class="titulo-separador">Histórico Global de Dosímetros</h2>
                <p class="subtitulo">Lista completa de utilizações atuais e passadas.</p>
            </div>
            <form action="admin.php" method="GET" class="search-form">
                <input type="hidden" name="tab" value="historico">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Pesquisar..." class="profile-input input-search">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </button>
            </form>
        </div>

        <?php if (empty($historyData)): ?>
            <?php if (!empty($searchTerm)): ?>
                <p class="text-center msg-nav">
                    Nenhum resultado para "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>".
                </p>
            <?php else: ?>
                <p class="text-center msg-nav">Ainda não existe histórico.</p>
            <?php endif; ?>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nº Série</th>
                            <th>Profissional</th>
                            <th>Início</th>
                            <th>Fim</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historyData as $row): 
                            $isAtivo = ($row['estado'] === 'Ativo');
                        ?>
                            <tr class="<?php echo $isAtivo ? 'row-highlight' : ''; ?>">
                                <td><?php echo htmlspecialchars($row['dosimeterSerial']); ?></td>
                                <td>
                                    <span class="nome-tab"><?php echo htmlspecialchars($row['name'] . ' ' . $row['surname']); ?></span><br>
                                    <?php echo htmlspecialchars($row['email']); ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($row['assignmentDate'])); ?></td>
                                <td>
                                    <?php if ($isAtivo): ?>
                                        <span class="role-badge alert-success">Em Uso</span>
                                    <?php else: ?>
                                        <?php echo date('d/m/Y', strtotime($row['removalDate'])); ?>
                                    <?php endif; ?>
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

// 4. Pedidos de Suspensão/Ativação
function renderRequestsTab($requests, $searchTerm) {
    ?>
    <div class="card">
        <div class="mb1 header-flex">
            <h2 class="titulo-separador mb1 header-flex-left">Pedidos Pendentes</h2>
            <form action="admin.php" method="GET" class="search-form">
                <input type="hidden" name="tab" value="pedidos">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Pesquisar..." class="profile-input input-search">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </button>
            </form>
        </div>

        <?php if (empty($requests)): ?>
            <?php if (!empty($searchTerm)): ?>
                <p class="text-center msg-nav">
                    Nenhum resultado encontrado para "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>".
                </p>
            <?php else: ?>
                <p class="subtitulo">Não existem pedidos pendentes.</p>
            <?php endif; ?>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Profissional</th>
                            <th>Tipo</th>
                            <th>Data</th>
                            <th>Justificação</th>
                            <th class="txt-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td>
                                    <span class="nome-tab"><?php echo htmlspecialchars($req['name'].' '.$req['surname']); ?></span><br>
                                    <?php echo htmlspecialchars($req['email']); ?>
                                </td>
                                <td>
                                    <span class="role-badge <?php echo $req['requestType'] == 'Suspender' ? 'badge-red' : 'badge-green'; ?>">
                                        <?php echo htmlspecialchars($req['requestType']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($req['requestDate'])); ?></td>
                                <td class="subtitulo"><?php echo htmlspecialchars($req['message']); ?></td>
                                <td class="txt-right">
                                    <a href="admin.php?tab=pedidos&decidir=<?php echo $req['idCR']; ?>&user=<?php echo urlencode($req['name'].' '.$req['surname']); ?>&type=<?php echo $req['requestType']; ?>" 
                                    class="btn btn-primary">Decidir</a>
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
// 4. Pedidos de Suspensão/Ativação: Modal para Suspender/Ativar um pedido
function renderDecisionModal($idCR, $userName, $requestType) {
    ?>
    <div class="modal-overlay">
        <div class="modal-box">
            <h3 class="titulo mb1">Decidir Pedido</h3>
            <p class="subtitulo mb1">
                O utilizador <b><?php echo htmlspecialchars($userName); ?></b> pediu para: 
                <span class="role-badge <?php echo $requestType == 'Suspender' ? 'badge-red' : 'badge-green'; ?>">
                    <?php echo htmlspecialchars($requestType); ?>
                </span>
            </p>

            <form action="processa_admin.php" method="POST">
                <input type="hidden" name="action" value="decide_suspensao">
                <input type="hidden" name="idCR" value="<?php echo htmlspecialchars($idCR); ?>">

                <div class="form-group mb1_5">
                    <label class="profile-label">Nota do Administrador (Opcional)</label>
                    <textarea name="adminNote" class="profile-input" rows="3"  placeholder="Escreva o motivo da decisão..."></textarea>
                </div>

                <div class="modal-actions">
                    <a href="admin.php?tab=pedidos" class="btn btn-cancel">Cancelar</a>
                    <div>
                        <button type="submit" name="decisao" value="rejeitado" class="btn btn-no">Rejeitar</button>
                        <button type="submit" name="decisao" value="aprovado" class="btn btn-save-profile">Aprovar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
}

// 5. Utilizadores
function renderUsersTab($users, $searchTerm) {
    ?>
    <div class="card mb2">
        <div class="mb1 header-flex">
            <a href="admin.php?tab=users&action=create" class="btn btn-primary">+ Novo Utilizador</a>
            <form action="admin.php" method="GET" class="search-form">
                <input type="hidden" name="tab" value="users">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Pesquisar..." class="profile-input input-search">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </button>
            </form>
        </div>
        <?php if (empty($users)): ?>
            <?php if (!empty($searchTerm)): ?>
                <p class="text-center msg-nav">
                    Nenhum resultado encontrado para "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>".
                </p>
            <?php else: ?>
                <p class="subtitulo msg-nav">Não existem utilizadores registados.</p>
            <?php endif; ?>
        <?php else: ?>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Profissional</th>
                        <th>Profissão</th>
                        <th>Estado</th>
                        <th class="txt-right">+ Info</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): 
                        $prof = $u['profession'] ? $u['profession'] : $u['userType'];
                        $activeText = $u['userStatus'] ? 'Ativo' : 'Inativo';
                        $badgeStyle = $u['userStatus'] ? 'alert-success' : 'alert-error';
                    ?>
                        <tr>
                            <td>
                                <span class="nome-tab"><?php echo htmlspecialchars($u['name'].' '.$u['surname']); ?></span><br>
                                <?php echo htmlspecialchars($u['email']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($prof); ?></td>
                            <td><span class="role-badge <?php echo $badgeStyle; ?>"><?php echo $activeText; ?></span></td>
                            <td class="txt-right">
                                <a href="user_details.php?idU=<?php echo $u['idU']; ?>" class="btn btn-ver">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                    </svg>
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
//5. Utilizadores: Modal para criar um user
function renderCreateUserModal() {
    ?>
    <div class="modal-overlay">
        <div class="new-user">
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
                        <input type="text" name="phoneN" class="profile-input" required placeholder="+351...">
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
                        
                        <div class="type-selector-wrapper">
                            
                            <input type="radio" name="userType" value="Administrador" id="t_admin" required>
                            <label for="t_admin">Administrador</label>

                            <input type="radio" name="userType" value="Físico Médico" id="t_fisico" required>
                            <label for="t_fisico">Físico Médico</label>

                            <input type="radio" name="userType" value="Profissional de Saúde" id="type_hp" required>
                            <label for="type_hp">Profissional de Saúde</label>

                            <div class="hp-only-fields">
                                <div class="hp-fields-grid">
                                    <div>
                                        <label class="profile-label">Profissão</label>
                                        <input type="text" name="profession" class="profile-input" required placeholder="Ex: Enfermeiro">
                                    </div>
                                    <div>
                                        <label class="profile-label">Departamento</label>
                                        <input type="text" name="department" class="profile-input" required placeholder="Ex: Urgência">
                                    </div>
                                </div>
                            </div>

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

?> 