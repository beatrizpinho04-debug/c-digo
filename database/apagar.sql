PRAGMA foreign_keys = OFF; -- desativa temporariamente as FK para facilitar limpeza

DELETE FROM DosimeterAssignmentHistory;
DELETE FROM DosimeterAssignment;
DELETE FROM ChangeRecord;
DELETE FROM RejectedRequest;
DELETE FROM ApprovedRequest;
DELETE FROM DosimeterRequest;
DELETE FROM HealthProfessional;
DELETE FROM User;

PRAGMA foreign_keys = ON; -- reativa FK