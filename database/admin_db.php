<?php

// Função para buscar pedidos aprovados que ainda não têm dosímetro (status = 'Por_Associar')
function getPendingAssociations($db) {
    $sql = "
        SELECT 
            DA.idDA,
            U.name,
            U.surname,
            AR.dosimeterType,
            AR.periodicity,
            AR.riskCategory
        FROM DosimeterAssignment DA
        JOIN ApprovedRequest AR ON DA.idA = AR.idA
        JOIN User U ON AR.idP = U.idU
        WHERE DA.status = 'Por_Associar'
        ORDER BY AR.approvalDate ASC
    ";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}
?>