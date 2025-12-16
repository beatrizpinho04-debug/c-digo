<?php
session_start();
require_once 'database/connection.php';
require_once "templates/header.php";
require_once "templates/nav.php";
require_once "templates/footer.php";

if (!isset($_SESSION['idU'])) {
    header("Location: index.php");
    exit();
}

$db = getDatabaseConnection();
$stmt = $db->prepare("SELECT * FROM User WHERE idU = ?");
$stmt->execute([$_SESSION['idU']]);
$user = $stmt->fetch();

if (!$user) exit("Utilizador não encontrado.");


$profession = '';
$department = '';
$roleLabel = 'Utilizador';
$badgeClass = 'badge-gray';

if ($user['userType'] === 'Profissional de Saúde') {
    $stmtHP = $db->prepare("SELECT profession, department FROM HealthProfessional WHERE idU = ?");
    $stmtHP->execute([$user['idU']]);
    $hpData = $stmtHP->fetch();
    
    if ($hpData) {
        $profession = $hpData['profession'];
        $department = $hpData['department'];
    }
    
    $badgeClass = 'badge-blue'; 
    $roleLabel = !empty($profession) ? $profession : 'Profissional de Saúde';

} elseif ($user['userType'] === 'Físico Médico') {
    $profession = 'Físico Médico';
    $department = 'Física Médica';
    $badgeClass = 'badge-purple';
    $roleLabel = 'Físico Médico';
} elseif ($user['userType'] === 'Administrador') {
    $badgeClass = 'badge-red';
    $roleLabel = 'Administrador';
}

$initials = strtoupper(substr($user['name'], 0, 1) . substr($user['surname'], 0, 1));
$foto_url = !empty($user['profilePic']) ? $user['profilePic'] : null;
$title = "Perfil";
?>

<?php header_set(); ?>

<body>
    <div class="page-wrapper">
        <?php nav_set(); ?>
        <main class="profile-main-container">
            <div class="profile-header-flex">
                <div class="profile-header-left">
                    <a href="index.php" class="voltar" title="Voltar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12 19-7-7 7-7"></path>
                            <path d="M19 12H5"></path>
                        </svg>
                    </a>
                </div>
            </div>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert-container <?php echo ($_SESSION['message_type'] == 'success') ? 'alert-success' : 'alert-error'; ?>">
                    <?php 
                        echo $_SESSION['message']; 
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                    ?>
                </div>
            <?php endif; ?>
            <div class="card1">
                <div class="card1-header">
                    <h1 class="card1-title">Perfil de Utilizador</h1>
                    <p class="card1-desc" id="cardDescription">Edite as suas informações pessoais</p>
                </div>
                <div class="card1-content">
                    <div class="foto-section-gray">
                        <div class="foto-circle-large">
                            <?php if ($foto_url): ?>
                                <img src="<?php echo htmlspecialchars($foto_url); ?>" alt="Foto">
                            <?php else: ?>
                                <span><?php echo $initials; ?></span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="nome">
                                <?php echo htmlspecialchars($user['name'] . ' ' . $user['surname']); ?>
                            </p>
                            <span class="role-badge <?php echo $badgeClass; ?>" class="etiqueta">
                                <?php echo htmlspecialchars($roleLabel); ?>
                            </span>
                        </div>
                    </div>
                    <form id="profileForm" action="processa_perfil.php" method="POST" enctype="multipart/form-data">
                        <div class="profile-form-grid">
                            <div>
                                <label class="profile-label" for="name">Nome Próprio</label>
                                <input type="text" id="name" name="name" class="profile-input editable" value="<?php echo htmlspecialchars($user['name']); ?>" disabled required minlength="2">
                            </div>
                            <div>
                                <label class="profile-label" for="surname">Apelido</label>
                                <input type="text" id="surname" name="surname" class="profile-input editable" value="<?php echo htmlspecialchars($user['surname']); ?>" disabled required minlength="2">
                            </div>
                            <div>
                                <label class="profile-label">Email</label>
                                <input type="email" class="profile-input" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            </div>
                            <div>
                                <label class="profile-label">Telemóvel</label>
                                <input 
                                    type="text" 
                                    id="phoneN" 
                                    name="phoneN" 
                                    class="profile-input editable" 
                                    value="<?php echo htmlspecialchars($user['phoneN']); ?>" 
                                    disabled 
                                    placeholder="+351912345678"
                                    pattern="^\+[0-9]{9,15}$" 
                                    title="Deve começar com '+' seguido do indicativo e número (Ex: +351912345678)"
                                >
                                <p class="comentario-tel">
                                    Formato obrigatório: +CódigoPaís + Número (Ex: +351912345678)
                                </p>
                            </div>
                            <div class="col-span-2">
                                <label class="profile-label" for="birthDate">Data de Nascimento</label>
                                <input type="date" id="birthDate" name="birthDate" class="profile-input editable" value="<?php echo htmlspecialchars($user['birthDate']); ?>" disabled required>
                            </div>
                            <?php if ($user['userType'] === 'Profissional de Saúde' || $user['userType'] === 'Físico Médico'): ?>
                                <div>
                                    <label class="profile-label">Profissão</label>
                                    <input type="text" class="profile-input" value="<?php echo htmlspecialchars($profession); ?>" disabled>
                                </div>
                                <div>
                                    <label class="profile-label">Departamento</label>
                                    <input type="text" name="department" class="profile-input <?php echo ($user['userType'] === 'Profissional de Saúde') ? 'editable' : ''; ?>" value="<?php echo htmlspecialchars($department); ?>" disabled>
                                </div>
                            <?php endif; ?>
                            <div class="col-span-2">
                                <label class="profile-label" for="sex">Sexo</label>
                                <select id="sex" name="sex" class="profile-input editable" disabled>
                                    <option value="Male" <?php echo ($user['sex'] === 'Male') ? 'selected' : ''; ?>>Masculino</option>
                                    <option value="Female" <?php echo ($user['sex'] === 'Female') ? 'selected' : ''; ?>>Feminino</option>
                                    <option value="Other" <?php echo ($user['sex'] === 'Other') ? 'selected' : ''; ?>>Outro</option>
                                </select>
                            </div>
                            <div class="col-span-2" id="extraFields" style="display:none;">
                                <hr style="border: 0; border-top: 1px solid var(--border); margin: 1rem 0;">
                                <div style="margin-bottom: 1rem;">
                                    <label class="profile-label">Nova Palavra-passe <span style="font-weight:normal; color:#6b7280;">(Opcional)</span></label>
                                    <input type="password" name="password" class="profile-input editable" placeholder="Deixe vazio para manter a atual" disabled>
                                </div>
                                <div>
                                    <label class="profile-label" for="profilePic">Alterar Foto</label>
                                    <input type="file" id="profilePic" name="profilePic" class="profile-input editable" accept="image/*" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="profile-actions">
                            <button type="button" id="btnEdit" class="btn btn-edit-profile">
                                <span class="lapis">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                    Editar
                                </span>
                            </button>
                            <div id="editActions" class="editActions">
                                <button type="button" id="btnCancel" class="btn btn-cancel">Cancelar</button>
                                <button type="submit" class="btn btn-save-profile">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <?php renderFooter(); ?>
    </div>
    <script>
        const btnEdit = document.getElementById('btnEdit');
        const btnCancel = document.getElementById('btnCancel');
        const editActions = document.getElementById('editActions');
        const formInputs = document.querySelectorAll('.editable');
        const cardDescription = document.getElementById('cardDescription');
        const extraFields = document.getElementById('extraFields');
        
        btnEdit.addEventListener('click', () => {
            btnEdit.style.display = 'none';
            editActions.style.display = 'flex';
            extraFields.style.display = 'block';
            cardDescription.textContent = "Edite as suas informações";
            formInputs.forEach(input => { input.disabled = false; });
        });

        btnCancel.addEventListener('click', () => { location.reload(); });
    </script>
</body>
</html>