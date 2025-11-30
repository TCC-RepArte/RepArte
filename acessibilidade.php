<?php
session_start();
require_once 'php/config.php';
include 'vlibras_include.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login1.php");
    exit();
}

require_once 'php/perfil_dados.php';
require_once 'php/vlibras_config.php';

$perfil = buscaUsuario();
$vlibras_ativo = verificarVLibras();
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
    <title>RepArte - Acessibilidade</title>
</head>

<body>
    <div class="container">
        <!-- Menu lateral -->
        <aside class="sidebar">
            <div class="top-bar" style="justify-content: flex-start; margin-bottom: 30px;">
                <a href="configuracoes.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <a href="notificacoes.php" class="menu-btn">
                <i class="bi bi-bell"></i> Notificações
            </a>
            <a href="privacidade.php" class="menu-btn">
                <i class="bi bi-shield-lock"></i> Privacidade
            </a>
            <a href="seguranca.php" class="menu-btn">
                <i class="bi bi-lock"></i> Segurança
            </a>
            <a href="acessibilidade.php" class="menu-btn"
                style="background: rgba(255, 102, 0, 0.1); border-left: 3px solid #ff6600;">
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
            <div style="max-width: 600px; margin: 0 auto;">
                <h2 style="color: #fff; margin-bottom: 30px;">
                    <i class="bi bi-universal-access" style="margin-right: 10px;"></i>
                    Acessibilidade
                </h2>

                <div style="background: #2a2a2a; padding: 25px; border-radius: 12px; margin-bottom: 20px;">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <div>
                            <h3 style="color: #fff; margin: 0; font-size: 18px;">VLibras</h3>
                            <p style="color: #888; margin: 5px 0 0 0; font-size: 14px;">
                                Tradutor de conteúdo digital para Libras (Língua Brasileira de Sinais)
                            </p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="vlibras-toggle" <?= $vlibras_ativo ? 'checked' : '' ?>>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <div
                    style="background: rgba(255, 102, 0, 0.1); padding: 15px; border-radius: 8px; border-left: 3px solid #ff6600;">
                    <p style="color: #fff; margin: 0; font-size: 14px;">
                        <i class="bi bi-info-circle" style="margin-right: 8px;"></i>
                        O VLibras é uma ferramenta que traduz conteúdos digitais para a Língua Brasileira de Sinais.
                        Quando ativado, um avatar aparecerá em todas as páginas do site.
                    </p>
                </div>
            </div>
        </main>
    </div>

    <style>
        /* Estilo do switch toggle */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #555;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #ff6600;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }
    </style>

    <script>
        // Função de logout
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

        // Atualizar configuração do VLibras
        document.getElementById('vlibras-toggle').addEventListener('change', function () {
            const vlibrasAtivo = this.checked ? 1 : 0;

            // Envia requisição para atualizar no banco de dados
            fetch('php/atualizar_vlibras.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ vlibras_ativo: vlibrasAtivo })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: vlibrasAtivo ? 'VLibras Ativado!' : 'VLibras Desativado!',
                            text: 'A página será recarregada para aplicar as alterações.',
                            icon: 'success',
                            confirmButtonColor: '#ff6600',
                            background: '#1a1a1a',
                            color: '#fff',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro!',
                            text: 'Não foi possível atualizar a configuração.',
                            icon: 'error',
                            confirmButtonColor: '#ff6600',
                            background: '#1a1a1a',
                            color: '#fff'
                        });
                        // Reverte o toggle
                        this.checked = !this.checked;
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Ocorreu um erro ao processar sua solicitação.',
                        icon: 'error',
                        confirmButtonColor: '#ff6600',
                        background: '#1a1a1a',
                        color: '#fff'
                    });
                    // Reverte o toggle
                    this.checked = !this.checked;
                });
        });
    </script>

</body>

</html>