<?php

header('content-type: application/json');

$dados = file_get_contents('php://input');


echo $dados;

?>