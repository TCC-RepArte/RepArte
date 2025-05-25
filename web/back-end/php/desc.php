<?php

define('ROOT', __DIR__);

require_once ROOT . '\..\..\..\vendor\autoload.php';

$client = new \GuzzleHttp\Client();

$response = $client->request('GET', 'https://api.themoviedb.org/3/search/collection?include_adult=true&language=pt-BR&page=1', [
  'headers' => [
    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJkNGY1NWJmYmRkYWU5MTRlMTI4NDE1YjczOTVhNzQ3NSIsIm5iZiI6MTc0ODAwMjMzOC4yNDgsInN1YiI6IjY4MzA2NjIyM2E3ZjBiNTc4MTgzNmY3NyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.QTXRWLYChv0Kly7kwVjvAKWxiuYGOW5hA6m9JHfIKHI',
    'accept' => 'application/json',
  ],
]);


