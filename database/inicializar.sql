-- 1. DESLIGAR VERIFICAÇÕES PARA LIMPEZA
PRAGMA FOREIGN KEY = OFF;
PRAGMA PRIMARY KEY = OFF;

-- 2. LIMPAR TUDO (Ordem inversa para evitar erros, embora o PRAGMA ajude)
DELETE FROM DosimeterAssignmentHistory;
DELETE FROM DosimeterAssignment;
DELETE FROM ChangeRecord;
DELETE FROM RejectedRequest;
DELETE FROM ApprovedRequest;
DELETE FROM DosimeterRequest;
DELETE FROM HealthProfessional;
DELETE FROM User;
DELETE FROM sqlite_sequence; -- Reseta os IDs (AUTOINCREMENT) para começar no 1

-- 3. LIGAR VERIFICAÇÕES
PRAGMA FOREIGN KEY = ON;
PRAGMA PRIMARY KEY = ON;

-- =================================================================================
-- 4. INSERIR UTILIZADORES
-- =================================================================================
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
('Vera', 'Ciclos', '1989-08-08', 'Female', 'vera@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000033', 'Profissional de Saúde', 1, 'foto/12225881.png'),
-- FÍSICOS MÉDICOS EXTRA (ID 13 e 14)
('Mariana', 'Costa', '1990-03-20', 'Female', 'mariana@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000044', 'Físico Médico', 1, 'foto/12225881.png'),
('Pedro', 'Nunes', '1982-11-11', 'Male', 'pedro@mail.com', '$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y', '+351910000055', 'Físico Médico', 1, 'foto/12225881.png');

INSERT INTO HealthProfessional (idU, profession, department) VALUES
(3, 'Técnico', 'Radiologia'), (4, 'Enfermeira', 'Bloco'), (5, 'Médico', 'Cardio'), 
(6, 'Dentista', 'Estomatologia'), (7, 'Técnico', 'Nuclear'), (8, 'Investigadora', 'Lab'),
(9, 'Enfermeiro', 'Urgência'), (10, 'Enfermeira', 'Urgência'),
(11, 'Estagiário', 'Radiologia'), (12, 'Técnica', 'Medicina Nuclear');

-- =================================================================================
-- 5. PEDIDOS (DosimeterRequest) + APROVAÇÕES (ApprovedRequest)
-- Nota: Ao inserir em ApprovedRequest, o Trigger 'CreateAssignmentAfterApprove'
-- cria automaticamente a linha em 'DosimeterAssignment' com status 'Por_Associar'.
-- =================================================================================

-- 1. Carlos (idU=3) -> idR=1 -> idA=1
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (3, 'Raio-X', DATETIME('now', '-6 months'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (1, 2, DATETIME('now', '-6 months', '+2 days'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

-- 2. Inês (idU=4) -> idR=2 -> idA=2
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (4, 'Bloco', DATETIME('now', '-4 months'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (2, 2, DATETIME('now', '-4 months', '+2 days'), 'Trimestral', 'B', 'Extremidade', 'Ativo');

-- 3. Paulo (idU=5) -> idR=3 -> idA=3
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (5, 'Hemodinâmica', DATETIME('now', '-2 months'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (3, 2, DATETIME('now', '-2 months', '+2 days'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

-- 4. Sofia (idU=6) -> idR=4 -> idA=4
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (6, 'Dentária', DATETIME('now', '-1 day'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (4, 2, DATETIME('now', '-1 day', '+2 days'), 'Trimestral', 'B', 'Corpo Inteiro', 'Ativo');

-- 5. Bruno (idU=7) -> idR=5 -> idA=5
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (7, 'Nuclear', DATETIME('now'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (5, 2, DATETIME('now'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

-- 6. Lara (idU=8) -> idR=6 -> idA=6 (Suspenso)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (8, 'Investigação', DATETIME('now', '-1 year'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (6, 2, DATETIME('now', '-1 year', '+2 days'), 'Trimestral', 'A', 'Extremidade', 'Suspenso');

-- 7. Rui (idU=9) -> idR=7 -> idA=7
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (9, 'Urgência', DATETIME('now', '-1 year'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (7, 2, DATETIME('now', '-1 year', '+2 days'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

-- 8. Marta (idU=10) -> idR=8 -> idA=8 (Suspenso)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (10, 'Urgência', DATETIME('now', '-2 years'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (8, 2, DATETIME('now', '-2 years', '+2 days'), 'Trimestral', 'B', 'Corpo Inteiro', 'Suspenso');

-- 9. Tiago (idU=11) -> Rejeitados e depois Aprovado (idR=11 -> idA=9)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (11, 'Estágio', DATETIME('now', '-1 year'), 1);
INSERT INTO RejectedRequest (idR, idP, rejectionDate, comment) VALUES (9, 2, DATETIME('now', '-1 year', '+2 days'), 'Falta certificado.');

INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (11, 'Estágio II', DATETIME('now', '-10 months'), 1);
INSERT INTO RejectedRequest (idR, idP, rejectionDate, comment) VALUES (10, 2, DATETIME('now', '-10 months', '+2 days'), 'Prática errada.');

INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (11, 'Estágio Final', DATETIME('now', '-9 months'), 1); 
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (11, 2, DATETIME('now', '-9 months'), 'Mensal', 'B', 'Corpo Inteiro', 'Ativo');

-- 10. Vera (idU=12) -> idR=12 -> idA=10 (Suspenso)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (12, 'MN', DATETIME('now', '-2 years'), 1); 
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (12, 2, DATETIME('now', '-2 years', '+2 days'), 'Mensal', 'A', 'Corpo Inteiro', 'Suspenso');


-- === PEDIDOS DOS FÍSICOS ===
-- 11. João Pinho (idU=2) -> idR=13 -> idA=11
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (2, 'Controlo Qualidade', DATETIME('now', '-18 months'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (13, 1, DATETIME('now', '-18 months', '+1 day'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

-- 12. Mariana Costa (idU=13) -> idR=14 -> idA=12
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (13, 'Medicina Nuclear', DATETIME('now', '-12 months'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (14, 2, DATETIME('now', '-12 months', '+1 day'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo');

-- 13. Pedro Nunes (idU=14) -> idR=15 -> idA=13
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (14, 'Radioterapia', DATETIME('now', '-10 days'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (15, 1, DATETIME('now', '-10 days', '+1 day'), 'Trimestral', 'B', 'Extremidade', 'Ativo');


-- =================================================================================
-- 6. ATUALIZAR 'DosimeterAssignment' (Colocar em Uso)
-- O Trigger já criou as linhas com 'Por_Associar'. Agora fazemos UPDATE.
-- =================================================================================

-- Carlos (idA=1)
UPDATE DosimeterAssignment SET dosimeterSerial='M-CARLOS-01', assignmentDate=DATETIME('now', '-15 days'), nextReplacementDate=DATETIME('now', '+15 days'), status='Em_Uso' WHERE idA=1;
-- Inês (idA=2)
UPDATE DosimeterAssignment SET dosimeterSerial='T-INES-01', assignmentDate=DATETIME('now', '-75 days'), nextReplacementDate=DATETIME('now', '+15 days'), status='Em_Uso' WHERE idA=2;
-- Paulo (idA=3)
UPDATE DosimeterAssignment SET dosimeterSerial='M-PAULO-LATE', assignmentDate=DATETIME('now', '-60 days'), nextReplacementDate=DATETIME('now', '-30 days'), status='Em_Uso' WHERE idA=3;
-- Sofia (idA=4) - Acabou de ser aprovado, fica Por_Associar (não fazemos update)

-- Bruno (idA=5)
UPDATE DosimeterAssignment SET dosimeterSerial='M-BRUNO-NEW', assignmentDate=DATETIME('now'), nextReplacementDate=DATETIME('now', '+30 days'), status='Em_Uso' WHERE idA=5;
-- Lara (idA=6) - Suspenso
UPDATE DosimeterAssignment SET status='Suspenso' WHERE idA=6;

-- Rui (idA=7)
UPDATE DosimeterAssignment SET dosimeterSerial='M-RUI-05', assignmentDate=DATETIME('now'), nextReplacementDate=DATETIME('now', '+30 days'), status='Em_Uso' WHERE idA=7;
-- Marta (idA=8) - Suspenso
UPDATE DosimeterAssignment SET status='Suspenso' WHERE idA=8;

-- Tiago (idA=9 - veio do idRequest 11 mas o idApproved é sequencial, cuidado aqui!)
-- ATENÇÃO: O idA é AutoIncrement. 
-- Requests aprovados até agora: 1, 2, 3, 4, 5, 6, 7, 8. O do Tiago é o 9.
UPDATE DosimeterAssignment SET dosimeterSerial='M-TIAGO-09', assignmentDate=DATETIME('now', '-20 days'), nextReplacementDate=DATETIME('now', '+10 days'), status='Em_Uso' WHERE idA=9;

-- Vera (idA=10) - Suspenso
UPDATE DosimeterAssignment SET status='Suspenso' WHERE idA=10;

-- FÍSICOS
-- João Pinho (idA=11)
UPDATE DosimeterAssignment SET dosimeterSerial='M-JOAO-PHYS-02', assignmentDate=DATETIME('now', '-5 days'), nextReplacementDate=DATETIME('now', '+25 days'), status='Em_Uso' WHERE idA=11;

-- Mariana Costa (idA=12)
UPDATE DosimeterAssignment SET dosimeterSerial='M-MARIANA-NEW', assignmentDate=DATETIME('now', '-20 days'), nextReplacementDate=DATETIME('now', '+10 days'), status='Em_Uso' WHERE idA=12;

-- Pedro Nunes (idA=13) - Deixar 'Por_Associar'


-- =================================================================================
-- 7. INSERIR HISTÓRICO (DosimeterAssignmentHistory)
-- Atenção aos IDs de ApprovedRequest (idA)
-- =================================================================================

-- Histórico Rui (idA=7)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (7, 'M-RUI-REC', DATETIME('now', '-30 days'), DATETIME('now')); 
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (7, 'M-RUI-01', DATETIME('now', '-185 days'), DATETIME('now', '-155 days'));
-- Reutilização Rui
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (7, 'M-RUI-MULTI', DATETIME('now', '-24 months'), DATETIME('now', '-22 months'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (7, 'M-RUI-MULTI', DATETIME('now', '-12 months'), DATETIME('now', '-10 months'));

-- Histórico Lara (idA=6)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (6, 'T-LARA-REC', DATETIME('now', '-6 months'), DATETIME('now'));

-- Histórico Marta (idA=8)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (8, 'T-MARTA-REC', DATETIME('now', '-1 year'), DATETIME('now'));

-- Histórico Tiago (idA=9)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (9, 'M-TIAGO-01', DATETIME('now', '-10 months'), DATETIME('now', '-9 months'));

-- Histórico João Pinho (idA=11)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (11, 'M-JOAO-PHYS-01', DATETIME('now', '-6 months'), DATETIME('now', '-5 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (11, 'M-JOAO-OLD', DATETIME('now', '-12 months'), DATETIME('now', '-6 months'));

-- Histórico Mariana Costa (idA=12)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate) VALUES (12, 'M-MARIANA-01', DATETIME('now', '-5 months'), DATETIME('now', '-2 months'));


-- =================================================================================
-- 8. CHANGE RECORD (Histórico de Suspensões/Ativações)
-- =================================================================================
-- Vera (idA=10) - Vários eventos
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus, adminNote) 
VALUES (10, 12, 'Suspender', 'Licença Maternidade', DATETIME('now', '-18 months'), 'Concluido', 1, DATETIME('now', '-18 months', '+1 day'), 'Suspenso', 'Aprovado.');

INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus, adminNote) 
VALUES (10, 12, 'Ativar', 'Regresso ao trabalho', DATETIME('now', '-12 months'), 'Concluido', 1, DATETIME('now', '-12 months', '+1 day'), 'Ativo', NULL);

INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus, adminNote) 
VALUES (10, 12, 'Suspender', 'Doutoramento', DATETIME('now', '-2 months'), 'Concluido', 1, DATETIME('now', '-2 months', '+1 day'), 'Suspenso', 'Autorizado.');

-- Carlos (idA=1) - Pendente
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status) 
VALUES (1, 3, 'Suspender', 'Férias prolongadas.', DATETIME('now'), 'Pendente');