-- 1. Inserir Users
INSERT INTO User (name, surname, birthDate, sex, email, password, phoneN, userType, userStatus)
VALUES 
('Alice','Admin','1980-01-01','Female','alice.admin@example.com','$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y','+351912345678','Administrador',1),
('Bob','Doctor','1985-05-10','Male','bob.physicist@example.com','$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y','+351923456789','Físico Médico',1),
('Carol','Physician','1990-02-15','Female','carol.physicist@example.com','$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y','+351934567891','Físico Médico',1),
('Dan','Nurse','1990-07-20','Male','dan.nurse@example.com','$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y','+351945678902','Profissional de Saúde',1),
('Eve','Nurse','1988-03-15','Female','eve.nurse@example.com','$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y','+351956789013','Profissional de Saúde',1),
('Frank','Nurse','1992-08-10','Male','frank.nurse@example.com','$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y','+351967890124','Profissional de Saúde',1);

-- ===============================================
-- 2. Definir Health Professionals
INSERT INTO HealthProfessional (idU, profession, department)
VALUES
(4,'Enfermeiro','Cardiologia'),
(5,'Enfermeiro','Radiologia'),
(6,'Enfermeiro','Oncologia');

-- ===============================================
-- 3. Fazer pedidos de dosímetro
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade)
VALUES
(4,'Operação cardíaca', DATETIME('now','-5 days'),0), -- Dan
(5,'Exame raio-x', DATETIME('now','-3 days'),0),       -- Eve
(6,'Radioterapia', DATETIME('now','-1 days'),0),       -- Frank
(2,'Física Médica', DATETIME('now','-10 days'),0); -- Bob

-- ===============================================
-- 4. Aprovar pedidos de dosímetro
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status)
VALUES
(1,2,DATETIME('now','-9 days'),'Mensal','A','Corpo Inteiro','Ativo'), -- Pedido do Dan aprovado pelo Bob
(2,3,DATETIME('now','-7 days'),'Mensal','B','Extremidade','Ativo'), -- Pedido da Eve aprovado pela Carol
(4,2,DATETIME('now','-10 days'),'Mensal','A','Corpo Inteiro','Ativo'); -- Pedido do Bob aprovado por ele mesmo

-- ===============================================
-- 5. Rejeitar pedidos de dosímetro
-- Pedido de Frank rejeitado pela Carol
INSERT INTO RejectedRequest (idR, idP, rejectionDate, comment)
VALUES
(3,3,DATETIME('now','-5 days'),'Informações incompletas');

-- -- ===============================================
-- 6. DOSIMETER ASSIGNMENTS e Dosimeter Assignment history
-- Dosímetro atribuído ao pedido aprovado do Daniel
UPDATE DosimeterAssignment
SET 
    dosimeterSerial = 'DX-1001',
    assignmentDate = DATE('now'),
    nextReplacementDate = DATE('now', '+30 days'),
    periodicity = 'Mensal',
    status = 'Em_Uso',
    notes = 'Primeiro ciclo'
WHERE idA = 1;
-- Dosímetro atribuído ao pedido aprovado do Bob    
UPDATE DosimeterAssignment
SET 
    dosimeterSerial = 'DX-1001',
    assignmentDate = DATE('now','-7 days'),
    nextReplacementDate = DATE('now','+23 days'),
    periodicity = 'Mensal',
    status = 'Em_Uso',
    notes = 'Primeiro ciclo'
WHERE idA = 1;
UPDATE DosimeterAssignment
SET 
    dosimeterSerial = 'DX-2001',
    assignmentDate = DATE('now','-9 days'),
    nextReplacementDate = DATE('now','+21 days'),
    periodicity = 'Mensal',
    status = 'Em_Uso',
    notes = 'Primeiro ciclo'
WHERE idA = 3;
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, insertDate)
VALUES
(1,'DX-1001',DATETIME('now','-7 days')),
(3,'DX-2001',DATETIME('now','-9 days'));

-- ===============================================
-- 7. CHANGE RECORDS
-- Pedido de suspensão feito por Daniel
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status)
VALUES
(1,4,'Suspender','Solicito suspensão temporária',DATETIME('now','-2 days'),'Pendente');

-- Decisão do Admin
UPDATE ChangeRecord
SET 
    status = 'Concluído',
    idAdmin = 1,
    decisionDate = DATE('now','-2 days'),
    finalStatus = 'Suspenso'
WHERE idCR = 1;
UPDATE ApprovedRequest
SET 
    status = 'Suspenso'
WHERE idA = 1;

-- -- Pedido de ativação feito por Daniel
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status)
VALUES
(1,4,'Ativar','Solicito reativação',DATETIME('now','-2 days'),'Pendente');

-- Decisão do Admin
UPDATE ChangeRecord
SET 
    status = 'Concluído',
    idAdmin = 1,
    decisionDate = DATE('now'),
    finalStatus = 'Ativo'
WHERE idCR = 2;
UPDATE ApprovedRequest
SET 
    status = 'Ativo'
WHERE idA = 1;
