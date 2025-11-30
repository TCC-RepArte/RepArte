<?php
// Verifica se o VLibras está ativo para o usuário logado
function verificarVLibras()
{
    // Verifica se o usuário está logado
    if (!isset($_SESSION['id'])) {
        return false;
    }

    // Importa a conexão com o banco de dados
    require_once __DIR__ . '/config.php';
    global $con;

    $id_usuario = $_SESSION['id'];

    // Busca a configuração do usuário
    $sql = "SELECT vlibras_ativo FROM configuracoes_usuario WHERE id_usuario = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    // Se não existe configuração, cria uma com VLibras ativo por padrão
    if ($result->num_rows === 0) {
        $sqlInsert = "INSERT INTO configuracoes_usuario (id_usuario, vlibras_ativo) VALUES (?, 1)";
        $stmtInsert = $con->prepare($sqlInsert);
        $stmtInsert->bind_param("s", $id_usuario);
        $stmtInsert->execute();
        $stmtInsert->close();
        return true;
    }

    $row = $result->fetch_assoc();
    $stmt->close();

    return (bool) $row['vlibras_ativo'];
}

// Renderiza o código do VLibras se estiver ativo
function renderizarVLibras()
{
    if (verificarVLibras()) {
        echo '
<div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper>
        <div class="vw-plugin-top-wrapper"></div>
    </div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
    new window.VLibras.Widget("https://vlibras.gov.br/app");
</script>';
    }
}
?>