<?php
// Script para listar pastas e ajudar a encontrar o PHPMailer
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Estrutura de Pastas em 'vendor'</h1>";

$vendorDir = __DIR__ . '/../vendor';

if (!is_dir($vendorDir)) {
    die("<p style='color:red'>Pasta 'vendor' não encontrada em: $vendorDir</p>");
}

echo "<p>Caminho base: <strong>$vendorDir</strong></p>";

function listarDiretorio($dir, $nivel = 0)
{
    if (!is_dir($dir))
        return;

    $itens = scandir($dir);
    echo "<ul>";
    foreach ($itens as $item) {
        if ($item == '.' || $item == '..')
            continue;

        $caminho = $dir . '/' . $item;
        $isDir = is_dir($caminho);

        echo "<li>";
        echo $isDir ? "📁 <strong>$item</strong>" : "📄 $item";

        if ($isDir) {
            // Se for diretório, listar conteúdo (até 3 níveis)
            if ($nivel < 3) {
                listarDiretorio($caminho, $nivel + 1);
            }
        }
        echo "</li>";
    }
    echo "</ul>";
}

listarDiretorio($vendorDir);
?>