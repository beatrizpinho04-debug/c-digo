-- 1. Inserir Users
INSERT INTO User (name, surname, birthDate, sex, email, password, phoneN, userType, userStatus, profilePic)
VALUES
-- Administradores
('Ana','Ferreira','1978-04-12','Female','ana.admin@hospital.pt','$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y','+351912111111','Administrador',1, 'foto/1.png'),
('Miguel','Santos','1982-09-30','Male','miguel.admin@hospital.pt','$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y','+351912222222','Administrador',1, 'foto/2.png'),

-- Físicos Médicos
('João','Almeida','1985-02-18','Male','joao.fisico@hospital.pt','$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y','+351913333333','Físico Médico',1, 'foto/3.png'),

('Rita','Pereira','1990-06-22','Female','rita.fisica@hospital.pt','$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y','+351914444444','Físico Médico',1, 'foto/4.png'),

-- Profissionais de Saúde
('Carlos','Mendes','1988-01-15','Male','carlos.mendes@hospital.pt','$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y','+351915555555','Profissional de Saúde',1, 'foto/5.png'),
('Inês','Rocha','1992-11-05','Female','ines.rocha@hospital.pt','$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y','+351916666666','Profissional de Saúde',1, 'foto/6.png'),
('Paulo','Teixeira','1980-03-28','Male','paulo.teixeira@hospital.pt','$2y$10$2auDYhZCi32TRIF6iW1HOuj8mYt9glxgREcrK.eJMZwMBQnzzG9/y','+351917777777','Profissional de Saúde',0, 'foto/7.png');

-- ===============================================
-- 2. Definir Health Professionals
INSERT INTO HealthProfessional (idU, profession, department)
VALUES
(5,'Técnico de Radiologia','Radiologia'),
(6,'Enfermeira','Bloco Operatório'),
(7,'Médico','Oncologia');


-- ===============================================
-- 3. Fazer pedidos de dosímetro
INSERT INTO DosimeterRequest (idU, pratica, requestDate, decisionMade)
VALUES
-- Pendente (para o físico decidir)
(5,'Radiologia de intervenção', DATETIME('now','-5 days'),0),
-- Já avaliados
(6,'Cirurgia ortopédica', DATETIME('now','-20 days'),0),
(7,'Radioterapia', DATETIME('now','-30 days'),0),
-- Físicos médicos (automáticos)
(3,'Atividade clínica em Física Médica', DATETIME('now','-40 days'),0),
(4,'Controlo de qualidade', DATETIME('now','-25 days'),0);

-- ===============================================
-- 4. Aprovar pedidos de dosímetro
INSERT INTO ApprovedRequest(idR, idP, approvalDate, periodicity, riskCategory, dosimeterType, status)
VALUES
-- Profissional aprovado (ativo, por associar)
(2,3,DATETIME('now','-18 days'),'Mensal','B','Corpo Inteiro','Ativo'),

-- Físico com histórico de troca
(4,3,DATETIME('now','-40 days'),'Trimestral','A','Corpo Inteiro','Ativo'),

-- Físico suspenso
(5,4,DATETIME('now','-25 days'),'Mensal','A','Extremidade','Suspenso');


-- ===============================================
-- 5. Rejeitar pedidos de dosímetro
INSERT INTO RejectedRequest (idR, idP, rejectionDate, comment)
VALUES
(3,4,DATETIME('now','-28 days'),
'Exposição não justificada para a prática indicada');

-- -- ===============================================
-- 6. DOSIMETER ASSIGNMENTS e Dosimeter Assignment history
UPDATE DosimeterAssignment
SET
dosimeterSerial = 'RX-1001',
assignmentDate = DATE('now','-15 days'),
nextReplacementDate = DATE('now','+15 days'),
periodicity = 'Mensal',
status = 'Em_Uso',
notes = 'Primeiro ciclo'
WHERE idA = 2;

-- Pedido com histórico
UPDATE DosimeterAssignment
SET
dosimeterSerial = 'FM-2001',
assignmentDate = DATE('now','-25 days'),
nextReplacementDate = DATE('now','+65 days'),
periodicity = 'Trimestral',
status = 'Em_Uso',
notes = 'Segundo dosímetro'
WHERE idA = 3;

INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, insertDate)
VALUES
(2,'RX-1001',DATETIME('now','-15 days')),
(3,'FM-1000',DATETIME('now','-90 days')),
(3,'FM-2001',DATETIME('now','-25 days'));


-- ===============================================
-- 7. CHANGE RECORDS
INSERT INTO ChangeRecord
(idA, idUser, requestType, message, requestDate)
VALUES
(2,6,'Suspender','Ausência prolongada',DATETIME('now','-2 days'));

-- Pedido já concluído
INSERT INTO ChangeRecord
(idA, idUser, requestType, message, requestDate, status, idAdmin, decisionDate, finalStatus)
VALUES
(3,4,'Suspender','Equipamento em manutenção',
DATETIME('now','-10 days'),'Concluído',1,DATETIME('now','-8 days'),'Suspenso');
