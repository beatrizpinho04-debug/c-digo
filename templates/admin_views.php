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

function renderAssociationTable($pendingData) {
    ?>
    <div class="card">
        <div style="margin-bottom: 1.5rem;">
            <h2 style="font-size:1.25rem; font-weight:600;">Associação de Dosímetros</h2>
            <p class="subtítulo">Dosímetros ainda por associar a pedidos aprovados</p>
        </div>

        <?php if (empty($pendingData)): ?>
            <div class="alert-container alert-success" style="justify-content:center;">✅ Tudo em dia!</div>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Profissional</th>
                            <th>Tipo de dosímetro</th>
                            <th>Periocidade</th>
                            <th>Tipo de Risco</th>
                            <th style="text-align:right;">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingData as $row): ?>
                            <tr>
                                <td><span style="font-weight:600; color:var(--text-main);"><?php echo htmlspecialchars($row['name'] . ' ' . $row['surname']); ?></span></td>
                                <td><?php echo htmlspecialchars($row['dosimeterType']); ?></td>
                                <td><?php echo htmlspecialchars($row['periodicity']); ?></td>
                                <td><span class="role-badge badge-gray"><?php echo htmlspecialchars($row['riskCategory']); ?></span></td>
                                <td style="text-align:right;">
                                    <a href="admin.php?tab=associacao&associar=<?php echo $row['idDA']; ?>&user=<?php echo urlencode($row['name'] . ' ' . $row['surname']); ?>" 
                                       class="btn btn-primary" 
                                       style="padding: 0.4rem 0.8rem; font-size:0.8rem; text-decoration:none;">
                                        Associar
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


function renderAssociateForm($idDA, $userName) {
    ?>
    <div class="modal-overlay-php">
        <div class="modal-box-php">
            <h3 style="font-size:1.25rem; font-weight:bold; margin-bottom:0.5rem;">Associar Dosímetro</h3>
            <p style="font-size:0.875rem; color:var(--muted); margin-bottom:1.5rem;">
                Insira os dados para <span style="font-weight:bold; color:var(--foreground);"><?php echo htmlspecialchars($userName); ?></span>.
            </p>

            <form action="processa_admin.php" method="POST">
                <input type="hidden" name="action" value="associar_dosimetro">
                <input type="hidden" name="idDA" value="<?php echo htmlspecialchars($idDA); ?>">

                <div class="form-group mb1">
                    <label for="serial" class="profile-label">Número de Série</label>
                    <input type="text" name="serial" id="serial" class="profile-input" required placeholder="Ex: 10203040" autofocus>
                </div>

                <div class="form-group mb1_5">
                    <label for="notes" class="profile-label">Notas <span style="font-weight:normal; color:var(--muted);">(Opcional)</span></label>
                    <textarea name="notes" id="notes" class="profile-input" style="height: auto; min-height: 80px; resize: vertical; padding: 0.5rem;"></textarea>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                    <a href="admin.php?tab=associacao" class="btn btn-cancel" style="text-decoration:none; display:flex; align-items:center;">Cancelar</a>
                    <button type="submit" class="btn btn-save-profile">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
    <?php
}
?>