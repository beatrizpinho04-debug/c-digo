<?php
// =================================================================================
// QUERY: BUSCAR TODOS OS PEDIDOS COM TODOS OS DETALHES
// =================================================================================
$sqlTodos = "
    SELECT 
        -- Dados do Pedido Inicial
        dr.idR, dr.requestDate, dr.pratica, dr.decisionMade,
        
        -- Dados de Aprovação
        ar.idA, ar.status as statusAprovado, ar.approvalDate, ar.periodicity, ar.riskCategory, ar.dosimeterType,
        
        -- Dados de Rejeição
        rr.rejectionDate, rr.comment as motivoRejeicao,
        
        -- Quem decidiu (Físico Médico) - Pode vir do Aprovado (ar.idP) ou Rejeitado (rr.idP)
        uPhys.name AS nomeFisico, uPhys.surname AS sobrenomeFisico
        
    FROM DosimeterRequest dr
    LEFT JOIN ApprovedRequest ar ON dr.idR = ar.idR
    LEFT JOIN RejectedRequest rr ON dr.idR = rr.idR
    LEFT JOIN User uPhys ON (ar.idP = uPhys.idU OR rr.idP = uPhys.idU)
    WHERE dr.idU = :id
    ORDER BY dr.requestDate DESC
";

$stmt = $pdo->prepare($sqlTodos);
$stmt->execute(['id' => $idUsuario]);
$todosPedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card mb2">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2 class="nome" style="margin: 0;">Histórico Completo de Pedidos</h2>
        <span class="badge-gray role-badge"><?php echo count($todosPedidos); ?> Registos</span>
    </div>

    <?php if (count($todosPedidos) > 0): ?>
        
        <div class="timeline-list">
            <?php foreach ($todosPedidos as $p): ?>
                <?php
                    // Determinar Estado Geral para Cores e Ícones
                    if ($p['decisionMade'] == 0) {
                        $estado = 'Pendente';
                        $classeBorda = 'border-yellow';
                        $classeBg = 'bg-yellow-light';
                        $icon = '⏳';
                    } elseif ($p['statusAprovado']) {
                        $estado = 'Aprovado';
                        $classeBorda = 'border-blue'; // Azul para aprovados
                        $classeBg = 'bg-blue-light';
                        $icon = '✅';
                    } else {
                        $estado = 'Rejeitado';
                        $classeBorda = 'border-red';
                        $classeBg = 'bg-red-light';
                        $icon = '❌';
                    }
                ?>

                <div class="request-card <?php echo $classeBorda; ?>">
                    
                    <div class="req-header">
                        <div class="req-title">
                            <span class="req-icon"><?php echo $icon; ?></span>
                            <div>
                                <strong>Pedido de <?php echo date('d/m/Y', strtotime($p['requestDate'])); ?></strong>
                                <div class="text-muted" style="font-size: 0.85rem;">Prática: <?php echo htmlspecialchars($p['pratica']); ?></div>
                            </div>
                        </div>
                        <span class="<?php echo obterClasseEstado($estado); ?> role-badge">
                            <?php echo $estado; ?>
                        </span>
                    </div>

                    <?php if ($estado !== 'Pendente'): ?>
                        <div class="req-body">
                            <div class="divider"></div>
                            
                            <?php if ($estado === 'Aprovado'): ?>
                                <div class="details-grid">
                                    <div class="d-item">
                                        <label>Aprovado Por:</label>
                                        <span><?php echo htmlspecialchars($p['nomeFisico'] . ' ' . $p['sobrenomeFisico']); ?></span>
                                    </div>
                                    <div class="d-item">
                                        <label>Data Aprovação:</label>
                                        <span><?php echo date('d/m/Y', strtotime($p['approvalDate'])); ?></span>
                                    </div>
                                    <div class="d-item">
                                        <label>Periodicidade:</label>
                                        <span><?php echo htmlspecialchars($p['periodicity']); ?></span>
                                    </div>
                                    <div class="d-item">
                                        <label>Categoria Risco:</label>
                                        <span><?php echo htmlspecialchars($p['riskCategory']); ?></span>
                                    </div>
                                    <div class="d-item">
                                        <label>Tipo Dosímetro:</label>
                                        <span><?php echo htmlspecialchars($p['dosimeterType']); ?></span>
                                    </div>
                                    <div class="d-item">
                                        <label>Estado Atual:</label>
                                        <span class="<?php echo obterClasseEstado($p['statusAprovado']); ?> role-badge" style="font-size: 0.7rem;">
                                            <?php echo htmlspecialchars($p['statusAprovado']); ?>
                                        </span>
                                    </div>
                                </div>
                            
                            <?php elseif ($estado === 'Rejeitado'): ?>
                                <div class="details-grid">
                                    <div class="d-item">
                                        <label>Rejeitado Por:</label>
                                        <span><?php echo htmlspecialchars($p['nomeFisico'] . ' ' . $p['sobrenomeFisico']); ?></span>
                                    </div>
                                    <div class="d-item">
                                        <label>Data Rejeição:</label>
                                        <span><?php echo date('d/m/Y', strtotime($p['rejectionDate'])); ?></span>
                                    </div>
                                    <div class="d-item full-width" style="background-color: #fee2e2; padding: 0.5rem; border-radius: 4px; border-left: 3px solid #ef4444;">
                                        <label style="color: #991b1b;">Motivo:</label>
                                        <span style="color: #7f1d1d;"><?php echo htmlspecialchars($p['motivoRejeicao']); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="req-body">
                            <div class="divider"></div>
                            <p class="text-muted" style="font-size: 0.9rem; margin: 0;">
                                <em>Este pedido ainda está a aguardar análise pelo Físico Médico.</em>
                            </p>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <p class="text-muted" style="text-align: center; padding: 2rem;">Ainda não efetuou nenhum pedido.</p>
    <?php endif; ?>
</div>

