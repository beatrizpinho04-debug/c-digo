<?php
function output_header($title, $userType, $userName, $userSurname, $profilePic)
{ ?>
    <!DOCTYPE html>
    <html lang="pt">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/responsive.css">
        <link rel="icon" type="image/png" href="css/icon.svg">
    </head>

    <body>
        <div class="page-wrapper">
            <header class="top-bar">
                <div class="page-title-area">
                    <img src="css/icon.svg" width="32" height="32" alt="Logo">
                    <h1><?php echo $title; ?></h1>
                </div>
                <div class="user-area">
                    <div class="user-details">
                        <span class="user-name"><?php echo htmlspecialchars($userName . ' ' . $userSurname); ?></span>
                        <span class="user-role"><?php echo $userType; ?></span>
                    </div>
                    <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Perfil" class="profile-pic">
                    <a href="perfil.php" class="btn btn-header" title="Editar Meus Dados">⚙️</a>
                    <a href="LogOut.php" class="btn btn-header" title="Sair">🚪</a>
                </div>
            </header>
            <main class="container" style="margin-top: 30px;">
            <?php } ?>
            <?php
            function output_footer()
            { ?>
            </main>
            <footer class="footer mb2">Gestão de Dosimetros © 2025</footer>
        </div>
    </body>

    </html>
<?php } ?>