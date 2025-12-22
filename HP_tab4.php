<?php
// Query para buscar histórico de suspensões/ativações
$sqlAlteracoes = "
    SELECT 
        cr.requestDate, 
        cr.requestType, 
        cr.message, 
        cr.status, 
        cr.adminNote, 
        cr.decisionDate, 
        cr.finalStatus,
        uAdmin.name AS nomeAdmin, 
        uAdmin.surname AS apelidoAdmin
    FROM ChangeRecord cr
    LEFT JOIN User uAdmin ON cr.idAdmin = uAdmin.idU
    WHERE cr.idUser = :id
    ORDER BY cr.requestDate DESC
";
$stmt = $pdo->prepare($sqlAlteracoes);
$stmt->execute(['id' => $idUsuario]);
$alteracoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card mb2">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2 class="nome" style="margin: 0;">Histórico de Suspensões e Ativações</h2>
        <span class="badge-gray role-badge"><?php echo count($alteracoes); ?> Registos</span>
    </div>

    <?php if (count($alteracoes) > 0): ?>
        
        <div class="timeline-list">
            <?php foreach ($alteracoes as $a): ?>
                <?php
                    // Definir Cores e Ícones
                    if ($a['status'] === 'Pendente') {
                        $classeBorda = 'border-yellow';
                        $icon = '⏳';
                        $estadoDisplay = 'Pendente';
                    } elseif ($a['status'] === 'Rejeitado') {
                        $classeBorda = 'border-red';
                        $icon = '❌';
                        $estadoDisplay = 'Rejeitado';
                    } else {
                        $classeBorda = 'border-blue'; // Concluído
                        $icon = '✅';
                        $estadoDisplay = 'Concluído';
                    }
                ?>
                
                <div class="request-card <?php echo $classeBorda; ?>">
                    
                    <div class="req-header">
                        <div class="req-title">
                            <span class="req-icon"><?php echo $icon; ?></span>
                            <div>
                                <strong>Pedido de <?php echo htmlspecialchars($a['requestType']); ?></strong>
                                <div class="text-muted" style="font-size: 0.85rem;">
                                    Submetido a: <?php echo date('d/m/Y', strtotime($a['requestDate'])); ?>
                                </div>
                            </div>
                        </div>
                        <span class="<?php echo obterClasseEstado($a['status']); ?> role-badge">
                            <?php echo htmlspecialchars($estadoDisplay); ?>
                        </span>
                    </div>

                    <div class="req-body">
                        <div class="divider"></div>
                        
                        <div>
                            <label style="font-size: 0.75rem; text-transform: uppercase; color: #6b7280; font-weight: 600;">A sua justificação:</label>
                            <div class="user-justification">
                                "<?php echo htmlspecialchars($a['message']); ?>"
                            </div>
                        </div>

                        <?php if ($a['status'] !== 'Pendente'): ?>
                            <div class="admin-response-box">
                                
                                <div class="admin-response-title">
                                    Resposta da Administração
                                </div>

                                <div class="details-grid">
                                    <div class="d-item">
                                        <label>Decidido Por:</label>
                                        <span><?php echo htmlspecialchars(($a['nomeAdmin'] ?? 'Sistema') . ' ' . ($a['apelidoAdmin'] ?? '')); ?></span>
                                    </div>
                                    <div class="d-item">
                                        <label>Data Decisão:</label>
                                        <span><?php echo $a['decisionDate'] ? date('d/m/Y', strtotime($a['decisionDate'])) : '-'; ?></span>
                                    </div>
                                    <div class="d-item">
                                        <label>Resultado Final:</label>
                                        <div>
                                            <span class="<?php echo obterClasseEstado($a['finalStatus'] ?? ''); ?> role-badge">
                                                <?php echo htmlspecialchars($a['finalStatus'] ?? 'Inalterado'); ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($a['adminNote'])): ?>
                                        <div class="d-item full-width admin-note-area">
                                            <span class="admin-note-label">Nota do Administrador:</span>
                                            <span class="admin-note-text">
                                                "<?php echo htmlspecialchars($a['adminNote']); ?>"
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            </div>
                        <?php else: ?>
                            <p class="text-muted" style="font-size: 0.85rem; font-style: italic; margin-top: 1rem; margin-bottom: 0;">
                                O seu pedido aguarda análise por parte dos Serviços Administrativos.
                            </p>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <p class="text-muted" style="text-align: center; padding: 2rem;">
            Não existem registos de alterações.
        </p>
    <?php endif; ?>
</div>