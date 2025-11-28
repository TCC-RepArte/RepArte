<?php
// Configuração do Google OAuth

// Credenciais do Google OAuth (você precisa criar no Google Cloud Console)
// Acesse: https://console.cloud.google.com/apis/credentials
define('GOOGLE_CLIENT_ID', 'SEU_CLIENT_ID_AQUI.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'SEU_CLIENT_SECRET_AQUI');
define('GOOGLE_REDIRECT_URI', 'https://reparte.free.nf/php/google_callback.php');

// Função para gerar URL de autenticação do Google
function getGoogleAuthUrl() {
    $params = [
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope' => 'email profile',
        'access_type' => 'online'
    ];
    
    return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}

// Função para trocar código por token de acesso
function getGoogleAccessToken($code) {
    $url = 'https://oauth2.googleapis.com/token';
    
    $data = [
        'code' => $code,
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Função para obter informações do usuário
function getGoogleUserInfo($accessToken) {
    $url = 'https://www.googleapis.com/oauth2/v2/userinfo';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
?>

