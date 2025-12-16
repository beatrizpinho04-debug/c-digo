UPDATE DosimeterAssignment
SET status = 'Por_Associar',
    dosimeterSerial = NULL,
    assignmentDate = NULL,
    nextReplacementDate = NULL
WHERE idDA IN (
    SELECT DA.idDA
    FROM DosimeterAssignment DA
    JOIN ApprovedRequest AR ON DA.idA = AR.idA
    JOIN User U ON AR.idP = U.idU
    WHERE U.name = 'Carol'
);