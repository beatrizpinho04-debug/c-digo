-- Desligar verificações
PRAGMA FOREIGN KEY = OFF;
PRAGMA PRIMARY KEY = OFF;

-- Limpar tudo
DELETE FROM DosimeterAssignmentHistory;
DELETE FROM DosimeterAssignment;
DELETE FROM ChangeRecord;
DELETE FROM RejectedRequest;
DELETE FROM ApprovedRequest;
DELETE FROM DosimeterRequest;
DELETE FROM HealthProfessional;
DELETE FROM User;
DELETE FROM sqlite_sequence;

-- Ligar verificações
PRAGMA FOREIGN KEY = ON;
PRAGMA PRIMARY KEY = ON;

-- =================================================================================
-- Inserir utilizadores
-- =================================================================================
INSERT INTO User (name, surname, birthDate, sex, email, password, phoneN, userType, userStatus, profilePic) VALUES
('Ana', 'Silva', '1980-05-12', 'Female', 'ana@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000001', 'Administrador', 1, 'foto/1.png'),
('João', 'Pinho', '1985-02-15', 'Male', 'joao@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000003', 'Físico Médico', 1, 'foto/2.png'),
('Carlos', 'Vieira', '1988-01-10', 'Male', 'carlos@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000005', 'Profissional de Saúde', 1, 'foto/3.png'),
('Inês', 'Rocha', '1992-06-25', 'Female', 'ines@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000006', 'Profissional de Saúde', 1, 'foto/4.png'),
('Paulo', 'Morgado', '1982-03-15', 'Male', 'paulo@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000007', 'Profissional de Saúde', 1, 'foto/5.png'),
('Sofia', 'Almeida', '1995-09-01', 'Female', 'sofia@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000008', 'Profissional de Saúde', 1, 'foto/6.png'),
('Bruno', 'Costa', '1990-12-12', 'Male', 'bruno@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000009', 'Profissional de Saúde', 1, 'foto/7.png'),
('Lara', 'Sousa', '1998-04-04', 'Female', 'lara@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000010', 'Profissional de Saúde', 1, 'foto/8.png'),
('Rui', 'Tavares', '1985-07-07', 'Male', 'rui@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000011', 'Profissional de Saúde', 1, 'foto/9.png'),
('Marta', 'Lima', '1985-05-05', 'Female', 'marta@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000099', 'Profissional de Saúde', 0, 'foto/10.png'),
('Tiago', 'Fernandes', '1993-02-02', 'Male', 'tiago@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000022', 'Profissional de Saúde', 1, 'foto/12225881.png'),
('Vera', 'Pereira', '1989-08-08', 'Female', 'vera@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000033', 'Profissional de Saúde', 1, 'foto/12225881.png'),
('Mariana', 'Costa', '1990-03-20', 'Female', 'mariana@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000044', 'Físico Médico', 1, 'foto/12225881.png'),
('Pedro', 'Nunes', '1982-11-11', 'Male', 'pedro@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000055', 'Físico Médico', 1, 'foto/12225881.png'),
('Duarte', 'Matos', '1990-01-01', 'Male', 'duarte@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000066', 'Físico Médico', 1, 'foto/12225881.png'),
('Elisa', 'Oliveira', '1992-05-05', 'Female', 'elisa@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000077', 'Profissional de Saúde', 1, 'foto/12225881.png'),
('Fábio', 'Costa', '1988-08-08', 'Male', 'fabio@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000088', 'Profissional de Saúde', 1, 'foto/12225881.png'),
('Gustavo', 'Soares', '1995-11-11', 'Male', 'gustavo@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000099', 'Profissional de Saúde', 1, 'foto/12225881.png'),
('Ricardo', 'Santos', '1975-10-30', 'Male', 'ricardo@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000111', 'Administrador', 1, 'foto/12225881.png');

-- =================================================================================
-- Inserir profissionais de saúde
-- =================================================================================
INSERT INTO HealthProfessional (idU, profession, department) VALUES
(3, 'Técnico', 'Radiologia'), 
(4, 'Enfermeira', 'Bloco'), 
(5, 'Médico', 'Cardio'), 
(6, 'Dentista', 'Estomatologia'), 
(7, 'Técnico', 'Nuclear'), 
(8, 'Investigadora', 'Lab'),
(9, 'Enfermeiro', 'Urgência'), 
(10, 'Enfermeira', 'Urgência'),
(11, 'Estagiário', 'Radiologia'), 
(12, 'Técnica', 'Medicina Nuclear'),
(16, 'Enfermeira', 'Pediatria'),
(17, 'Técnico', 'Radiologia'),
(18, 'Médico', 'Oncologia');

-- =================================================================================
-- Pedidos de dosímetro
-- =================================================================================
-- Carlos (idU=3) -> Aprovado por João Pinho (idP=2)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (3, 'Raio-X', DATETIME('now', '-182 days'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (1, 2, DATETIME('now', '-181 days'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

-- Inês (idU=4) -> Aprovado por Mariana Costa (idP=13)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (4, 'Bloco', DATETIME('now', '-78 days'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (2, 13, DATETIME('now', '-77 days'), 'Trimestral', 'B', 'Extremidade', 'Ativo');

-- Paulo (idU=5) -> Aprovado por Pedro Nunes (idP=14)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (5, 'Hemodinâmica', DATETIME('now', '-34 days'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (3, 14, DATETIME('now', '-33 days'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

-- Sofia (idU=6) -> Aprovado por João Pinho (idP=2)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (6, 'Dentária', DATETIME('now', '-2 days'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (4, 2, DATETIME('now', '-1 day'), 'Trimestral', 'B', 'Corpo Inteiro', 'Ativo');

-- Bruno (idU=7) -> Aprovado por Mariana Costa (idP=13)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (7, 'Nuclear', DATETIME('now', '-2 day'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (5, 13, DATETIME('now', '-1 day'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

-- Lara (idU=8) -> Aprovado por Pedro Nunes (idP=14), mas autorização/pedido em suspenso
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (8, 'Investigação', DATETIME('now', '-180 days'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (6, 14, DATETIME('now', '-179 days'), 'Trimestral', 'A', 'Extremidade', 'Suspenso');

-- Rui (idU=9) -> Aprovado por João Pinho (idP=2)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (9, 'Urgência', DATETIME('now', '-125 days'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (7, 2, DATETIME('now', '-124 days'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

-- Marta (idU=10) -> Aprovado por João Pinho (idP=2), mas autorização/pedido em suspenso pois o user está inativo
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (10, 'Urgência', DATETIME('now', '-720 days'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (8, 2, DATETIME('now', '-718 days'), 'Trimestral', 'B', 'Corpo Inteiro', 'Suspenso');

-- Tiago (idU=11) -> 2 pedidos rejeitados e depois 1 pedido aprovado por João Pinho (idP=2)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (11, 'Estágio', DATETIME('now', '-70 days'), 1);
INSERT INTO RejectedRequest (idR, idP, rejectionDate, comment) VALUES (9, 2, DATETIME('now', '-68 days'), 'Falta certificado.');
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (11, 'Estágio II', DATETIME('now', '-66 days'), 1);
INSERT INTO RejectedRequest (idR, idP, rejectionDate, comment) VALUES (10, 2, DATETIME('now', '-64 days'), 'Prática errada.');
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (11, 'Estágio Final', DATETIME('now', '-62 days'), 1); 
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (11, 2, DATETIME('now', '-60 days'), 'Mensal', 'B', 'Corpo Inteiro', 'Ativo');

-- Vera (idU=12) -> Aprovado por Mariana Costa (idP=13), mas autorização/pedido em suspenso
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (12, 'MN', DATETIME('now', '-629 days'), 1); 
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (12, 13, DATETIME('now', '-627 days'), 'Mensal', 'A', 'Corpo Inteiro', 'Suspenso');

-- Físico João Pinho (idU=2) -> Aprovado pelo o próprio
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (2, 'Controlo Qualidade', DATETIME('now', '-67 days'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (13, 2, DATETIME('now', '-67 days'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

-- Físico Mariana Costa (idU=13) -> Aprovado pela a própria
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (13, 'Medicina Nuclear', DATETIME('now', '-51 days'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (14, 13, DATETIME('now', '-51 days'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

-- Físico Pedro Nunes (idU=14) -> Aprovado pelo o próprio
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (14, 'Radioterapia', DATETIME('now', '-3 days'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (15, 14, DATETIME('now', '-3 days'), 'Trimestral', 'B', 'Extremidade', 'Ativo');

-- Elisa (idU=16) -> Pendente (para testar Aprovar)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) 
VALUES (16, 'Raio-X Portátil', DATETIME('now', '-2 days'), 0);

-- Fábio (idU=17) -> PENDENTE (para testar Rejeitar)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) 
VALUES (17, 'Manutenção sem Credencial', DATETIME('now', '-1 day'), 0);

-- Gustavo (idU=18) não tem pedidos, para testar a funcionalidade de pedir dosímetro

-- =================================================================================
-- Atualizar 'DosimeterAssignment'
-- =================================================================================
-- Carlos (idA=1)
UPDATE DosimeterAssignment SET dosimeterSerial='DS-2025-007', assignmentDate=DATETIME('now'), nextReplacementDate=DATETIME('now', '+30 days'), status='Em_Uso' WHERE idA=1;
-- Inês (idA=2)
UPDATE DosimeterAssignment SET dosimeterSerial='DS-2025-008', assignmentDate=DATETIME('now', '-75 days'), nextReplacementDate=DATETIME('now', '+15 days'), status='Em_Uso' WHERE idA=2;
-- Paulo (idA=3)
UPDATE DosimeterAssignment SET dosimeterSerial='DS-2025-011', assignmentDate=DATETIME('now', '-32 days'), nextReplacementDate=DATETIME('now', '-2 days'), status='Em_Uso' WHERE idA=3;
-- Sofia (idA=4) - Fica 'Por_Associar'
-- Bruno (idA=5)
UPDATE DosimeterAssignment SET dosimeterSerial='DS-2025-001', assignmentDate=DATETIME('now'), nextReplacementDate=DATETIME('now', '+30 days'), status='Em_Uso' WHERE idA=5;
-- Lara (idA=6) - Suspenso
UPDATE DosimeterAssignment SET status='Suspenso' WHERE idA=6;
-- Rui (idA=7)
UPDATE DosimeterAssignment SET dosimeterSerial='DS-2025-013', assignmentDate=DATETIME('now'), nextReplacementDate=DATETIME('now', '+30 days'), status='Em_Uso' WHERE idA=7;
-- Marta (idA=8) - Suspenso
UPDATE DosimeterAssignment SET status='Suspenso' WHERE idA=8;
-- Tiago (idA=9 - Request 11)
UPDATE DosimeterAssignment SET dosimeterSerial='DS-2025-014', assignmentDate=DATETIME('now', '-29 days'), nextReplacementDate=DATETIME('now', '+1 day'), status='Em_Uso' WHERE idA=9;
-- Vera (idA=10) - Suspenso
UPDATE DosimeterAssignment SET status='Suspenso' WHERE idA=10;
-- João Pinho (idA=11)
UPDATE DosimeterAssignment SET dosimeterSerial='DS-2025-002', assignmentDate=DATETIME('now', '-5 days'), nextReplacementDate=DATETIME('now', '+25 days'), status='Em_Uso' WHERE idA=11;
-- Mariana Costa (idA=12)
UPDATE DosimeterAssignment SET dosimeterSerial='DS-2025-005', assignmentDate=DATETIME('now', '-20 days'), nextReplacementDate=DATETIME('now', '+10 days'), status='Em_Uso' WHERE idA=12;
-- Pedro Nunes (idA=13) - Fica 'Por_Associar'

-- =================================================================================
-- Inserir Histórico (DosimeterAssignmentHistory)
-- =================================================================================
-- Carlos (idA=1)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (1, 'DS-2025-001', DATETIME('now', '-180 days'), DATETIME('now', '-150 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (1, 'DS-2025-002', DATETIME('now', '-150 days'), DATETIME('now', '-120 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (1, 'DS-2025-003', DATETIME('now', '-120 days'), DATETIME('now', '-90 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (1, 'DS-2025-004', DATETIME('now', '-90 days'), DATETIME('now', '-60 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (1, 'DS-2025-005', DATETIME('now', '-60 days'), DATETIME('now', '-30 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (1, 'DS-2025-006', DATETIME('now', '-30 days'), DATETIME('now'));

-- Rui (idA=7)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (7, 'DS-2025-012', DATETIME('now', '-30 days'), DATETIME('now')); 
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (7, 'DS-2025-007', DATETIME('now', '-60 days'), DATETIME('now', '-30 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (7, 'DS-2025-002', DATETIME('now', '-90 days'), DATETIME('now', '-60 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (7, 'DS-2025-001', DATETIME('now', '-120 days'), DATETIME('now', '-90 days'));

-- Lara (idA=6)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (6, 'DS-2025-009', DATETIME('now', '-178 days'), DATETIME('now', '-88 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (6, 'DS-2025-010', DATETIME('now', '-88 days'), DATETIME('now'));

-- Marta (idA=8)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (8, 'DS-2025-008', DATETIME('now', '-717 days'), DATETIME('now', '-627 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (8, 'DS-2025-009', DATETIME('now', '-627 days'), DATETIME('now', '-537 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (8, 'DS-2025-010', DATETIME('now', '-537 days'), DATETIME('now', '-447 days'));

-- Tiago (idA=9)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (9, 'DS-2025-013', DATETIME('now', '-59 days'), DATETIME('now', '-29 days'));

-- João Pinho (idA=11)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (11, 'DS-2025-004', DATETIME('now', '-35 days'), DATETIME('now', '-5 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (11, 'DS-2025-001', DATETIME('now', '-65 days'), DATETIME('now', '-35 days'));
-- Mariana Costa (idA=12)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (12, 'DS-2025-003', DATETIME('now', '-50 days'), DATETIME('now', '-20 days'));

-- Vera (Devolveu o dosímetro quando foi suspensa há 2 meses)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (10, 'DS-2025-001', DATETIME('now', '-626 days'), DATETIME('now', '-596 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (10, 'DS-2025-002', DATETIME('now', '-596 days'), DATETIME('now', '-566 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (10, 'DS-2025-003', DATETIME('now', '-566 days'), DATETIME('now', '-539 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (10, 'DS-2025-004', DATETIME('now', '-359 days'), DATETIME('now', '-329 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (10, 'DS-2025-005', DATETIME('now', '-329 days'), DATETIME('now', '-299 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (10, 'DS-2025-006', DATETIME('now', '-299 days'), DATETIME('now', '-269 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (10, 'DS-2025-007', DATETIME('now', '-269 days'), DATETIME('now', '-239 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (10, 'DS-2025-011', DATETIME('now', '-239 days'), DATETIME('now', '-209 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (10, 'DS-2025-012', DATETIME('now', '-209 days'), DATETIME('now', '-179 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (10, 'DS-2025-013', DATETIME('now', '-179 days'), DATETIME('now', '-149 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (10, 'DS-2025-014', DATETIME('now', '-149 days'), DATETIME('now', '-119 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (10, 'DS-2025-015', DATETIME('now', '-119 days'), DATETIME('now', '-89 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (10, 'DS-2025-016', DATETIME('now', '-89 days'), DATETIME('now', '-60 days'));

-- =================================================================================
-- Change Record (Histórico de Suspensões/Ativações)
-- =================================================================================
-- Vera (idA=10) -> Ana aprovou
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus, adminNote) 
VALUES (10, 12, 'Suspender', 'Licença Maternidade', DATETIME('now', '-540 days'), 'Concluido', 1, DATETIME('now', '-540 days', '+1 day'), 'Suspenso', 'Aprovado.');

-- Vera (idA=10) -> Ricardo aprovou
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus, adminNote) 
VALUES (10, 12, 'Ativar', 'Regresso ao trabalho', DATETIME('now', '-361 days'), 'Concluido', 19, DATETIME('now', '-359 days'), 'Ativo', NULL);
-- Vera (idA=10) -> Ricardo aprovou
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus, adminNote) 
VALUES (10, 12, 'Suspender', 'Doutoramento', DATETIME('now', '-61 days'), 'Concluido', 19, DATETIME('now', '-60 days'), 'Suspenso', 'Autorizado.');

-- Lara (idA=6) -> Ana aprovou
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus, adminNote) 
VALUES (6, 8, 'Suspender', 'Licença Maternidade', DATETIME('now', '-5 days'), 'Concluido', 1, DATETIME('now'), 'Suspenso', 'Aprovado.');

-- Carlos (idA=1) -> Pendente
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status) 
VALUES (1, 3, 'Suspender', 'Baixa médica.', DATETIME('now'), 'Pendente');