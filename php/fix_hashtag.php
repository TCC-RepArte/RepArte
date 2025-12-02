<?php
require 'config.php';

echo "<h2>Iniciando correção de hashtags...</h2>";

// 1. Limpa a tabela de ligação para refazer (opcional, mas garante limpeza)
$con->query("TRUNCATE TABLE post_hashtags");
echo "Tabela post_hashtags limpa.<br>";

// 2. Pega todos os posts
$result = $con->query("SELECT id, texto FROM postagens");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $post_id = $row['id'];
        $texto = $row['texto'];

        // Encontra todas as hashtags no texto
        preg_match_all('/#(\w+)/u', $texto, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $tag) {
                $tag_com_hash = "#" . $tag;

                // 3. Verifica se a hashtag já existe na tabela 'hashtags'
                $stmt = $con->prepare("SELECT id FROM hashtags WHERE nome = ?");
                $stmt->bind_param("s", $tag_com_hash);
                $stmt->execute();
                $res_tag = $stmt->get_result();

                if ($res_tag->num_rows > 0) {
                    // Já existe, pega o ID
                    $tag_row = $res_tag->fetch_assoc();
                    $tag_id = $tag_row['id'];
                } else {
                    // Não existe, cria
                    $stmt_insert = $con->prepare("INSERT INTO hashtags (nome) VALUES (?)");
                    $stmt_insert->bind_param("s", $tag_com_hash);
                    $stmt_insert->execute();
                    $tag_id = $stmt_insert->insert_id;
                    echo "Criada nova hashtag: $tag_com_hash<br>";
                }

                // 4. Cria a ligação na tabela 'post_hashtags'
                $stmt_link = $con->prepare("INSERT INTO post_hashtags (post_id, hashtag_id) VALUES (?, ?)");
                $stmt_link->bind_param("si", $post_id, $tag_id);
                $stmt_link->execute();
            }
            echo "Post $post_id processado: " . count($matches[1]) . " tags encontradas.<br>";
        }
    }
} else {
    echo "Nenhum post encontrado.";
}

echo "<h3>Concluído! Agora tente acessar a página da hashtag.</h3>";
?>