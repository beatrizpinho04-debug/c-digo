<?php
// Se as variáveis não existirem, vai buscá-las à sessão
if (!isset($userName)) $userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Utilizador';
if (!isset($userType)) $userType = isset($_SESSION['userType']) ? $_SESSION['userType'] : '';
if (!isset($userSurname)) $userSurname = isset($_SESSION['surname']) ? $_SESSION['surname'] : '';
if (!isset($profilePic)) $profilePic = isset($_SESSION['profilePic']) ? $_SESSION['profilePic'] : 'foto\12225881.png';

//Definir a cor do Badge consoante o cargo
if ($userType === 'Físico Médico') {
    $badgeClass = 'badge-purple';
    $roleLabel = 'Físico Médico';
} elseif ($userType === 'Administrador') {
    $badgeClass = 'badge-red';
    $roleLabel = 'Administrador';
} elseif ($userType === 'Profissional de Saúde') {
    $badgeClass = 'badge-blue';
    $roleLabel = 'Prof. de Saúde';
}
?>

        <header class="header1">
            <div class="header-container">
                <div class="logo-area">
                    <div class="logo-box">
                        <img src="css/icon.svg" width="48" height="48" alt="Logo">
                    </div>
                    <div class="logo-text">
                        <h1>Gestão de Dosímetros</h1>
                        <p>Sistema de Gestão para Instituições de Saúde</p>
                    </div>
                </div>
                <div class="user-area"> 
                    <div class="user-info">
                        <span class="user-name">
                            <?php echo htmlspecialchars($userName . ' ' . $userSurname); ?>
                        </span>
                        <span class="role-badge <?php echo $badgeClass; ?>">
                            <?php echo $roleLabel; ?>
                        </span>
                    </div>

                    <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Perfil" class="profile-pic">

                    <div class="action-buttons">
                        <a href="perfil.php" class="btn-header" title="Dados de Perfil">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.09a2 2 0 0 1-1-1.74v-.47a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.35a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </a>
                        <a href="LogOut.php" class="btn-header" title="Sair">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" x2="9" y1="12" y2="12"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </header>