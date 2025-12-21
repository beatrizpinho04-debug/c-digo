PRAGMA FOREIGN KEY = OFF;
PRAGMA PRIMARY KEY = OFF;

DELETE FROM ChangeRecord;
DELETE FROM DosimeterAssignmentHistory;
DELETE FROM DosimeterAssignment;
DELETE FROM RejectedRequest;
DELETE FROM ApprovedRequest;
DELETE FROM DosimeterRequest;
DELETE FROM HealthProfessional;
DELETE FROM User;
DELETE FROM sqlite_sequence;

PRAGMA FOREIGN KEY = ON;
PRAGMA PRIMARY KEY = ON;

-- 1. UTILIZADORES
INSERT INTO User (name, surname, birthDate, sex, email, password, phoneN, userType, userStatus, profilePic) VALUES
('Ana', 'Silva', '1980-05-12', 'Female', 'admin@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000001', 'Administrador', 1, 'foto/1.png'),
('João', 'Pinho', '1985-02-15', 'Male', 'joao@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000003', 'Físico Médico', 1, 'foto/2.png'),
('Carlos', 'Vieira', '1988-01-10', 'Male', 'carlos@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000005', 'Profissional de Saúde', 1, 'foto/3.png'),
('Inês', 'Rocha', '1992-06-25', 'Female', 'ines@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000006', 'Profissional de Saúde', 1, 'foto/4.png'),
('Paulo', 'Morgado', '1982-03-15', 'Male', 'paulo@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000007', 'Profissional de Saúde', 1, 'foto/5.png'),
('Sofia', 'Almeida', '1995-09-01', 'Female', 'sofia@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000008', 'Profissional de Saúde', 1, 'foto/6.png'),
('Bruno', 'Costa', '1990-12-12', 'Male', 'bruno@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000009', 'Profissional de Saúde', 1, 'foto/7.png'),
('Lara', 'Sousa', '1998-04-04', 'Female', 'lara@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000010', 'Profissional de Saúde', 1, 'foto/8.png'),
('Rui', 'Tavares', '1985-07-07', 'Male', 'rui@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000011', 'Profissional de Saúde', 1, 'foto/9.png'),
('Marta', 'Lima', '1985-05-05', 'Female', 'marta@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000099', 'Profissional de Saúde', 0, 'foto/10.png'),
('Tiago', 'Persistente', '1993-02-02', 'Male', 'tiago@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000022', 'Profissional de Saúde', 1, 'foto/12225881.png'),
('Vera', 'Ciclos', '1989-08-08', 'Female', 'vera@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000033', 'Profissional de Saúde', 1, 'foto/12225881.png');

INSERT INTO HealthProfessional (idU, profession, department) VALUES
(3, 'Técnico', 'Radiologia'), (4, 'Enfermeira', 'Bloco'), (5, 'Médico', 'Cardio'), 
(6, 'Dentista', 'Estomatologia'), (7, 'Técnico', 'Nuclear'), (8, 'Investigadora', 'Lab'),
(9, 'Enfermeiro', 'Urgência'), (10, 'Enfermeira', 'Urgência'),
(11, 'Estagiário', 'Radiologia'), (12, 'Técnica', 'Medicina Nuclear');

-- 2. PEDIDOS E APROVAÇÕES
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (3, 'Raio-X', DATETIME('now', '-6 months'), 1); 
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (1, 2, DATETIME('now', '-6 months', '+2 days'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (4, 'Bloco', DATETIME('now', '-4 months'), 1); 
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (2, 2, DATETIME('now', '-4 months', '+2 days'), 'Trimestral', 'B', 'Extremidade', 'Ativo');

INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (5, 'Hemodinâmica', DATETIME('now', '-2 months'), 1); 
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (3, 2, DATETIME('now', '-2 months', '+2 days'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (6, 'Dentária', DATETIME('now', '-1 day'), 1); 
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (4, 2, DATETIME('now', '-1 day', '+2 days'), 'Trimestral', 'B', 'Corpo Inteiro', 'Ativo');

INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (7, 'Nuclear', DATETIME('now'), 1); 
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (5, 2, DATETIME('now'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (8, 'Investigação', DATETIME('now', '-1 year'), 1); 
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (6, 2, DATETIME('now', '-1 year', '+2 days'), 'Trimestral', 'A', 'Extremidade', 'Suspenso');

INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (9, 'Urgência', DATETIME('now', '-1 year'), 1); 
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (7, 2, DATETIME('now', '-1 year', '+2 days'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (10, 'Urgência', DATETIME('now', '-2 years'), 1); 
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (8, 2, DATETIME('now', '-2 years', '+2 days'), 'Trimestral', 'B', 'Corpo Inteiro', 'Suspenso');

-- Tiago
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (11, 'Estágio', DATETIME('now', '-1 year'), 1);
INSERT INTO RejectedRequest (idR, idP, rejectionDate, comment) VALUES (9, 2, DATETIME('now', '-1 year', '+2 days'), 'Falta certificado.');
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (11, 'Estágio II', DATETIME('now', '-10 months'), 1);
INSERT INTO RejectedRequest (idR, idP, rejectionDate, comment) VALUES (10, 2, DATETIME('now', '-10 months', '+2 days'), 'Prática errada.');
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (11, 'Estágio Final', DATETIME('now', '-9 months'), 1); 
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (11, 2, DATETIME('now', '-9 months'), 'Mensal', 'B', 'Corpo Inteiro', 'Ativo');

-- Vera
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (12, 'MN', DATETIME('now', '-2 years'), 1); 
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (12, 2, DATETIME('now', '-2 years', '+2 days'), 'Mensal', 'A', 'Corpo Inteiro', 'Suspenso');


-- =================================================================================
-- 3. ASSOCIAÇÕES (Status Atual)
-- =================================================================================

UPDATE DosimeterAssignment SET dosimeterSerial='M-CARLOS-01', assignmentDate=DATETIME('now', '-15 days'), nextReplacementDate=DATETIME('now', '+15 days'), status='Em_Uso', periodicity='Mensal' WHERE idA=1;
UPDATE DosimeterAssignment SET dosimeterSerial='T-INES-01', assignmentDate=DATETIME('now', '-75 days'), nextReplacementDate=DATETIME('now', '+15 days'), status='Em_Uso', periodicity='Trimestral' WHERE idA=2;
UPDATE DosimeterAssignment SET dosimeterSerial='M-PAULO-LATE', assignmentDate=DATETIME('now', '-60 days'), nextReplacementDate=DATETIME('now', '-30 days'), status='Em_Uso', periodicity='Mensal' WHERE idA=3;
UPDATE DosimeterAssignment SET dosimeterSerial='M-BRUNO-NEW', assignmentDate=DATETIME('now'), nextReplacementDate=DATETIME('now', '+30 days'), status='Em_Uso', periodicity='Mensal' WHERE idA=5;
UPDATE DosimeterAssignment SET dosimeterSerial=NULL, assignmentDate=NULL, nextReplacementDate=NULL, status='Suspenso', periodicity='Trimestral' WHERE idA=6;
UPDATE DosimeterAssignment SET dosimeterSerial='M-RUI-05', assignmentDate=DATETIME('now'), nextReplacementDate=DATETIME('now', '+30 days'), status='Em_Uso', periodicity='Mensal' WHERE idA=7;
UPDATE DosimeterAssignment SET dosimeterSerial=NULL, assignmentDate=NULL, nextReplacementDate=NULL, status='Suspenso', periodicity='Trimestral' WHERE idA=8;
UPDATE DosimeterAssignment SET dosimeterSerial='M-TIAGO-09', assignmentDate=DATETIME('now', '-20 days'), nextReplacementDate=DATETIME('now', '+10 days'), status='Em_Uso', periodicity='Mensal' WHERE idA=9;
UPDATE DosimeterAssignment SET dosimeterSerial=NULL, assignmentDate=NULL, nextReplacementDate=NULL, status='Suspenso', periodicity='Mensal' WHERE idA=10;


-- =================================================================================
-- 4. HISTÓRICO BASE
-- =================================================================================

-- 1. Rui: Trocou hoje.
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) 
VALUES (7, 'M-RUI-REC', DATETIME('now', '-30 days'), DATETIME('now')); 

-- 2. Lara: Suspensa hoje.
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) 
VALUES (6, 'T-LARA-REC', DATETIME('now', '-6 months'), DATETIME('now'));

-- 3. Marta: Suspensa hoje.
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) 
VALUES (8, 'T-MARTA-REC', DATETIME('now', '-1 year'), DATETIME('now'));

-- Histórico Antigo
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) 
VALUES (7, 'M-RUI-01', DATETIME('now', '-185 days'), DATETIME('now', '-155 days'));

INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) 
VALUES (9, 'M-TIAGO-01', DATETIME('now', '-10 months'), DATETIME('now', '-9 months'));

INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) 
VALUES (10, 'M-VERA-01', DATETIME('now', '-25 months'), DATETIME('now', '-24 months'));


-- =================================================================================
-- 5. DADOS EXTRA: SIMULAÇÃO DE REUTILIZAÇÃO (CASOS ESPECIAIS)
-- =================================================================================

-- CASO A: O Rui usou o MESMO dosímetro ('M-RUI-MULTI') em 3 anos diferentes.
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate)
VALUES (7, 'M-RUI-MULTI', DATETIME('now', '-24 months'), DATETIME('now', '-22 months'));

INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate)
VALUES (7, 'M-RUI-MULTI', DATETIME('now', '-12 months'), DATETIME('now', '-10 months'));

INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate)
VALUES (7, 'M-RUI-MULTI', DATETIME('now', '-5 months'), DATETIME('now', '-4 months'));


-- CASO B: O dosímetro 'M-PARTILHADO-99' foi usado por pessoas diferentes.
-- Carlos usou primeiro
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate)
VALUES (1, 'M-PARTILHADO-99', DATETIME('now', '-8 months'), DATETIME('now', '-6 months'));

-- Inês usou a seguir
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate)
VALUES (2, 'M-PARTILHADO-99', DATETIME('now', '-5 months'), DATETIME('now', '-3 months'));


-- =================================================================================
-- 6. HISTÓRICO DE PEDIDOS (CHANGE RECORD) - NÃO APAGAR
-- =================================================================================

-- Pedidos Concluídos (Status 'Concluido' sem acento!)
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus, adminNote) 
VALUES (10, 12, 'Suspender', 'Licença', DATETIME('now', '-18 months'), 'Concluido', 1, DATETIME('now', '-18 months', '+1 day'), 'Suspenso', 'Aprovado conforme regulamento.');

INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus, adminNote) 
VALUES (10, 12, 'Ativar', 'Regresso', DATETIME('now', '-12 months'), 'Concluido', 1, DATETIME('now', '-12 months', '+1 day'), 'Ativo', NULL);

-- Pedido Rejeitado
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus, adminNote) 
VALUES (10, 12, 'Suspender', 'Prefiro não usar', DATETIME('now', '-6 months'), 'Rejeitado', 1, DATETIME('now', '-6 months', '+1 day'), 'Ativo', 'Uso obrigatório nesta prática.');

INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus, adminNote) 
VALUES (10, 12, 'Suspender', 'Doutoramento', DATETIME('now', '-2 months'), 'Concluido', 1, DATETIME('now', '-2 months', '+1 day'), 'Suspenso', 'Autorizado pela direção.');

-- Pedido Pendente
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status) 
VALUES (1, 3, 'Suspender', 'Férias.', DATETIME('now'), 'Pendente');