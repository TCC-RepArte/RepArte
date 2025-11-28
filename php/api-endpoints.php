<?php
// api-endpoints.php - Versão com Bearer Token para TMDB

// Desativar display de erros para produção, mas logar tudo
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Funções auxiliares para retornar JSON
function returnError($message) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $message]);
    exit;
}

function returnSuccess($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Configurações das APIs
define('TMDB_API_KEY', 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJkNGY1NWJmYmRkYWU5MTRlMTI4NDE1YjczOTVhNzQ3NSIsIm5iZiI6MTc0ODAwMjMzOC4yNDgsInN1YiI6IjY4MzA2NjIyM2E3ZjBiNTc4MTgzNmY3NyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.QTXRWLYChv0Kly7kwVjvAKWxiuYGOW5hA6m9JHfIKHI');
define('SPOTIFY_CLIENT_ID', '9a234ae20b4b4dd09866c1d22c56f5bb');
define('SPOTIFY_CLIENT_SECRET', '4ac381f6be504327b2ab6d8ce73f69f7');

// Adicionar cabeçalhos CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Função genérica para requisições HTTP
function makeHttpRequest($url, $method = 'GET', $headers = [], $postData = null) {
    $contextOptions = [
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $headers),
            'timeout' => 10,
            'ignore_errors' => true // Captura respostas HTTP com erro como conteúdo
        ]
    ];
    if ($postData !== null) {
        $contextOptions['http']['content'] = $postData;
    }

    $context = stream_context_create($contextOptions);
    $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
        error_log("HTTP Request Failed for URL: " . $url);
        return false;
    }
    return $response;
}

// Funções específicas para APIs
function getSpotifyToken() {
    $url = 'https://accounts.spotify.com/api/token';
    $data = [
        'grant_type' => 'client_credentials',
        'client_id' => SPOTIFY_CLIENT_ID,
        'client_secret' => SPOTIFY_CLIENT_SECRET
    ];
    $headers = ['Content-Type: application/x-www-form-urlencoded'];
    $response = makeHttpRequest($url, 'POST', $headers, http_build_query($data));

    if ($response) {
        $tokenData = json_decode($response, true);
        if (isset($tokenData['access_token'])) {
            return $tokenData['access_token'];
        }
    }
    error_log("Failed to get Spotify token. Response: " . ($response ? substr($response, 0, 200) : 'No response'));
    return false;
}

function makeTMDBRequest($endpoint, $params = []) {
    $baseUrl = 'https://api.themoviedb.org/3';
    $url = $baseUrl . $endpoint . '?' . http_build_query($params);
    $headers = [
        'Authorization: Bearer ' . TMDB_API_KEY,
        'Accept: application/json'
    ];
    return makeHttpRequest($url, 'GET', $headers);
}

function makeSpotifyApiRequest($endpoint, $accessToken, $params = []) {
    $baseUrl = 'https://api.spotify.com/v1';
    $url = $baseUrl . $endpoint . '?' . http_build_query($params);
    $headers = [
                'Authorization: Bearer ' . $accessToken,
                'Accept: application/json'
    ];
    return makeHttpRequest($url, 'GET', $headers);
}

function makeMetMuseumRequest($endpoint) {
    $baseUrl = 'https://collectionapi.metmuseum.org/public/collection/v1';
    $url = $baseUrl . $endpoint;
    return makeHttpRequest($url);
}

// Obter a ação solicitada
$action = $_GET['action'] ?? '';

    switch ($action) {
        case 'search_movies':
        case 'search_tv':
            $query = $_GET['query'] ?? '';
            $page = $_GET['page'] ?? 1;
        if (empty($query)) {
            returnSuccess(['results' => []]);
        }
        $endpoint = ($action === 'search_movies') ? '/search/movie' : '/search/tv';
        $result = makeTMDBRequest($endpoint, ['query' => $query, 'page' => $page, 'language' => 'pt-BR']);
        if ($result === false) {
            returnSuccess(['results' => []]);
        }
        returnSuccess(json_decode($result, true));
            break;

        case 'movie_details':
        case 'tv_details':
            $id = $_GET['id'] ?? '';
        if (empty($id)) {
            returnError('ID não fornecido');
        }
        $endpoint = ($action === 'movie_details') ? "/movie/{$id}" : "/tv/{$id}";
        $result = makeTMDBRequest($endpoint, ['language' => 'pt-BR']);
        if ($result === false) {
            returnError('Detalhes não encontrados');
        }
        returnSuccess(json_decode($result, true));
            break;

        case 'search_art':
            $query = $_GET['query'] ?? '';
        if (empty($query)) {
            returnSuccess(['objectIDs' => []]);
        }
        $result = makeMetMuseumRequest("/search?q=" . urlencode($query));
        if ($result === false) {
            returnSuccess(['objectIDs' => []]);
        }
        returnSuccess(json_decode($result, true));
            break;

        case 'art_details':
            $id = $_GET['id'] ?? '';
        if (empty($id)) {
            returnError('ID não fornecido');
        }
            $result = makeMetMuseumRequest("/objects/{$id}");
        if ($result === false) {
            returnError('Detalhes não encontrados');
        }
        returnSuccess(json_decode($result, true));
            break;

        case 'search_tracks':
            $query = $_GET['query'] ?? '';
        if (empty($query)) {
            returnSuccess(['tracks' => ['items' => []]]);
        }
            $accessToken = getSpotifyToken();
        if (!$accessToken) {
            returnSuccess(['tracks' => ['items' => []]]);
        }
        $result = makeSpotifyApiRequest('/search', $accessToken, ['q' => $query, 'type' => 'track', 'limit' => '10', 'market' => 'BR']);
        if ($result === false) {
            returnSuccess(['tracks' => ['items' => []]]);
        }
        returnSuccess(json_decode($result, true));
        break;

    case 'track_details':
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            returnError('ID não fornecido');
        }
        $accessToken = getSpotifyToken();
            if (!$accessToken) {
            returnError('Spotify temporariamente indisponível');
        }
        $result = makeSpotifyApiRequest("/tracks/{$id}", $accessToken, ['market' => 'BR']);
        if ($result === false) {
            returnError('Detalhes não encontrados');
        }
        returnSuccess(json_decode($result, true));
            break;

        default:
        returnError('Ação não reconhecida');
            break;
}
?>