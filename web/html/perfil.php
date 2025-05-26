<!DOCTYPE html>
<html lang="pt-br">

<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

?>

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
</head>

<body>

    <header>
        <div class="interface">
            <div class="logo">
                <a>
                    <img src="../imagens/logo.png" alt="Logo do site">
                </a>
            </div>
    </header>

    <section>
        <div class="box">

            <form enctype="multipart/form-data" method="post" action="../back-end/php/signup2.php">
                <div class="adc-img">
                    <button type="button" id="selft"><i id="ftperfil" class="bi bi-person-circle"><br>
                    <img src="" alt=""> 
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

    foto = selecionarfoto.addEventListener('click', (event) => {
        inpfoto.click();
        var foto = this.files[0];
    })

    if(foto){

        const buttonft = document.getElementById('envfoto');

        buttonft.setAttribute("class", "")

    }
    

</script>


</body>

</html>