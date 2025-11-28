<?php
// Teste de conectividade simples com o Gmail
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Conexão SMTP</h1>";

$host = 'smtp.gmail.com';
$ports = [587, 465, 25];

foreach ($ports as $port) {
    echo "<p>Tentando conectar em <strong>$host:$port</strong>... ";

    $connection = @fsockopen($host, $port, $errno, $errstr, 10);

    if (is_resource($connection)) {
        echo "<span style='color:green'>✅ SUCESSO! Porta $port aberta.</span>";
        fclose($connection);
    } else {
        echo "<span style='color:red'>❌ FALHA. Porta $port fechada/bloqueada.</span>";
        echo "<br><small>Erro: $errstr ($errno)</small>";
    }
    echo "</p>";
}

echo "<hr>";
echo "<p><strong>Interpretação:</strong></p>";
echo "<ul>";
echo "<li>Se todas derem falha, o InfinityFree está bloqueando conexões de saída (comum em contas gratuitas antigas ou com restrição).</li>";
echo "<li>Se a porta 587 estiver aberta, o erro no PHPMailer é provavelmente senha ou configuração.</li>";
echo "</ul>";
?>