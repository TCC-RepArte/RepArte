<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require 'php/config.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login1.php");
    exit();
}

require_once 'php/perfil_dados.php';

// Buscar dados do usuário logado
$perfil = buscaUsuario();

// Verificar se o perfil foi encontrado
if (!$perfil) {
    header("Location: perfil.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/perfil.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hammersmith+One&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/mensagens.css">
    <title>RepArte - Editar Perfil</title>
    <style>
        .mensagem.erro {
            background-color: #ffecec;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 12px;
            margin: 15px 0;
            border-radius: 8px;
            text-align: center;
        }

        .mensagem.sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 12px;
            margin: 15px 0;
            border-radius: 8px;
            text-align: center;
        }
    </style>
</head>

<body>

    <header>
        <div class="interface">
            <div class="logo">
                <a href="meu_perfil.php">
                    <img src="images/logo.png" alt="Logo do site">
                </a>
            </div>
        </div>
    </header>

    <section>
        <div class="box">
            <?php if (isset($_SESSION['erro_perfil'])): ?>
                <div class="mensagem erro">
                    <?php echo $_SESSION['erro_perfil'];
                    unset($_SESSION['erro_perfil']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['sucesso_perfil'])): ?>
                <div class="mensagem sucesso">
                    <?php echo $_SESSION['sucesso_perfil'];
                    unset($_SESSION['sucesso_perfil']); ?>
                </div>
            <?php endif; ?>

            <form enctype="multipart/form-data" method="post" action="php/atualizar_perfil.php">
                <div class="adc-img">
                    <button type="button" id="selft">
                        <?php if (!empty($perfil['caminho']) && file_exists($perfil['caminho'])): ?>
                            <img src="<?= htmlspecialchars($perfil['caminho']) ?>" alt="Foto de perfil">
                        <?php else: ?>
                            <i id="ftperfil" class="bi bi-person-circle"></i>
                        <?php endif; ?>
                    </button>
                    <input type="file" id="envfoto" name="envft"
                        accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" />
                </div>
                <div class="texto">
                    <div class="nomeexb">
                        <label for="nome"></label>
                        <input class="nome" type="text" name="nomeexi" placeholder="Insira seu nome"
                            value="<?= htmlspecialchars($perfil['nomexi'] ?? '') ?>">
                    </div>
                    <div class="descricao">
                        <label for="descri"></label>
                        <input class="desc" type="text" name="desc" placeholder="Insira sua descrição"
                            value="<?= htmlspecialchars($perfil['descri'] ?? '') ?>">
                        <div class="botoes">
                            <button type="submit" class="btn-salvar">Salvar Alterações</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </section>

    <script>
        const inpfoto = document.getElementById('envfoto');
        const selecionarfoto = document.getElementById('selft');
        const ftperfil = document.getElementById('ftperfil');

        selecionarfoto.addEventListener('click', (event) => {
            inpfoto.click();
        });

        inpfoto.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                // Validar se é uma imagem
                const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

                if (!tiposPermitidos.includes(file.type)) {
                    alert('Por favor, selecione apenas arquivos de imagem (JPG, PNG, GIF ou WEBP)');
                    inpfoto.value = '';
                    return;
                }

                // Validar tamanho do arquivo (máx 5MB)
                const tamanhoMaximo = 5 * 1024 * 1024;
                if (file.size > tamanhoMaximo) {
                    alert('A imagem deve ter no máximo 5MB');
                    inpfoto.value = '';
                    return;
                }

                // Remove a imagem anterior se existir
                const imgAnterior = selecionarfoto.querySelector('img');
                if (imgAnterior) {
                    imgAnterior.remove();
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    // Cria uma nova imagem
                    const img = document.createElement('img');
                    img.src = e.target.result;

                    // Esconde o ícone
                    if (ftperfil) {
                        ftperfil.style.display = 'none';
                    }

                    selecionarfoto.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>


</body>

</html>