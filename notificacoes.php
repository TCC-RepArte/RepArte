<?php
session_start();
require_once 'php/config.php';
include 'vlibras_include.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login1.php");
    exit();
}
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
    <title>RepArte - Notificações</title>
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

            <a href="notificacoes.php" class="menu-btn active">
                <i class="bi bi-bell"></i> Notificações
            </a>
            <a href="privacidade.php" class="menu-btn">
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
            <h2 style="color: #fff; margin-bottom: 30px;">Notificações</h2>

            <div class="settings-list">
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Comentários</h3>
                        <p>Seja notificado quando alguém comentar em suas postagens.</p>
                    </div>
                    <div class="toggle" id="toggle-comentarios" data-tipo="notif_comentarios"
                        onclick="togglePreferencia(this)"></div>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Reações</h3>
                        <p>Receba alertas sobre curtidas e reações em seu conteúdo.</p>
                    </div>
                    <div class="toggle" id="toggle-reacoes" data-tipo="notif_reacoes" onclick="togglePreferencia(this)">
                    </div>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Respostas</h3>
                        <p>Saiba quando alguém responder aos seus comentários.</p>
                    </div>
                    <div class="toggle" id="toggle-respostas" data-tipo="notif_respostas"
                        onclick="togglePreferencia(this)"></div>
                </div>
            </div>

            <div class="actions" style="margin-top: 30px; justify-content: flex-end;">
                <button class="save" onclick="salvarPreferencias()">Salvar Preferências</button>
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

    <script>
        // Carregar preferências ao abrir a página
        document.addEventListener('DOMContentLoaded', function () {
            carregarPreferencias();
        });

        // Carregar preferências do banco
        function carregarPreferencias() {
            fetch('php/notificacoes_api.php?acao=buscar_preferencias')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.preferencias) {
                        const pref = data.preferencias;

                        // Ativar/desativar toggles conforme banco
                        if (pref.notif_comentarios == 1) {
                            document.getElementById('toggle-comentarios').classList.add('active');
                        }
                        if (pref.notif_reacoes == 1) {
                            document.getElementById('toggle-reacoes').classList.add('active');
                        }
                        if (pref.notif_respostas == 1) {
                            document.getElementById('toggle-respostas').classList.add('active');
                        }
                    } else {
                        // Se não tem preferências, ativar tudo por padrão
                        document.querySelectorAll('.toggle').forEach(toggle => {
                            toggle.classList.add('active');
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar preferências:', error);
                    // Em caso de erro, ativar tudo por padrão
                    document.querySelectorAll('.toggle').forEach(toggle => {
                        toggle.classList.add('active');
                    });
                });
        }

        // Toggle de preferência
        function togglePreferencia(element) {
            element.classList.toggle('active');
        }

        // Salvar preferências
        function salvarPreferencias() {
            const preferencias = {
                notif_comentarios: document.getElementById('toggle-comentarios').classList.contains('active'),
                notif_reacoes: document.getElementById('toggle-reacoes').classList.contains('active'),
                notif_respostas: document.getElementById('toggle-respostas').classList.contains('active')
            };

            fetch('php/salvar_preferencias_notificacoes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(preferencias)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: "Sucesso!",
                            text: "Preferências salvas com sucesso!",
                            icon: "success",
                            confirmButtonColor: "#ff6600",
                            background: "#1a1a1a",
                            color: "#fff"
                        });
                    } else {
                        Swal.fire({
                            title: "Erro!",
                            text: "Erro ao salvar preferências.",
                            icon: "error",
                            confirmButtonColor: "#ff6600",
                            background: "#1a1a1a",
                            color: "#fff"
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    Swal.fire({
                        title: "Erro!",
                        text: "Erro ao salvar preferências.",
                        icon: "error",
                        confirmButtonColor: "#ff6600",
                        background: "#1a1a1a",
                        color: "#fff"
                    });
                });
        }

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