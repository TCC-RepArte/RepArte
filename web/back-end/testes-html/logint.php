<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form id="loginf" method="post" action="/web/back-end/php/signup.php">
        <label>usuario:</label>
        <input name="usuario" id="usuario" type="text">
        <label for="">email:</label>
        <input name="email" id="email" type="text">
        <label for="">senha:</label>
        <input name="senha" id="senha" type="password">
        <input name="confsenha" id="confsenha" type="password">
        <button type="submit">Enviar</button>
    </form>

    <button onclick="criarID()"></button>

    <script src="../back-end/js/signup.js"></script>

</body>
</html>

