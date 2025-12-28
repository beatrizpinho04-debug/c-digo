.headers ON
.mode columns

---------------
-- 1. USER
---------------
CREATE TABLE IF NOT EXISTS User (
    idU INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    surname TEXT NOT NULL, 
    birthDate DATE NOT NULL,
    sex TEXT CHECK(sex IN ('Female', 'Male', 'Other')) NOT NULL,
    email TEXT UNIQUE NOT NULL, 
    password TEXT NOT NULL,
    phoneN TEXT CHECK(phoneN GLOB '+[0-9][0-9][0-9]*'),
    userType TEXT CHECK(userType IN ('Administrador', 'Físico Médico', 'Profissional de Saúde')) NOT NULL,
    userStatus BOOLEAN DEFAULT 1,
    profilePic TEXT DEFAULT 'foto/12225881.png'
);

------------------------------
-- 2. HEALTH PROFESSIONAL
------------------------------
CREATE TABLE IF NOT EXISTS HealthProfessional (
    idHP INTEGER PRIMARY KEY AUTOINCREMENT,
    idU INTEGER UNIQUE NOT NULL,
    profession TEXT NOT NULL,
    department TEXT NOT NULL,
    FOREIGN KEY (idU) REFERENCES User(idU) ON DELETE CASCADE
);

----------------------------
-- 3. DOSIMETER REQUEST
----------------------------
CREATE TABLE IF NOT EXISTS DosimeterRequest (
    idR INTEGER PRIMARY KEY AUTOINCREMENT,
    idU INTEGER NOT NULL,
    pratica TEXT NOT NULL,
    requestDate DATETIME NOT NULL,
    decisionMade BOOLEAN DEFAULT 0,
    FOREIGN KEY (idU) REFERENCES User(idU) ON DELETE CASCADE
);
---------------------------
-- 4. APPROVED REQUEST
---------------------------
CREATE TABLE IF NOT EXISTS ApprovedRequest (
    idA INTEGER PRIMARY KEY AUTOINCREMENT,
    idR INTEGER NOT NULL,
    idP INTEGER NOT NULL,
    approvalDate DATETIME NOT NULL,
    periodicity TEXT CHECK(periodicity IN ('Mensal','Trimestral')) NOT NULL,
    riskCategory TEXT CHECK(riskCategory IN ('A', 'B')) NOT NULL,
    dosimeterType TEXT CHECK(dosimeterType IN ('Corpo Inteiro','Extremidade')) NOT NULL,
    status TEXT CHECK(status IN ('Ativo', 'Suspenso')) DEFAULT 'Ativo',
    FOREIGN KEY (idR) REFERENCES DosimeterRequest(idR) ON DELETE CASCADE,
    FOREIGN KEY (idP) REFERENCES User(idU) ON DELETE CASCADE
);

----------------------------------------------------------
-- TRIGGER: Passar o pedido aprovado para "Concluido"
----------------------------------------------------------
CREATE TRIGGER IF NOT EXISTS MarkRequestAsApproved
AFTER INSERT ON ApprovedRequest
FOR EACH ROW
BEGIN
    UPDATE DosimeterRequest
    SET decisionMade = 1
    WHERE idR = NEW.idR;
END;

---------------------------
-- 5. REJECTED REQUEST
---------------------------
CREATE TABLE IF NOT EXISTS RejectedRequest (
    idRej INTEGER PRIMARY KEY AUTOINCREMENT,
    idR INTEGER NOT NULL,
    idP INTEGER NOT NULL,
    rejectionDate DATETIME NOT NULL,
    comment TEXT NOT NULL,
    FOREIGN KEY (idR) REFERENCES DosimeterRequest(idR) ON DELETE CASCADE,
    FOREIGN KEY (idP) REFERENCES User(idU)
);

-----------------------------------------------------------
-- TRIGGER: Passar o pedido rejeitado para "Concluido"
-----------------------------------------------------------
CREATE TRIGGER IF NOT EXISTS MarkRequestAsRejected
AFTER INSERT ON RejectedRequest
FOR EACH ROW
BEGIN
    UPDATE DosimeterRequest
    SET decisionMade = 1
    WHERE idR = NEW.idR;
END;

------------------------
-- 6. CHANGE RECORD
------------------------
CREATE TABLE IF NOT EXISTS ChangeRecord (
    idCR INTEGER PRIMARY KEY AUTOINCREMENT,
    idA INTEGER NOT NULL,
    idUser INTEGER NOT NULL,
    requestType TEXT CHECK(requestType IN ('Suspender','Ativar')) NOT NULL,
    message TEXT NOT NULL,
    requestDate DATETIME NOT NULL,
    status TEXT CHECK(status IN ('Pendente','Concluido', 'Rejeitado')) DEFAULT 'Pendente',

    idAdmin INTEGER,
    decisionDate DATETIME,
    finalStatus TEXT CHECK(finalStatus IN ('Ativo','Suspenso')),
    adminNote TEXT,

    FOREIGN KEY (idA) REFERENCES ApprovedRequest(idA) ON DELETE CASCADE,
    FOREIGN KEY (idUser) REFERENCES User(idU),
    FOREIGN KEY (idAdmin) REFERENCES User(idU)
);

-------------------------------
-- 7. DOSIMETER ASSIGNMENT
-------------------------------
CREATE TABLE IF NOT EXISTS DosimeterAssignment (
    idDA INTEGER PRIMARY KEY AUTOINCREMENT,
    idA INTEGER NOT NULL,
    dosimeterSerial TEXT,
    assignmentDate DATETIME,
    nextReplacementDate DATETIME,
    periodicity TEXT CHECK(periodicity IN ('Mensal','Trimestral')) NOT NULL,
    status TEXT CHECK(status IN ('Por_Associar','Em_Uso','Suspenso')) DEFAULT 'Por_Associar',
    FOREIGN KEY (idA) REFERENCES ApprovedRequest(idA) ON DELETE CASCADE
);

-------------------------------------------------
-- TRIGGER: criar assignment automaticamente
-------------------------------------------------
CREATE TRIGGER IF NOT EXISTS CreateAssignmentAfterApprove
AFTER INSERT ON ApprovedRequest
FOR EACH ROW
BEGIN
    INSERT INTO DosimeterAssignment (idA, periodicity, status)
    VALUES (NEW.idA, NEW.periodicity, 'Por_Associar');
END;

-----------------------------
-- 8. ASSIGNMENT HISTORY
-----------------------------
CREATE TABLE IF NOT EXISTS DosimeterAssignmentHistory (
    idH INTEGER PRIMARY KEY AUTOINCREMENT,
    idA INTEGER NOT NULL,
    dosimeterSerial TEXT NOT NULL,
    assignmentDate DATETIME NOT NULL,
    removalDate DATETIME NOT NULL,
    FOREIGN KEY (idA) REFERENCES ApprovedRequest(idA) ON DELETE CASCADE
);

--------------------------------------------------------------------------------
-- TRIGGER: Guardar o histórico quando há troca de dosímetros
--------------------------------------------------------------------------------
CREATE TRIGGER IF NOT EXISTS AutoLogHistory
AFTER UPDATE ON DosimeterAssignment
FOR EACH ROW
WHEN OLD.dosimeterSerial IS NOT NULL 
     AND (NEW.dosimeterSerial != OLD.dosimeterSerial OR NEW.dosimeterSerial IS NULL)
BEGIN
    INSERT INTO DosimeterAssignmentHistory (idA, dosimeterSerial, assignmentDate, removalDate)
    VALUES (
        NEW.idA, 
        OLD.dosimeterSerial, 
        OLD.assignmentDate, 
        DATETIME('now')
    );
END;