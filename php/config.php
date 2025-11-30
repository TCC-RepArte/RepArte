<?php
// Arquivo de configuração para chaves de API

// TMDB API
define('TMDB_API_KEY', 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJkNGY1NWJmYmRkYWU5MTRlMTI4NDE1YjczOTVhNzQ3NSIsIm5iZiI6MTc0ODAwMjMzOC4yNDgsInN1YiI6IjY4MzA2NjIyM2E3ZjBiNTc4MTgzNmY3NyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.QTXRWLYChv0Kly7kwVjvAKWxiuYGOW5hA6m9JHfIKHI');

// Spotify API
define('SPOTIFY_CLIENT_ID', '9a234ae20b4b4dd09866c1d22c56f5bb');
define('SPOTIFY_CLIENT_SECRET', '4ac381f6be504327b2ab6d8ce73f69f7');

$con = new mysqli("localhost", "root", '', "if0_40154094_reparte");

// Composer autoload (opcional - pode não estar disponível em hospedagem gratuita)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Função para obter token do Spotify (versão compatível com hospedagem gratuita)
function getSpotifyToken()
{
    $auth = base64_encode(SPOTIFY_CLIENT_ID . ':' . SPOTIFY_CLIENT_SECRET);

    // Usar file_get_contents em vez de Guzzle para compatibilidade
    $postData = http_build_query([
        'grant_type' => 'client_credentials'
    ]);

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'Authorization: Basic ' . $auth,
                'Content-Type: application/x-www-form-urlencoded',
                'Content-Length: ' . strlen($postData)
            ],
            'content' => $postData,
            'timeout' => 10
        ]
    ]);

    try {
        $response = file_get_contents('https://accounts.spotify.com/api/token', false, $context);

        if ($response === false) {
            error_log("Spotify: Falha ao fazer requisição");
            return null;
        }

        $data = json_decode($response, true);

        if (isset($data['access_token'])) {
            error_log("Spotify token obtido com sucesso");
            return $data['access_token'];
        } else {
            error_log("Spotify token não encontrado na resposta: " . json_encode($data));
            return null;
        }
    } catch (\Exception $e) {
        error_log("Spotify Error: " . $e->getMessage());
        return null;
    }
}