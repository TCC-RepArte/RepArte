<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../front-end/perfil.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hammersmith+One&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <title>RepArte</title>
    <style>
        .mensagem {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }
        .erro {
            background-color: #ffecec;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>

<body>

    <header>
        <div class="interface">
            <div class="logo">
                <a>
                    <img src="../imagens/logo.png" alt="Logo do site">
                </a>
            </div>
        </div>
    </header>

    <section>
        <div class="box">
            <?php if(isset($_SESSION['erro_perfil'])): ?>
                <div class="mensagem erro">
                    <?php echo $_SESSION['erro_perfil']; unset($_SESSION['erro_perfil']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['sucesso_perfil'])): ?>
                <div class="mensagem sucesso">
                    <?php echo $_SESSION['sucesso_perfil']; unset($_SESSION['sucesso_perfil']); ?>
                </div>
            <?php endif; ?>

            <form enctype="multipart/form-data" method="post" action="../back-end/php/signup2.php">
                <div class="adc-img">
                    <button type="button" id="selft"><i id="ftperfil" class="bi bi-person-circle"><br>
                            <img id="imgselecionada" style="display: none;" src="" alt="">
                            <input type="file" style="display: none;" id="envfoto" name="envft" /></i></button>
                </div>
                <div class="texto">
                    <div class="nomeexb">
                        <label for="nome"></label>
                        <input class="nome" type="text" name="nomeexi" placeholder="Insira seu nome">
                    </div>
                    <div class="descricao">
                        <label for="descri"></label>
                        <input class="desc" type="text" name="desc" placeholder="Insira sua descrição">
                        <div class="botoes">
                            <button type="submit" class="btn-salvar">Salvar</button>
                        </div>
            </form>

        </div>
        </div>
        </div>
        </div>


        </div>
    </section>

    <script>
        const inpfoto = document.getElementById('envfoto');
        const selecionarfoto = document.getElementById('selft');
        const ftperfil = document.getElementById('ftperfil');

        // Estilo fixo para o botão
        selecionarfoto.style.width = '300px';
        selecionarfoto.style.height = '300px';
        selecionarfoto.style.borderRadius = '50%';
        selecionarfoto.style.overflow = 'hidden';
        selecionarfoto.style.padding = '0';

        // Ajusta o ícone para ocupar todo o espaço
        ftperfil.style.fontSize = '300px';
        ftperfil.style.width = '100%';
        ftperfil.style.height = '100%';
        ftperfil.style.display = 'flex';
        ftperfil.style.alignItems = 'center';
        ftperfil.style.justifyContent = 'center';

        selecionarfoto.addEventListener('click', (event) => {
            inpfoto.click();
        });

        inpfoto.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
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
                    img.style.width = '300px';
                    img.style.height = '300px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '50%';
                    img.style.display = 'flex';
                    img.style.alignItems = 'center';
                    img.style.justifyContent = 'center';
                    img.style.border = '30px';
                    img.style.borderColor = 'white';

                    // Substitui o ícone pela imagem
                    ftperfil.style.display = 'none';
                    selecionarfoto.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>


</body>

</html>