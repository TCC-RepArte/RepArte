<?php
// Arquivo de configuração para chaves de API

// TMDB API
define('TMDB_API_KEY', 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJkNGY1NWJmYmRkYWU5MTRlMTI4NDE1YjczOTVhNzQ3NSIsIm5iZiI6MTc0ODAwMjMzOC4yNDgsInN1YiI6IjY4MzA2NjIyM2E3ZjBiNTc4MTgzNmY3NyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.QTXRWLYChv0Kly7kwVjvAKWxiuYGOW5hA6m9JHfIKHI');

// Spotify API
define('SPOTIFY_CLIENT_ID', '9a234ae20b4b4dd09866c1d22c56f5bb');
define('SPOTIFY_CLIENT_SECRET', '4ac381f6be504327b2ab6d8ce73f69f7');

$con = new mysqli("localhost", "root", '', "reparte");

require_once __DIR__ . '/../../../vendor/autoload.php';

// Função para obter token do Spotify
function getSpotifyToken() {
    $auth = base64_encode(SPOTIFY_CLIENT_ID . ':' . SPOTIFY_CLIENT_SECRET);
    
    $client = new \GuzzleHttp\Client();
    
    try {
        $response = $client->request('POST', 'https://accounts.spotify.com/api/token', [
            'headers' => [
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ]);
        
        $data = json_decode($response->getBody(), true);
        return $data['access_token'] ?? null;
    } catch (\Exception $e) {
        return null;
    }
} 