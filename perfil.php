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


$isEditing = isset($_GET['edit']) && $_GET['edit'] == 'true';
$disabledStr = $isEditing ? '' : 'disabled';

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

<?php header_set($title); ?>

<body>
    <div class="page-wrapper">
        <?php nav_set(); ?>
        <main class="main-container" style="max-width: 45rem">
            <div class="profile-header-flex mb1_5">
                <div class="user-area">
                    <a href="index.php" class="btn voltar" title="Voltar">
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
                    <h1 class="card1-title">Perfil do Utilizador</h1>
                    <p class="subtítulo"><?php echo $isEditing ? "Edite os seus dados" : "Dados pessoais"; ?></p>
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
                                <input type="text" id="name" name="name" class="profile-input" value="<?php echo htmlspecialchars($user['name']); ?>" <?php echo $disabledStr; ?> required minlength="2">
                            </div>
                            <div>
                                <label class="profile-label" for="surname">Apelido</label>
                                <input type="text" id="surname" name="surname" class="profile-input" value="<?php echo htmlspecialchars($user['surname']); ?>" <?php echo $disabledStr; ?> required minlength="2">
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
                                    class="profile-input"
                                    value="<?php echo htmlspecialchars($user['phoneN']); ?>"
                                    <?php echo $disabledStr; ?>
                                    placeholder="+351912345678"
                                    pattern="^\+[0-9]{9,15}$"
                                    title="Deve começar com '+' seguido do indicativo e número (Ex: +351912345678)">
                                <p class="comentario-tel">
                                    Formato obrigatório: +CódigoPaís + Número (Ex: +351912345678)
                                </p>
                            </div>
                            <div class="g2">
                                <label class="profile-label" for="birthDate">Data de Nascimento</label>
                                <input type="date" id="birthDate" name="birthDate" class="profile-input" value="<?php echo htmlspecialchars($user['birthDate']); ?>" <?php echo $disabledStr; ?> required>
                            </div>

                            <?php if ($user['userType'] === 'Profissional de Saúde' || $user['userType'] === 'Físico Médico'): ?>
                                <div>
                                    <label class="profile-label">Profissão</label>
                                    <input type="text" class="profile-input" value="<?php echo htmlspecialchars($profession); ?>" disabled>
                                </div>
                                <div>
                                    <label class="profile-label">Departamento</label>
                                    <input type="text" name="department" class="profile-input" value="<?php echo htmlspecialchars($department); ?>"
                                        <?php echo ($isEditing && $user['userType'] === 'Profissional de Saúde') ? '' : 'disabled'; ?>>
                                </div>
                            <?php endif; ?>

                            <div class="g2">
                                <label class="profile-label" for="sex">Sexo</label>
                                <select id="sex" name="sex" class="profile-input" <?php echo $disabledStr; ?>>
                                    <option value="Male" <?php echo ($user['sex'] === 'Male') ? 'selected' : ''; ?>>Masculino</option>
                                    <option value="Female" <?php echo ($user['sex'] === 'Female') ? 'selected' : ''; ?>>Feminino</option>
                                    <option value="Other" <?php echo ($user['sex'] === 'Other') ? 'selected' : ''; ?>>Outro</option>
                                </select>
                            </div>

                            <?php if ($isEditing): ?>
                                <div class="g2">
                                    <hr class="nova-pass">
                                    <div class="mb1">
                                        <label class="profile-label">Nova Palavra-passe <span class="opcional">(Opcional)</span></label>
                                        <input type="password" name="password" class="profile-input" placeholder="Deixe vazio para manter a atual">
                                    </div>
                                    <div>
                                        <label class="profile-label" for="profilePic">Alterar Foto</label>
                                        <input type="file" id="profilePic" name="profilePic" class="profile-input" accept="image/*">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="profile-actions">
                            <?php if (!$isEditing): ?>
                                <a href="perfil.php?edit=true" class="btn btn-primary">
                                    <span class="lapis">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                        </svg>
                                        Editar
                                    </span>
                                </a>
                            <?php else: ?>
                                <div class="action-buttons">
                                    <a href="perfil.php" class="btn btn-cancel">Cancelar</a>
                                    <button type="submit" class="btn btn-save-profile">Guardar</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <?php renderFooter(); ?>
    </div>
</body>

</html>