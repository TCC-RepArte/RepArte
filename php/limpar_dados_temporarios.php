<?php
// Sistema de limpeza automática de dados temporários
// Deve ser chamado periodicamente (ex: ao carregar páginas principais)

require_once 'config.php';

global $con;

try {
    // 1. Limpar códigos de verificação expirados
    $sql = "DELETE FROM codigos_verificacao WHERE data_expiracao < NOW()";
    $con->query($sql);
    $codigos_expirados = $con->affected_rows;

    // 2. Limpar códigos com tentativas esgotadas (5 ou mais)
    $sql = "DELETE FROM codigos_verificacao WHERE tentativas >= 5";
    $con->query($sql);
    $codigos_bloqueados = $con->affected_rows;

    // 3. Limpar tokens de recuperação de senha expirados
    $sql = "DELETE FROM recuperacao_senha WHERE data_expiracao < NOW()";
    $con->query($sql);
    $tokens_expirados = $con->affected_rows;

    // 4. Limpar tokens já usados com mais de 24 horas
    $sql = "DELETE FROM recuperacao_senha WHERE usado = 1 AND data_criacao < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $con->query($sql);
    $tokens_antigos = $con->affected_rows;

    // 5. Limpar códigos já usados com mais de 24 horas
    $sql = "DELETE FROM codigos_verificacao WHERE usado = 1 AND data_criacao < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $con->query($sql);
    $codigos_antigos = $con->affected_rows;

    // Log dos resultados (opcional)
    $total_limpeza = $codigos_expirados + $codigos_bloqueados + $tokens_expirados + $tokens_antigos + $codigos_antigos;

    if ($total_limpeza > 0) {
        error_log("Limpeza automática concluída: {$total_limpeza} registros removidos");
        error_log(" - Códigos expirados: {$codigos_expirados}");
        error_log(" - Códigos bloqueados: {$codigos_bloqueados}");
        error_log(" - Tokens expirados: {$tokens_expirados}");
        error_log(" - Tokens antigos: {$tokens_antigos}");
        error_log(" - Códigos antigos: {$codigos_antigos}");
    }

    return true;

} catch (Exception $e) {
    error_log("Erro na limpeza automática: " . $e->getMessage());
    return false;
}
?>