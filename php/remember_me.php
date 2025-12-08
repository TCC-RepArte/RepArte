<?php
// Funções para "Lembre de mim"

// Gera um token seguro
function gerarTokenLembreMim()
{
    return bin2hex(random_bytes(32));
}

// Salva o token no banco e no cookie
function salvarTokenLembreMim($con, $id_usuario)
{
    $token = gerarTokenLembreMim();
    $expira = date('Y-m-d H:i:s', strtotime('+30 days'));

    // Atualiza o token no banco
    $sql = "UPDATE login SET remember_token = ?, remember_token_expira = ? WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sss", $token, $expira, $id_usuario);

    if ($stmt->execute()) {
        // Define o cookie por 30 dias
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        setcookie('remember_user', $id_usuario, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        return true;
    }

    return false;
}

// Verifica o token de "lembre de mim"
function verificarTokenLembreMim($con)
{
    if (!isset($_COOKIE['remember_token']) || !isset($_COOKIE['remember_user'])) {
        return false;
    }

    $token = $_COOKIE['remember_token'];
    $id_usuario = $_COOKIE['remember_user'];

    // Busca o usuário com o token
    $sql = "SELECT id, usuario, email, remember_token_expira FROM login 
            WHERE id = ? AND remember_token = ? AND remember_token_expira > NOW()";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $id_usuario, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Renova o token
        salvarTokenLembreMim($con, $usuario['id']);

        // Cria a sessão
        $_SESSION['usuario'] = $usuario['usuario'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['id'] = $usuario['id'];

        return true;
    }

    // Token inválido, remove os cookies
    limparTokenLembreMim();
    return false;
}

// Remove o token de "lembre de mim"
function limparTokenLembreMim()
{
    setcookie('remember_token', '', time() - 3600, '/');
    setcookie('remember_user', '', time() - 3600, '/');
}

// Remove o token do banco ao fazer logout
function removerTokenDoBanco($con, $id_usuario)
{
    $sql = "UPDATE login SET remember_token = NULL, remember_token_expira = NULL WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $id_usuario);
    $stmt->execute();
}
?>