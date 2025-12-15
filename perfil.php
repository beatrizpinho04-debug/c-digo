<?php
// perfil.php (Na raiz 'código/')

require_once 'database/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['idU'])) {
    header("Location: LogIn.php");
    exit();
}

$db = getDatabaseConnection();
$stmt = $db->prepare("SELECT * FROM User WHERE idU = ?");
$stmt->execute([$_SESSION['idU']]);
$user = $stmt->fetch();

// Caminho da foto
$foto_caminho_bd = $user['profilePic'];
// Se o script está na raiz, a pasta foto também está, logo é direto
$foto_display = !empty($foto_caminho_bd) ? $foto_caminho_bd : "foto/default.png";

$title = "Perfil do Utilizador";
// Includes procuram dentro da pasta templates
include 'templates/header.php';
?>
<body>
    <div class="page-wrapper">
        <?php include 'templates/nav.php'; ?>
        
        <div class="dashboard-container">
            <aside class="settings-sidebar">
                <nav class="settings-nav">
                    <a href="perfil.php" class="active">Perfil</a>
                    <a href="#">Segurança</a>
                    <a href="LogOut.php" style="color: #ef4444;">Sair</a>
                </nav>
            </aside>

            <main class="settings-content">
                <h1 class="section-title">Perfil</h1>
                <p class="section-subtitle">Gira as suas informações pessoais.</p>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                        <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    </div>
                <?php endif; ?>

                <form action="processa_perfil.php" method="POST" enctype="multipart/form-data" class="profile-form">
                    
                    <div class="avatar-upload">
                        <img src="<?php echo htmlspecialchars($foto_display); ?>" alt="Avatar" class="avatar-preview">
                        <div class="form-group">
                            <label for="profilePic" class="btn-upload">Alterar Foto</label>
                            <input type="file" id="profilePic" name="profilePic" style="display: none;" onchange="previewImage(this)">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nome Próprio</label>
                            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="surname">Apelido</label>
                            <input type="text" name="surname" id="surname" value="<?php echo htmlspecialchars($user['surname'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="birthDate">Data de Nascimento</label>
                            <input type="date" name="birthDate" id="birthDate" value="<?php echo htmlspecialchars($user['birthDate'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="sex">Sexo</label>
                            <select name="sex" id="sex">
                                <option value="" disabled <?php echo empty($user['sex']) ? 'selected' : ''; ?>>Selecione...</option>
                                <option value="Male" <?php echo ($user['sex'] ?? '') == 'Male' ? 'selected' : ''; ?>>Masculino</option>
                                <option value="Female" <?php echo ($user['sex'] ?? '') == 'Female' ? 'selected' : ''; ?>>Feminino</option>
                                <option value="Other" <?php echo ($user['sex'] ?? '') == 'Other' ? 'selected' : ''; ?>>Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phoneN">Telemóvel</label>
                        <input type="text" name="phoneN" id="phoneN" value="<?php echo htmlspecialchars($user['phoneN'] ?? ''); ?>" placeholder="+351 ...">
                    </div>

                    <div class="form-group">
                        <label for="password">Nova Palavra-passe</label>
                        <input type="password" name="password" id="password" placeholder="Deixe em branco para manter a atual">
                        <p style="font-size: 0.8rem; color: #64748b; margin-top: 5px;">Apenas preencha se desejar alterar a sua palavra-passe.</p>
                    </div>

                    <button type="submit" class="btn-save">Guardar Alterações</button>
                </form>
            </main>
        </div>

        <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        </script>

    <?php include 'templates/footer.php'; ?>