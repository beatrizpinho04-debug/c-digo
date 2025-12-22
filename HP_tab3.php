<?php
// =================================================================================
// 1. QUERY: DADOS ATUAIS (Para o Destaque de Topo)
// =================================================================================
// Precisamos de saber o estado do Pedido (ApprovedRequest) para decidir se mostramos o topo
// E os dados do Dosímetro Atual (DosimeterAssignment)
$sqlAtual = "
    SELECT 
        ar.status AS estadoPedido,
        da.dosimeterSerial, 
        da.assignmentDate, 
        da.nextReplacementDate, 
        da.status AS estadoDosimetro
    FROM ApprovedRequest ar
    LEFT JOIN DosimeterAssignment da ON ar.idA = da.idA
    WHERE ar.idP = :id AND ar.status != 'Concluido'
    ORDER BY ar.approvalDate DESC LIMIT 1
";
$stmt = $pdo->prepare($sqlAtual);
$stmt->execute(['id' => $idUsuario]);
$dadosAtuais = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não houver pedido aprovado, define variáveis vazias para não dar erro
$estadoPedido = $dadosAtuais['estadoPedido'] ?? '';
$serialAtual = $dadosAtuais['dosimeterSerial'] ?? '---';

// =================================================================================
// 2. QUERY: HISTÓRICO COMPLETO (União de Passado + Presente + Pendentes)
// =================================================================================
$sqlHistorico = "
    -- 1. O REGISTO ATUAL (Seja 'Em Uso', 'Suspenso' ou 'Por_Associar')
    SELECT 
        da.dosimeterSerial, 
        da.assignmentDate AS data_inicio, 
        da.nextReplacementDate AS data_fim_prevista, -- Usamos a data de troca como referência
        NULL AS data_devolucao_real,
        da.status AS estado,
        1 AS ordem_prioridade
    FROM DosimeterAssignment da
    JOIN ApprovedRequest ar ON da.idA = ar.idA
    WHERE ar.idP = :id

    UNION ALL

    -- 2. O HISTÓRICO (Já devolvidos)
    SELECT 
        dah.dosimeterSerial, 
        dah.assignmentDate AS data_inicio, 
        NULL AS data_fim_prevista,
        dah.removalDate AS data_devolucao_real,
        'Devolvido' AS estado,
        2 AS ordem_prioridade
    FROM DosimeterAssignmentHistory dah
    JOIN ApprovedRequest ar ON dah.idA = ar.idA
    WHERE ar.idP = :id

    ORDER BY ordem_prioridade ASC, data_inicio DESC
";

$stmt = $pdo->prepare($sqlHistorico);
$stmt->execute(['id' => $idUsuario]);
$historico = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if ($estadoPedido === 'Ativo'): ?>
    <div class="cardHP mb1_5" style="background: linear-gradient(to right, #eff6ff, #ffffff); border: 1px solid #bfdbfe;">
        <h2 class="nome mb05" style="color: #1e40af;">Dosímetro em Uso</h2>
        
        <?php if ($serialAtual && $serialAtual !== '---'): ?>
            <div class="status-grid">
                <div class="info-box" style="background: white; border-color: #dbeafe;">
                    <span class="info-label">Nº de Série</span>
                    <span class="info-value" style="font-size: 1.25rem; color: #1e40af;">
                        <?php echo htmlspecialchars($serialAtual); ?>
                    </span>
                </div>

                <div class="info-box" style="background: white; border-color: #dbeafe;">
                    <span class="info-label">Data de Atribuição</span>
                    <span class="info-value">
                        <?php echo $dadosAtuais['assignmentDate'] ? date('d/m/Y', strtotime($dadosAtuais['assignmentDate'])) : '-'; ?>
                    </span>
                </div>

                <div class="info-box" style="background: white; border-color: #dbeafe;">
                    <span class="info-label" style="color: #c2410c;">Trocar até</span> <span class="info-value" style="color: #ea580c;">
                        <?php echo $dadosAtuais['nextReplacementDate'] ? date('d/m/Y', strtotime($dadosAtuais['nextReplacementDate'])) : '-'; ?>
                    </span>
                </div>
            </div>
        <?php else: ?>
            <div class="alert-container alert-info" style="background: white; color: #1e40af;">
                O seu pedido está ativo, mas aguarda a entrega física do dosímetro pelo Físico Médico.
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>


<div class="cardHP">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2 class="nome" style="margin: 0;">Histórico de Monitorização</h2>
        <span class="badge-gray role-badge"><?php echo count($historico); ?> Registos</span>
    </div>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Dosímetro</th>
                    <th>Data Atribuição</th>
                    <th>Data Fim / Troca</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($historico) > 0): ?>
                    <?php foreach ($historico as $h): ?>
                    <tr>
                        <td>
                            <?php if ($h['dosimeterSerial']): ?>
                                <span class="serial-mono"><?php echo htmlspecialchars($h['dosimeterSerial']); ?></span>
                            <?php else: ?>
                                <span class="text-muted" style="font-style: italic; font-size: 0.85rem;">A aguardar...</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php echo $h['data_inicio'] ? date('d/m/Y', strtotime($h['data_inicio'])) : '<span class="text-muted">-</span>'; ?>
                        </td>

                        <td>
                            <?php 
                            if ($h['data_devolucao_real']) {
                                // Se já foi devolvido, mostra a data real normal
                                echo date('d/m/Y', strtotime($h['data_devolucao_real']));
                            } elseif ($h['data_fim_prevista']) {
                                // Se está em uso, mostra a previsão com a classe nova 'prediction-date'
                                echo "<span class='prediction-date'>Troca: " . date('d/m/Y', strtotime($h['data_fim_prevista'])) . "</span>";
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>

                        <td>
                            <?php 
                                // Tratamento especial para nomes de estado mais bonitos
                                $estadoDisplay = $h['estado'];
                                if ($h['estado'] == 'Por_Associar') $estadoDisplay = 'Pendente';
                                if ($h['estado'] == 'Em_Uso') $estadoDisplay = 'Em Uso';
                            ?>
                            <span class="<?php echo obterClasseEstado($estadoDisplay); ?> role-badge">
                                <?php echo htmlspecialchars($estadoDisplay); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-muted" style="text-align: center; padding: 2rem;">
                            Ainda não existem registos de dosímetros.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>