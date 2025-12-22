<?php
// ... (O teu código PHP de queries mantém-se igual no topo) ...
$sqlHP = "SELECT profession, department FROM HealthProfessional WHERE idU = :id";
$stmt = $pdo->prepare($sqlHP);
$stmt->execute(['id' => $idUsuario]);
$hp = $stmt->fetch(PDO::FETCH_ASSOC);

$profModal = $hp['profession'] ?? 'Não definido';
$depModal = $hp['department'] ?? 'Não definido';

// Query Último Pedido
$sqlUltimo = "
    SELECT 
        dr.idR, dr.requestDate, dr.pratica, dr.decisionMade,
        ar.idA, ar.status AS statusAprovado, ar.approvalDate, ar.riskCategory, ar.dosimeterType, ar.periodicity,
        rr.rejectionDate, rr.comment AS motivoRejeicao,
        uPhys.name AS nomeFisico, uPhys.surname AS sobrenomeFisico,
        da.dosimeterSerial, da.assignmentDate, da.nextReplacementDate, da.status AS statusDosimetro
    FROM DosimeterRequest dr
    LEFT JOIN ApprovedRequest ar ON dr.idR = ar.idR
    LEFT JOIN RejectedRequest rr ON dr.idR = rr.idR
    LEFT JOIN User uPhys ON (ar.idP = uPhys.idU OR rr.idP = uPhys.idU)
    LEFT JOIN DosimeterAssignment da ON ar.idA = da.idA
    WHERE dr.idU = :id
    ORDER BY dr.requestDate DESC LIMIT 1
";
$stmt = $pdo->prepare($sqlUltimo);
$stmt->execute(['id' => $idUsuario]);
$ultimo = $stmt->fetch(PDO::FETCH_ASSOC);

// Determinar Estado
$estadoDash = 'Novo'; 
if ($ultimo) {
    if ($ultimo['decisionMade'] == 0) $estadoDash = 'Pendente';
    elseif ($ultimo['idA']) $estadoDash = 'Aprovado';
    else $estadoDash = 'Rejeitado';
}
?>

<?php if ($estadoDash === 'Novo' || $estadoDash === 'Rejeitado'): ?>
    
    <div class="cardHP mb1_5">
        <h2 class="nome mb05">O Meu Perfil Profissional</h2>
        <div class="dashboard-grid">
            <div class="dash-info-box">
                <span class="dash-label">Serviço / Departamento</span>
                <span class="dash-value"><?php echo htmlspecialchars($depModal); ?></span>
            </div>
            <div class="dash-info-box">
                <span class="dash-label">Função</span>
                <span class="dash-value"><?php echo htmlspecialchars($profModal); ?></span>
            </div>
            <div class="dash-info-box">
                <span class="dash-label">Estado Atual</span>
                <div>
                    <span class="badge-gray role-badge big-badge">Sem Pedido Ativo</span>
                </div>
            </div>
        </div>
    </div>

    <div class="cardHP mb1_5" style="text-align: center; padding: 3rem; background-color: #f8fafc; border: 2px dashed #cbd5e1; margin-bottom: 2rem;">
        <h2 class="nome mb05">Solicitar uso de Dosímetro</h2>
        <p class="text-muted mb1_5">Necessita de monitorização radiológica para exercer as suas funções?</p>
        <button class="btn btn-primary" onclick="abrirModal()">+ Preencher Pedido</button>
    </div>

    <?php if ($estadoDash === 'Rejeitado'): ?>
        <div class="cardHP mb1_5" style="border-left: 5px solid #ef4444;">
            <h2 class="nome mb05" style="color: #991b1b;">Último Pedido Rejeitado</h2>
            <div class="dashboard-grid">
                <div class="dash-info-box">
                    <span class="dash-label">Prática</span>
                    <span class="dash-value"><?php echo htmlspecialchars($ultimo['pratica']); ?></span>
                </div>
                <div class="dash-info-box">
                    <span class="dash-label">Motivo da Rejeição</span>
                    <span class="dash-value" style="color: #dc2626; font-size: 1rem;">
                        <?php echo htmlspecialchars($ultimo['motivoRejeicao']); ?>
                    </span>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>


<?php if ($estadoDash === 'Pendente'): ?>
    <div class="cardHP mb1_5">
        <h2 class="nome mb05">Pedido em Análise</h2>
        <div class="alert-container alert-info" style="margin-bottom: 1.5rem; background-color: #eff6ff; color: #1e40af; padding: 1rem; border-radius: 6px; border: 1px solid #dbeafe;">
            O seu pedido foi enviado e encontra-se a aguardar aprovação.
        </div>
        
        <div class="dashboard-grid">
            <div class="dash-info-box">
                <span class="dash-label">Data Envio</span>
                <span class="dash-value"><?php echo date('d/m/Y', strtotime($ultimo['requestDate'])); ?></span>
            </div>
            <div class="dash-info-box">
                <span class="dash-label">Prática Declarada</span>
                <span class="dash-value"><?php echo htmlspecialchars($ultimo['pratica']); ?></span>
            </div>
            <div class="dash-info-box">
                <span class="dash-label">Estado</span>
                <div><span class="badge-yellow role-badge big-badge">Pendente</span></div>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php if ($estadoDash === 'Aprovado'): ?>
    <div class="cardHP mb1_5">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
            <div>
                <h2 class="nome" style="margin: 0;">O Meu Pedido Atual</h2>
            </div>
            
            <?php if ($ultimo['statusAprovado'] === 'Ativo'): ?>
                <form action="" method="POST" style="margin:0;">
                    <input type="hidden" name="action" value="suspender_pedido">
                    <button class="btn btn-cancel" style="border-color: var(--red); color: var(--red);">
                        Pedir Suspensão
                    </button>
                </form>
            <?php elseif ($ultimo['statusAprovado'] === 'Suspenso'): ?>
                <form action="" method="POST" style="margin:0;">
                    <input type="hidden" name="action" value="ativar_pedido">
                    <button class="btn btn-primary">
                        Pedir Ativação
                    </button>
                </form>
            <?php endif; ?>
        </div>
        
        <div class="dashboard-grid">
            <div class="dash-info-box">
                <span class="dash-label">Prática:</span>
                <span class="dash-value"style="font-weight: 800; color: #000;">
                    <?php echo htmlspecialchars($ultimo['pratica']); ?>
                </span>
            </div>
            
            <div class="dash-info-box">
                <span class="dash-label">Aprovado por: </span>
                <span class="dash-value" style="font-weight: 800; color: #000;">
                     <?php echo htmlspecialchars($ultimo['nomeFisico'] . ' ' . $ultimo['sobrenomeFisico']); ?>
                </span>
                <span style="font-size: 0.8rem; color: #64748b; margin-top: 4px;">
                    em <?php echo date('d/m/Y', strtotime($ultimo['approvalDate'])); ?>
                </span>
            </div>
            
            <div class="dash-info-box">
                <span class="dash-label">Estado do Pedido</span>
                <div>
                    <span class="<?php echo obterClasseEstado($ultimo['statusAprovado']); ?> role-badge big-badge">
                        <?php echo htmlspecialchars($ultimo['statusAprovado']); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="tech-details-box">
            <span class="tech-title">Detalhes Técnicos:</span>
            Categoria <strong><?php echo htmlspecialchars($ultimo['riskCategory']); ?></strong> &nbsp;|&nbsp; 
            Tipo: <strong><?php echo htmlspecialchars($ultimo['dosimeterType']); ?></strong> &nbsp;|&nbsp; 
            Periodicidade: <strong><?php echo htmlspecialchars($ultimo['periodicity']); ?></strong>
        </div>

        <h3 class="section-title">Dosímetro Atribuído</h3>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nº Série</th>
                        <th>Data Atribuição</th>
                        <th>Próxima Troca</th>
                        <th>Estado Equipamento</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong style="font-family: monospace; font-size: 1rem; color: #374151;">
                                <?php echo htmlspecialchars($ultimo['dosimeterSerial'] ?? 'Por Associar'); ?>
                            </strong>
                        </td>
                        <td><?php echo $ultimo['assignmentDate'] ? date('d/m/Y', strtotime($ultimo['assignmentDate'])) : '-'; ?></td>
                        <td><?php echo $ultimo['nextReplacementDate'] ? date('d/m/Y', strtotime($ultimo['nextReplacementDate'])) : '-'; ?></td>
                        <td>
                            <?php $stDos = $ultimo['statusDosimetro'] ?? 'Pendente'; ?>
                            <span class="<?php echo obterClasseEstado($stDos); ?> role-badge">
                                <?php echo htmlspecialchars($stDos); ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<div id="modalPedido" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Solicitar Dosímetro</h3>
            <span class="close-modal" onclick="fecharModal()">&times;</span>
        </div>
        <form action="" method="POST">
            <input type="hidden" name="action" value="novo_pedido">
            <div class="modal-body">
                <div class="form-group">
                    <label>Profissão</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($profModal); ?>" readonly disabled style="background: #f3f4f6;">
                </div>
                <div class="form-group">
                    <label>Departamento</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($depModal); ?>" readonly disabled style="background: #f3f4f6;">
                </div>
                <div class="form-group">
                    <label>Data do Pedido</label>
                    <input type="text" class="form-control" value="<?php echo date('d/m/Y'); ?>" readonly disabled style="background: #f3f4f6;">
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label for="pratica">Prática / Atividade <span style="color:red">*</span></label>
                    <input type="text" id="pratica" name="pratica" class="form-control" placeholder="Ex: Radiologia de Intervenção" required>
                    <small class="text-muted">Descreva a atividade onde será usado o dosímetro.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" onclick="fecharModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Submeter Pedido</button>
            </div>
        </form>
    </div>
</div>