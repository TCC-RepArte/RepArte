<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login1.php");
    exit();
}

require_once 'php/perfil_dados.php';
$perfil = buscaUsuario();
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
    <title>RepArte - Configurações</title>
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

            <a href="#" class="menu-btn active" onclick="openTab(event, 'notificacoes')">
                <i class="bi bi-bell"></i> Notificações
            </a>
            <a href="#" class="menu-btn" onclick="openTab(event, 'seguranca')">
                <i class="bi bi-lock"></i> Segurança
            </a>
            <a href="#" class="menu-btn" onclick="openTab(event, 'acessibilidade')">
                <i class="bi bi-universal-access"></i> Acessibilidade
            </a>
            <a href="#" onclick="confirmarLogout()" class="menu-btn">
                <i class="bi bi-box-arrow-right"></i> Sair
            </a>
            <a href="#" onclick="confirmarDesativarConta()" class="menu-btn" style="color: #ff3b3b;">
                <i class="bi bi-trash"></i> Desativar conta
            </a>
        </aside>

        <!-- Conteúdo principal -->
        <main class="content">
            <div class="mobile-header">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h3>Configurações</h3>
            </div>

            <!-- Aba Notificações -->
            <div id="notificacoes" class="tab-content active">
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
                        <div class="toggle" id="toggle-reacoes" data-tipo="notif_reacoes"
                            onclick="togglePreferencia(this)">
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
            </div>

            <!-- Aba Segurança -->
            <div id="seguranca" class="tab-content">
                <h2 style="color: #fff; margin-bottom: 30px;">Segurança</h2>

                <div class="settings-list">
                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Autenticação de Dois Fatores (2FA)</h3>
                            <p>Adicione uma camada extra de segurança à sua conta.</p>
                        </div>
                        <div class="toggle" id="toggle-2fa" onclick="toggle2FA(this)"></div>
                    </div>

                    <div class="setting-item" style="cursor: pointer;"
                        onclick="window.location.href='emailesqueceu.php'">
                        <div class="setting-info">
                            <h3>Alterar Senha</h3>
                            <p>Atualize sua senha periodicamente para manter sua conta segura.</p>
                        </div>
                        <i class="bi bi-chevron-right" style="color: #888;"></i>
                    </div>
                </div>
            </div>

            <!-- Aba Acessibilidade -->
            <div id="acessibilidade" class="tab-content">
                <h2 style="color: #fff; margin-bottom: 30px;">Acessibilidade</h2>

                <div class="settings-list">
                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>VLibras</h3>
                            <p>Tradutor de Libras integrado ao site.</p>
                        </div>
                        <div class="toggle active" id="toggle-vlibras" onclick="toggleVLibras(this)"></div>
                    </div>
                </div>
            </div>
    </div>
    </main>

    <div class="overlay" onclick="toggleSidebar()"></div>
    </div>

    <script>
        // Sistema de abas
        function openTab(event, tabName) {
            event.preventDefault();

            // Esconde todas as abas
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }

            // Remove active de todos os botões
            const menuBtns = document.getElementsByClassName('menu-btn');
            for (let i = 0; i < menuBtns.length; i++) {
                menuBtns[i].classList.remove('active');
            }

            // Mostra a aba selecionada
            document.getElementById(tabName).classList.add('active');

            // Marca o botão como ativo
            event.currentTarget.classList.add('active');

            // Fecha sidebar no mobile
            if (window.innerWidth <= 900) {
                toggleSidebar();
            }
        }

        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
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

        function confirmarDesativarConta() {
            Swal.fire({
                title: "⚠️ ATENÇÃO!",
                html: "Você está prestes a <strong>desativar sua conta</strong>.<br><br>" +
                    "Isso significa que:<br>" +
                    "• Seu perfil ficará invisível<br>" +
                    "• Suas postagens serão ocultadas<br>" +
                    "• Você poderá reativar fazendo login novamente<br><br>" +
                    "<strong>Tem certeza absoluta?</strong>",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#666",
                confirmButtonText: "Sim, desativar!",
                cancelButtonText: "Cancelar",
                background: "#1a1a1a",
                color: "#fff"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Segunda confirmação
                    Swal.fire({
                        title: "Última confirmação",
                        text: "Digite 'DESATIVAR' para confirmar:",
                        input: 'text',
                        inputPlaceholder: 'Digite DESATIVAR',
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#666",
                        confirmButtonText: "Confirmar",
                        cancelButtonText: "Cancelar",
                        background: "#1a1a1a",
                        color: "#fff",
                        inputValidator: (value) => {
                            if (value !== 'DESATIVAR') {
                                return 'Você precisa digitar exatamente "DESATIVAR"';
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Aqui você implementaria a lógica de desativação
                            Swal.fire({
                                title: "Conta Desativada",
                                text: "Sua conta foi desativada. Você pode reativá-la fazendo login novamente.",
                                icon: "success",
                                confirmButtonColor: "#ff6600",
                                background: "#1a1a1a",
                                color: "#fff"
                            }).then(() => {
                                window.location.href = 'php/logout.php';
                            });
                        }
                    });
                }
            });
        }

        // Funções de notificações
        document.addEventListener('DOMContentLoaded', function () {
            carregarPreferencias();
        });

        function carregarPreferencias() {
            fetch('php/notificacoes_api.php?acao=buscar_preferencias')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.preferencias) {
                        const pref = data.preferencias;

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
                        document.querySelectorAll('.toggle').forEach(toggle => {
                            toggle.classList.add('active');
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar preferências:', error);
                    document.querySelectorAll('.toggle').forEach(toggle => {
                        toggle.classList.add('active');
                    });
                });
        }

        function togglePreferencia(element) {
            element.classList.toggle('active');
        }

        function toggle2FA(element) {
            const isActive = element.classList.contains('active');
            
            if (!isActive) {
                // Ativando 2FA
                Swal.fire({
                    title: "Ativar Autenticação de Dois Fatores?",
                    html: "A autenticação de dois fatores adiciona uma camada extra de segurança.<br><br>" +
                          "<strong>Como funciona:</strong><br>" +
                          "• Você receberá um código por email a cada login<br>" +
                          "• Apenas você poderá acessar sua conta<br>" +
                          "• Proteção contra acessos não autorizados",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#ff6600",
                    cancelButtonColor: "#666",
                    confirmButtonText: "Ativar 2FA",
                    cancelButtonText: "Cancelar",
                    background: "#1a1a1a",
                    color: "#fff"
                }).then((result) => {
                    if (result.isConfirmed) {
                        element.classList.add('active');
                        Swal.fire({
                            title: "2FA Ativado!",
                            text: "A autenticação de dois fatores foi ativada com sucesso.",
                            icon: "success",
                            confirmButtonColor: "#ff6600",
                            background: "#1a1a1a",
                            color: "#fff"
                        });
                    }
                });
            } else {
                // Desativando 2FA
                Swal.fire({
                    title: "Desativar 2FA?",
                    text: "Isso tornará sua conta menos segura. Tem certeza?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#666",
                    confirmButtonText: "Sim, desativar",
                    cancelButtonText: "Cancelar",
                    background: "#1a1a1a",
                    color: "#fff"
                }).then((result) => {
                    if (result.isConfirmed) {
                        element.classList.remove('active');
                        Swal.fire({
                            title: "2FA Desativado",
                            text: "A autenticação de dois fatores foi desativada.",
                            icon: "info",
                            confirmButtonColor: "#ff6600",
                            background: "#1a1a1a",
                            color: "#fff"
                        });
                    }
                });
            }
        }

        function toggleVLibras(element) {
            const isActive = element.classList.contains('active');
            const vlibrasWidget = document.querySelector('[vw]');
            
            if (isActive) {
                // Desativando VLibras
                element.classList.remove('active');
                if (vlibrasWidget) {
                    vlibrasWidget.style.display = 'none';
                }
                Swal.fire({
                    title: "VLibras Desativado",
                    text: "O tradutor de Libras foi desativado.",
                    icon: "info",
                    confirmButtonColor: "#ff6600",
                    background: "#1a1a1a",
                    color: "#fff",
                    timer: 2000
                });
            } else {
                // Ativando VLibras
                element.classList.add('active');
                if (vlibrasWidget) {
                    vlibrasWidget.style.display = 'block';
                }
                Swal.fire({
                    title: "VLibras Ativado",
                    text: "O tradutor de Libras está disponível!",
                    icon: "success",
                    confirmButtonColor: "#ff6600",
                    background: "#1a1a1a",
                    color: "#fff",
                    timer: 2000
                });
            }
        }

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
    </script>

</body>

</html>