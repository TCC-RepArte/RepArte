<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login1.php");
    exit();
}

require_once 'php/config.php';
include 'vlibras_include.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/configuraçoes.css">
    <title>RepArte - Privacidade</title>
</head>

<body>
    <div class="container">
        <!-- Menu lateral -->
        <aside class="sidebar">
            <div class="top-bar" style="justify-content: flex-start; margin-bottom: 30px;">
                <a href="telainicial.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <a href="notificacoes.php" class="menu-btn">
                <i class="bi bi-bell"></i> Notificações
            </a>
            <a href="privacidade.php" class="menu-btn active">
                <i class="bi bi-shield-lock"></i> Privacidade
            </a>
            <a href="seguranca.php" class="menu-btn">
                <i class="bi bi-lock"></i> Segurança
            </a>
            <a href="#" class="menu-btn">
                <i class="bi bi-universal-access"></i> Acessibilidade
            </a>
            <a href="#" onclick="confirmarLogout()" class="menu-btn">
                <i class="bi bi-box-arrow-right"></i> Sair
            </a>
            <a href="#" class="menu-btn" style="color: #ff3b3b;">
                <i class="bi bi-trash"></i> Desativar conta
            </a>
        </aside>

        <!-- Conteúdo principal -->
        <main class="content">
            <h2 style="color: #fff; margin-bottom: 30px;">Privacidade</h2>

            <div class="settings-list">
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Perfil Privado</h3>
                        <p>Apenas seguidores aprovados podem ver suas publicações.</p>
                    </div>
                    <div class="toggle" onclick="this.classList.toggle('active')"></div>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Status Online</h3>
                        <p>Permitir que outros vejam quando você está online.</p>
                    </div>
                    <div class="toggle active" onclick="this.classList.toggle('active')"></div>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Recibos de Leitura</h3>
                        <p>Mostrar quando você visualizou mensagens.</p>
                    </div>
                    <div class="toggle active" onclick="this.classList.toggle('active')"></div>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Permitir menções</h3>
                        <p>Quem pode mencionar você em comentários e postagens.</p>
                    </div>
                    <div class="toggle active" onclick="this.classList.toggle('active')"></div>
                </div>
            </div>

            <div class="actions" style="margin-top: 30px; justify-content: flex-end;">
                <button class="save">Salvar Preferências</button>
            </div>
        </main>
    </div>

    <script>
        function confirmarLogout() {
            Swal.fire({
                title: "Tem certeza que deseja sair?",
                text: "Você precisará fazer login novamente.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ff6600",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sim, sair!",
                cancelButtonText: "Cancelar",
                background: "#1a1a1a",
                color: "#fff"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'php/logout.php';
                }
            });
        }
    </script>
</body>

</html>