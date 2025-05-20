<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form enctype="multipart/form-data" method="post" action="">

        <label for="">digite seu nome de exibição</label><br>
        <input type="text" name="nome" id="nome"><br><br>
        <label for="">envie sua foto</label><br>
        <input type="file" name="foto" id="foto"><br><br><br>

        <button type="submit">enviar</button>

    </form>
</body>

<?php include "../php/signup2.php" ?>

</html>

