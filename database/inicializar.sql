



DELETE FROM ChangeRecord;
DELETE FROM DosimeterAssignmentHistory;
DELETE FROM DosimeterAssignment;
DELETE FROM RejectedRequest;
DELETE FROM ApprovedRequest;
DELETE FROM DosimeterRequest;
DELETE FROM HealthProfessional;
DELETE FROM User;

DELETE FROM sqlite_sequence;

-- 1. UTILIZADORES
INSERT INTO User (name, surname, birthDate, sex, email, password, phoneN, userType, userStatus, profilePic) VALUES
('Ana', 'Silva', '1980-05-12', 'Female', 'admin@mail.com', '123', '+351910000001', 'Administrador', 1, 'foto/1.png'),
('João', 'Pinho', '1985-02-15', 'Male', 'joao@mail.com', '123', '+351910000003', 'Físico Médico', 1, 'foto/2.png'),
('Carlos', 'Vieira', '1988-01-10', 'Male', 'carlos@mail.com', '123', '+351910000005', 'Profissional de Saúde', 1, 'foto/3.png'),
('Inês', 'Rocha', '1992-06-25', 'Female', 'ines@mail.com', '123', '+351910000006', 'Profissional de Saúde', 1, 'foto/4.png'),
('Paulo', 'Morgado', '1982-03-15', 'Male', 'paulo@mail.com', '123', '+351910000007', 'Profissional de Saúde', 1, 'foto/5.png'),
('Sofia', 'Almeida', '1995-09-01', 'Female', 'sofia@mail.com', '123', '+351910000008', 'Profissional de Saúde', 1, 'foto/6.png'),
('Bruno', 'Costa', '1990-12-12', 'Male', 'bruno@mail.com', '123', '+351910000009', 'Profissional de Saúde', 1, 'foto/7.png'),
('Lara', 'Sousa', '1998-04-04', 'Female', 'lara@mail.com', '123', '+351910000010', 'Profissional de Saúde', 1, 'foto/8.png'),
('Rui', 'Tavares', '1985-07-07', 'Male', 'rui@mail.com', '123', '+351910000011', 'Profissional de Saúde', 1, 'foto/9.png'),
('Marta', 'Lima', '1985-05-05', 'Female', 'marta@mail.com', '123', '+351910000099', 'Profissional de Saúde', 0, 'foto/10.png'),
('Tiago', 'Persistente', '1993-02-02', 'Male', 'tiago@mail.com', '123', '+351910000022', 'Profissional de Saúde', 1, 'foto/12225881.png'),
('Vera', 'Ciclos', '1989-08-08', 'Female', 'vera@mail.com', '123', '+351910000033', 'Profissional de Saúde', 1, 'foto/12225881.png');

INSERT INTO HealthProfessional (idU, profession, department) VALUES
(3, 'Técnico', 'Radiologia'), (4, 'Enfermeira', 'Bloco'), (5, 'Médico', 'Cardio'), 
(6, 'Dentista', 'Estomatologia'), (7, 'Técnico', 'Nuclear'), (8, 'Investigadora', 'Lab'),
(9, 'Enfermeiro', 'Urgência'), (10, 'Enfermeira', 'Urgência'),
(11, 'Estagiário', 'Radiologia'), (12, 'Técnica', 'Medicina Nuclear');

-- =================================================================================
-- 2. PEDIDOS E APROVAÇÕES
-- =================================================================================
-- Carlos (ID 3)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (3, 'Raio-X', DATE('now', '-6 months'), 1); -- idR 1
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (1, 2, DATE('now', '-6 months'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo'); -- idA 1

-- Inês (ID 4)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (4, 'Bloco', DATE('now', '-4 months'), 1); -- idR 2
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (2, 2, DATE('now', '-4 months'), 'Trimestral', 'B', 'Extremidade', 'Ativo'); -- idA 2

-- Paulo (ID 5)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (5, 'Hemodinâmica', DATE('now', '-2 months'), 1); -- idR 3
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (3, 2, DATE('now', '-2 months'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo'); -- idA 3

-- Sofia (ID 6)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (6, 'Dentária', DATE('now', '-1 day'), 1); -- idR 4
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (4, 2, DATE('now', '-1 day'), 'Trimestral', 'B', 'Corpo Inteiro', 'Ativo'); -- idA 4

-- Bruno (ID 7)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (7, 'Nuclear', DATE('now'), 1); -- idR 5
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (5, 2, DATE('now'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo'); -- idA 5

-- Lara (ID 8)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (8, 'Investigação', DATE('now', '-1 year'), 1); -- idR 6
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (6, 2, DATE('now', '-1 year'), 'Trimestral', 'A', 'Extremidade', 'Suspenso'); -- idA 6

-- Rui (ID 9)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (9, 'Urgência', DATE('now', '-1 year'), 1); -- idR 7
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (7, 2, DATE('now', '-1 year'), 'Mensal', 'A', 'Corpo Inteiro', 'Ativo'); -- idA 7

-- Marta (ID 10)
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (10, 'Urgência', DATE('now', '-2 years'), 1); -- idR 8
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) VALUES (8, 2, DATE('now', '-2 years'), 'Trimestral', 'B', 'Corpo Inteiro', 'Suspenso'); -- idA 8


-- === NOVOS CASOS ===

-- TIAGO (ID 11): Rejeitado 2x, Aprovado à 3ª
-- Pedido 1 (Rejeitado) - idR 9
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (11, 'Estágio', DATE('now', '-1 year'), 1);
INSERT INTO RejectedRequest (idR, idP, rejectionDate, comment) VALUES (9, 2, DATE('now', '-1 year', '+2 days'), 'Falta certificado de radioproteção.');

-- Pedido 2 (Rejeitado) - idR 10
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (11, 'Estágio II', DATE('now', '-10 months'), 1);
INSERT INTO RejectedRequest (idR, idP, rejectionDate, comment) VALUES (10, 2, DATE('now', '-10 months', '+2 days'), 'Prática não especificada corretamente.');

-- Pedido 3 (Aprovado) - idR 11 -> idA 9
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (11, 'Estágio Final', DATE('now', '-9 months'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (11, 2, DATE('now', '-9 months'), 'Mensal', 'B', 'Corpo Inteiro', 'Ativo');


-- VERA (ID 12): Ciclo Ativa/Suspende
-- Pedido Aprovado há 2 anos - idR 12 -> idA 10
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade) VALUES (12, 'MN', DATE('now', '-2 years'), 1);
INSERT INTO ApprovedRequest (idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status) 
VALUES (12, 2, DATE('now', '-2 years'), 'Mensal', 'A', 'Corpo Inteiro', 'Suspenso'); -- Atualmente suspenso


-- =================================================================================
-- 3. ASSOCIAÇÕES (Assignments)
-- =================================================================================

-- Carlos (idA 1): Mensal.
UPDATE DosimeterAssignment SET dosimeterSerial='M-CARLOS-01', assignmentDate=DATE('now', '-15 days'), nextReplacementDate=DATE('now', '-15 days', '+30 days'), status='Em_Uso', periodicity='Mensal' WHERE idA=1;

-- Inês (idA 2): Trimestral.
UPDATE DosimeterAssignment SET dosimeterSerial='T-INES-01', assignmentDate=DATE('now', '-80 days'), nextReplacementDate=DATE('now', '-80 days', '+90 days'), status='Em_Uso', periodicity='Trimestral' WHERE idA=2;

-- Paulo (idA 3): Atrasado.
UPDATE DosimeterAssignment SET dosimeterSerial='M-PAULO-LATE', assignmentDate=DATE('now', '-35 days'), nextReplacementDate=DATE('now', '-35 days', '+30 days'), status='Em_Uso', periodicity='Mensal' WHERE idA=3;

-- Bruno (idA 5): Hoje.
UPDATE DosimeterAssignment SET dosimeterSerial='M-BRUNO-HOJE', assignmentDate=DATE('now'), nextReplacementDate=DATE('now', '+30 days'), status='Em_Uso', periodicity='Mensal' WHERE idA=5;

-- Lara (idA 6): Suspensa.
UPDATE DosimeterAssignment SET dosimeterSerial='T-LARA-SUSP', assignmentDate=DATE('now', '-6 months'), nextReplacementDate=DATE('now'), status='Suspenso', periodicity='Trimestral' WHERE idA=6;

-- Rui (idA 7): Ativo.
UPDATE DosimeterAssignment SET dosimeterSerial='M-RUI-05', assignmentDate=DATE('now', '-5 days'), nextReplacementDate=DATE('now', '-5 days', '+30 days'), status='Em_Uso', periodicity='Mensal' WHERE idA=7;

-- Marta (idA 8): Suspensa.
UPDATE DosimeterAssignment SET dosimeterSerial='T-MARTA-LAST', assignmentDate=DATE('now', '-1 year'), nextReplacementDate=DATE('now'), status='Suspenso', periodicity='Trimestral' WHERE idA=8;

-- TIAGO (idA 9): Ativo (Está no seu 9º mês de trabalho após ser rejeitado 2x)
UPDATE DosimeterAssignment SET 
    dosimeterSerial = 'M-TIAGO-09', 
    assignmentDate = DATE('now', '-10 days'), 
    nextReplacementDate = DATE('now', '-10 days', '+30 days'),
    status = 'Em_Uso', periodicity = 'Mensal' WHERE idA = 9;

-- VERA (idA 10): Suspensa (Novamente)
UPDATE DosimeterAssignment SET 
    dosimeterSerial = 'M-VERA-LAST', 
    assignmentDate = DATE('now', '-3 months'), 
    nextReplacementDate = DATE('now'), -- Data irrelevante pois está suspenso
    status = 'Suspenso', periodicity = 'Mensal' WHERE idA = 10;


-- =================================================================================
-- 4. HISTÓRICO DE DOSÍMETROS
-- =================================================================================

-- Históricos Rui
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, insertDate) VALUES (7, 'M-RUI-01', DATE('now', '-155 days')), (7, 'M-RUI-02', DATE('now', '-125 days')), (7, 'M-RUI-03', DATE('now', '-95 days'));

-- Históricos Lara e Marta
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, insertDate) VALUES (6, 'T-LARA-01', DATE('now', '-6 months')), (6, 'T-LARA-SUSP', DATE('now', '-10 days'));
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, insertDate) VALUES (8, 'T-MARTA-01', DATE('now', '-18 months')), (8, 'T-MARTA-LAST', DATE('now', '-12 months'));

-- TIAGO: Tem histórico desde há 9 meses
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, insertDate) VALUES
(9, 'M-TIAGO-01', DATE('now', '-9 months')),
(9, 'M-TIAGO-02', DATE('now', '-8 months')),
(9, 'M-TIAGO-03', DATE('now', '-7 months'));

-- VERA: Histórico Complexo (Muitos períodos)
INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, insertDate) VALUES
(10, 'M-VERA-01', DATE('now', '-24 months')), -- Início
(10, 'M-VERA-02', DATE('now', '-23 months')),
(10, 'M-VERA-03', DATE('now', '-22 months')),
(10, 'M-VERA-SUSP1', DATE('now', '-18 months')), -- Suspendeu aqui (1ª vez)
(10, 'M-VERA-RET1', DATE('now', '-12 months')), -- Voltou aqui
(10, 'M-VERA-RET2', DATE('now', '-11 months')),
(10, 'M-VERA-SUSP2', DATE('now', '-2 months'));  -- Suspendeu de novo (Atual)


-- =================================================================================
-- 5. HISTÓRICO DE PEDIDOS (ChangeRecord) - ONDE SE VÊ A ATIVAÇÃO/SUSPENSÃO
-- =================================================================================

-- Pedido Pendente (Carlos)
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status) 
VALUES (1, 3, 'Suspender', 'Férias.', DATE('now'), 'Pendente');

-- Histórico VERA (O ciclo Suspend -> Active -> Suspend)
-- 1. Pediu para suspender há 18 meses
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus) 
VALUES (10, 12, 'Suspender', 'Licença de Maternidade', DATE('now', '-18 months'), 'Concluído', 1, DATE('now', '-18 months', '+1 day'), 'Suspenso');

-- 2. Pediu para ativar há 12 meses
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus) 
VALUES (10, 12, 'Ativar', 'Regresso ao serviço', DATE('now', '-12 months'), 'Concluído', 1, DATE('now', '-12 months', '+1 day'), 'Ativo');

-- 3. Pediu para suspender há 2 meses
INSERT INTO ChangeRecord (idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus) 
VALUES (10, 12, 'Suspender', 'Doutoramento no estrangeiro', DATE('now', '-2 months'), 'Concluído', 1, DATE('now', '-2 months', '+1 day'), 'Suspenso');