<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        button.filme {
            width: 100px;
            height: 100px;
        }

        div.post1 {
            display: flex;
            flex-direction: column;
        }

        form {
            display: flex;
            flex-direction: row;
        }

        #imgFilmeSel{

            width: 200px;

        }
    </style>
</head>

<body>
    <form action="">
        <div class="post1">
            <div class="d1">
                <label for="">titulo</label>
                <input type="text" name="" id="">
            </div>
            <div class="d2">
                <label for="">texto</label>
                <input type="text" name="" id="">
            </div>
            <button type="submit">enviar</button>
        </div>
        <div class="post2">
            <img id="imgFilmeSel" src="" alt="">
        </div>
    </form>

    <form id="procurarForm">
        <label for="">procurar</label>
        <input type="text" name="" id="inp_ser">
        <button type="submit">enviar</button>
    </form>

    <div id="resposta">
        <p class="nomefilme"></p>
        <p class="descrifilme"></p>
    </div>

    <script src="../js/desc.js"></script>
    <script>
        document.getElementById('procurarForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Previne o comportamento padrão do formulário
            desc(); // Chama a função desc
        });
    </script>
</body>
</html>