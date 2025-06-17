<?php
require_once 'config.php';

// Adicionar cabeçalhos CORS para permitir requisições do front-end
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400'); // cache por 1 dia

// Responder imediatamente para requisições OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');

// Função para fazer requisições à API TMDB
function makeTMDBRequest($endpoint, $params = []) {
    // Define a URL base da API TMDB
    $baseUrl = 'https://api.themoviedb.org/3';
    $url = $baseUrl . $endpoint;
    
    // Log para depuração
    error_log("TMDB Request URL: " . $url);
    error_log("TMDB Request Params: " . json_encode($params));
    
    // Cria uma instância do cliente Guzzle para fazer requisições HTTP
    $client = new \GuzzleHttp\Client();
    
    try {
        // Faz a requisição GET para a API TMDB
        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . TMDB_API_KEY,
                'accept' => 'application/json'
            ],
            'query' => $params
        ]);
        
        // Retorna o corpo da resposta como string
        $responseBody = $response->getBody()->getContents();
        error_log("TMDB Response: " . substr($responseBody, 0, 200) . "...");
        return $responseBody;
    } catch (\Exception $e) {
        // Em caso de erro, retorna uma mensagem de erro em formato JSON
        error_log("TMDB Error: " . $e->getMessage());
        return json_encode(['error' => 'Erro na requisição TMDB: ' . $e->getMessage()]);
    }
}

// Função para fazer requisições à API Spotify
function makeSpotifyRequest($endpoint, $params = []) {
    // Define a URL base da API Spotify
    $baseUrl = 'https://api.spotify.com/v1';
    $url = $baseUrl . $endpoint;
    
    // Log para depuração
    error_log("Spotify Request URL: " . $url);
    error_log("Spotify Request Params: " . json_encode($params));
    
    // Obtém o token de acesso do Spotify
    $token = getSpotifyToken();
    if (!$token) {
        error_log("Spotify Error: Failed to get token");
        return json_encode(['error' => 'Failed to get Spotify token']);
    }
    
    // Cria uma instância do cliente Guzzle para fazer requisições HTTP
    $client = new \GuzzleHttp\Client();
    
    try {
        // Faz a requisição GET para a API Spotify
        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ],
            'query' => $params
        ]);
        
        // Retorna o corpo da resposta como string
        $responseBody = $response->getBody()->getContents();
        error_log("Spotify Response: " . substr($responseBody, 0, 200) . "...");
        return $responseBody;
    } catch (\Exception $e) {
        // Em caso de erro, retorna uma mensagem de erro em formato JSON
        error_log("Spotify Error: " . $e->getMessage());
        return json_encode(['error' => 'Erro na requisição Spotify: ' . $e->getMessage()]);
    }
}

// Endpoint para buscar filmes
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'search_movies') {
    $query = $_GET['query'] ?? '';
    $response = makeTMDBRequest('/search/movie', [
        'query' => $query,
        'include_adult' => 'true',
        'language' => 'pt-BR',
        'page' => '1'
    ]);
    echo $response;
    exit;
}

// Endpoint para buscar séries
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'search_tv') {
    $query = $_GET['query'] ?? '';
    $response = makeTMDBRequest('/search/tv', [
        'query' => $query,
        'include_adult' => 'true',
        'language' => 'pt-BR',
        'page' => '1'
    ]);
    echo $response;
    exit;
}

// Endpoint para buscar músicas
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'search_tracks') {
    $query = $_GET['query'] ?? '';
    $response = makeSpotifyRequest('/search', [
        'q' => $query,
        'type' => 'track',
        'market' => 'BR',
        'limit' => '20'
    ]);
    echo $response;
    exit;
}

// Endpoint para obter detalhes de um filme
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'movie_details') {
    $id = $_GET['id'] ?? '';
    error_log("Recebendo requisição para detalhes do filme ID: " . $id);
    
    if (empty($id)) {
        echo json_encode(['error' => 'ID do filme não fornecido']);
        exit;
    }
    
    $response = makeTMDBRequest("/movie/{$id}", [
        'language' => 'pt-BR'
    ]);
    echo $response;
    exit;
}

// Endpoint para obter detalhes de uma série
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'tv_details') {
    $id = $_GET['id'] ?? '';
    error_log("Recebendo requisição para detalhes da série ID: " . $id);
    
    if (empty($id)) {
        echo json_encode(['error' => 'ID da série não fornecido']);
        exit;
    }
    
    $response = makeTMDBRequest("/tv/{$id}", [
        'language' => 'pt-BR'
    ]);
    echo $response;
    exit;
}

// Endpoint para obter detalhes de uma música
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'track_details') {
    $id = $_GET['id'] ?? '';
    error_log("Recebendo requisição para detalhes da música ID: " . $id);
    
    if (empty($id)) {
        echo json_encode(['error' => 'ID da música não fornecido']);
        exit;
    }
    
    $response = makeSpotifyRequest("/tracks/{$id}");
    echo $response;
    exit;
}

// Se nenhum endpoint válido for encontrado
http_response_code(404);
echo json_encode(['error' => 'Endpoint não encontrado']); 