<?php
// proxy.php?url=URL_DA_IMAGEM

if (!isset($_GET['url'])) {
    http_response_code(400);
    echo 'URL é necessária.';
    exit;
}

$url = $_GET['url'];

// Validação mínima da url da imagem
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo 'URL inválida.';
    exit;
}

// Inicia o cURL
$ch = curl_init($url);

// Remove referer e user-agent para evitar bloqueio em imagens dos livros
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Referer: ',
    'User-Agent: Mozilla/5.0 (compatible; ProxyBot/1.0)'
]);

$response = curl_exec($ch);

if ($response === false) {
    http_response_code(502);
    echo 'Erro ao buscar imagem';
    exit;
}

// Separa cabeçalhos e corpo
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $header_size);
$body = substr($response, $header_size);

// Extrai o Content-Type para repassar
$content_type = 'image/jpeg';

if (preg_match('/Content-Type:\s*(image\/[a-zA-Z0-9]+)/i', $headers, $matches)) {
    $content_type = $matches[1];
}

curl_close($ch);

// Envia os headers corretos e o corpo da imagem
header('Content-Type: ' . $content_type);
// cache de 1 dia
header('Cache-Control: max-age=86400'); 
echo $body;
?>