<?php
// Arquivo de teste para verificar configurações do InfinityFree
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Configuração - RepArte InfinityFree</h1>";

// Teste 1: Conexão com banco de dados
echo "<h2>1. Teste de Conexão com Banco de Dados</h2>";
try {
    require_once 'php/config.php';
    if ($con->connect_error) {
        echo "❌ Erro de conexão: " . $con->connect_error;
    } else {
        echo "✅ Conexão com banco de dados estabelecida com sucesso!";
        echo "<br>Host: sql103.infinityfree.com";
        echo "<br>Banco: if0_40154094_reparte";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}

// Teste 2: Verificar APIs
echo "<h2>2. Teste de APIs</h2>";
echo "TMDB API Key: " . (defined('TMDB_API_KEY') ? "✅ Definida" : "❌ Não definida") . "<br>";
echo "Spotify Client ID: " . (defined('SPOTIFY_CLIENT_ID') ? "✅ Definida" : "❌ Não definida") . "<br>";
echo "Spotify Client Secret: " . (defined('SPOTIFY_CLIENT_SECRET') ? "✅ Definida" : "❌ Não definida") . "<br>";

// Teste 3: Verificar Spotify Token
echo "<h2>3. Teste de Spotify Token</h2>";
try {
    $token = getSpotifyToken();
    if ($token) {
        echo "✅ Token do Spotify obtido com sucesso!";
    } else {
        echo "❌ Falha ao obter token do Spotify";
    }
} catch (Exception $e) {
    echo "❌ Erro ao obter token: " . $e->getMessage();
}

// Teste 4: Verificar estrutura de arquivos
echo "<h2>4. Verificação de Estrutura</h2>";
$required_dirs = ['css', 'js', 'php', 'images', 'vendor'];
foreach ($required_dirs as $dir) {
    if (is_dir($dir)) {
        echo "✅ Pasta '$dir' existe<br>";
    } else {
        echo "❌ Pasta '$dir' não encontrada<br>";
    }
}

// Teste 5: Verificar arquivos principais
echo "<h2>5. Verificação de Arquivos Principais</h2>";
$required_files = [
    'index.php',
    'login1.php', 
    'cadastro.php',
    'telainicial.php',
    'php/config.php',
    'php/api-endpoints.php',
    'vendor/autoload.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✅ Arquivo '$file' existe<br>";
    } else {
        echo "❌ Arquivo '$file' não encontrado<br>";
    }
}

echo "<h2>✅ Teste Concluído!</h2>";
echo "<p>Se todos os testes passaram, o site está pronto para funcionar no InfinityFree!</p>";
?>
