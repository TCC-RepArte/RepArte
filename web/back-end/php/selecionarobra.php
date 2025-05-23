<?php

$client = file_get_contents('signup2.php', true);
$client

$response = $client->request('GET', 'https://api.themoviedb.org/3/authentication/token/new', [
  'headers' => [
    'accept' => 'application/json',
  ],
]);

echo $response->getBody();

?>