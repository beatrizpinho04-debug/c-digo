<?php
function renderUserDetailsPage($user, $subtab, $data) {
    if (!$user) return;
    $isActive = $user['userStatus'] == 1;
    $prof = isset($user['profession']) ? $user['profession'] : $user['userType'];
    ?>
    
    <div class="card mb2">
        <div class="header-flex" style="justify-content: flex-start; gap: 2rem;">
            <div class="foto-circle-large">
                <img src="<?php echo htmlspecialchars($user['profilePic']); ?>" alt="Foto">
            </div>
            
            <div style="flex: 1;">
                <div style="display:flex; align-items:center; gap:1rem; margin-bottom:0.5rem;">
                    <h2 class="titulo" style="margin:0;"><?php echo htmlspecialchars($user['name'].' '.$user['surname']); ?></h2>
                    <span class="role-badge <?php echo $isActive?'alert-success':'alert-error'; ?>">
                        <?php echo $isActive?'Ativo':'Inativo'; ?>
                    </span>
                </div>
                <p class="subtítulo"><?php echo htmlspecialchars($prof); ?> | <?php echo htmlspecialchars($user['email']); ?></p>
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

    <div class="admin-tabs" style="margin-bottom: 1.5rem;">
        <a href="user_details.php?idU=<?php echo $user['idU']; ?>&subtab=info" 
           class="tab-link <?php echo $subtab === 'info' ? 'active' : ''; ?>">Informações</a>
        
        <a href="user_details.php?idU=<?php echo $user['idU']; ?>&subtab=pedidos" 
           class="tab-link <?php echo $subtab === 'pedidos' ? 'active' : ''; ?>">Pedidos</a>
        
        <a href="user_details.php?idU=<?php echo $user['idU']; ?>&subtab=dosimetros" 
           class="tab-link <?php echo $subtab === 'dosimetros' ? 'active' : ''; ?>">Histórico Dosímetros</a>
           
        <a href="user_details.php?idU=<?php echo $user['idU']; ?>&subtab=suspensoes" 
           class="tab-link <?php echo $subtab === 'suspensoes' ? 'active' : ''; ?>">Histórico Suspensões</a>
    </div>

    <div class="card">
        
        <?php if ($subtab === 'info'): ?>
            <h3 class="titulo-separador mb1">Dados Pessoais</h3>
            <div class="profile-form-grid" style="grid-template-columns: 1fr 1fr;">
                <div><label class="profile-label">Nome Completo</label><input disabled value="<?php echo htmlspecialchars($user['name'].' '.$user['surname']); ?>" class="profile-input"></div>
                <div><label class="profile-label">Email</label><input disabled value="<?php echo htmlspecialchars($user['email']); ?>" class="profile-input"></div>
                <div><label class="profile-label">Telemóvel</label><input disabled value="<?php echo htmlspecialchars($user['phoneN']); ?>" class="profile-input"></div>
                <div><label class="profile-label">Data Nasc.</label><input disabled value="<?php echo htmlspecialchars($user['birthDate']); ?>" class="profile-input"></div>
                <div><label class="profile-label">Sexo</label><input disabled value="<?php echo htmlspecialchars($user['sex']); ?>" class="profile-input"></div>
                <div><label class="profile-label">Tipo</label><input disabled value="<?php echo htmlspecialchars($user['userType']); ?>" class="profile-input"></div>
                <?php if(isset($user['department'])): ?>
                    <div><label class="profile-label">Departamento</label><input disabled value="<?php echo htmlspecialchars($user['department']); ?>" class="profile-input"></div>
                <?php endif; ?>
            </div>

        <?php elseif ($subtab === 'pedidos'): ?>
            <h3 class="titulo-separador mb1">Histórico de Pedidos</h3>
            <div class="table-container" style="border:none;">
            <table class="admin-table">
                <thead><tr><th>Data</th><th>Prática</th><th>Estado</th><th>Detalhes</th></tr></thead>
                <tbody>
                    <?php foreach($data as $r): 
                        $status = $r['decisionMade'] == 0 ? 'Pendente' : ($r['statusAprovacao'] ? 'Aprovado' : 'Rejeitado');
                        $badge = $status == 'Aprovado' ? 'alert-success' : ($status == 'Rejeitado' ? 'alert-error' : 'badge-purple');
                    ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($r['requestDate'])); ?></td>
                        <td><?php echo htmlspecialchars($r['pratica']); ?></td>
                        <td><span class="role-badge <?php echo $badge; ?>"><?php echo $status; ?></span></td>
                        <td class="subtítulo">
                            <?php if($status=='Rejeitado') echo "Motivo: ".$r['comment']; ?>
                            <?php if($status=='Aprovado') echo "Aprovado em: ".date('d/m/Y', strtotime($r['approvalDate'])); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($data)) echo "<tr><td colspan='4'>Sem registos.</td></tr>"; ?>
                </tbody>
            </table>
            </div>

        <?php elseif ($subtab === 'dosimetros'): ?>
            <h3 class="titulo-separador mb1">Histórico de Dosímetros</h3>
            <div class="table-container" style="border:none;">
            <table class="admin-table">
                <thead><tr><th>Serial</th><th>Data Atribuição</th><th>Estado</th></tr></thead>
                <tbody>
                    <?php foreach($data as $d): ?>
                    <tr>
                        <td style="font-family:monospace; font-weight:bold;"><?php echo htmlspecialchars($d['dosimeterSerial']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($d['insertDate'])); ?></td>
                        <td>
                            <?php if($d['type'] == 'Ativo'): ?>
                                <span class="role-badge alert-success">Em Uso (Atual)</span>
                            <?php else: ?>
                                <span class="subtítulo">Devolvido/Trocado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($data)) echo "<tr><td colspan='3'>Sem registos.</td></tr>"; ?>
                </tbody>
            </table>
            </div>

        <?php elseif ($subtab === 'suspensoes'): ?>
            <h3 class="titulo-separador mb1">Registos de Suspensão/Ativação</h3>
            <div class="table-container" style="border:none;">
            <table class="admin-table">
                <thead><tr><th>Data Pedido</th><th>Tipo</th><th>Motivo</th><th>Decisão</th><th>Nota Admin</th></tr></thead>
                <tbody>
                    <?php foreach($data as $c): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($c['requestDate'])); ?></td>
                        <td><?php echo htmlspecialchars($c['requestType']); ?></td>
                        <td><?php echo htmlspecialchars($c['message']); ?></td>
                        <td>
                            <?php if($c['status'] == 'Pendente'): ?>
                                <span class="role-badge badge-purple">Pendente</span>
                            <?php else: ?>
                                <?php echo htmlspecialchars($c['status']); ?> 
                                <span style="font-size:0.75em; color:var(--muted);">
                                    (<?php echo date('d/m', strtotime($c['decisionDate'])); ?>)
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="subtítulo"><?php echo htmlspecialchars($c['adminNote']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($data)) echo "<tr><td colspan='5'>Sem registos.</td></tr>"; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>

    </div>
    <?php
}
?>