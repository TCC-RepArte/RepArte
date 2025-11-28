<?php
require_once __DIR__ . '/config.php';

global $con;

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// SQL para criar tabela hashtags
$sql_hashtags = "CREATE TABLE IF NOT EXISTS hashtags (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    contagem INT(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

// SQL para criar tabela post_hashtags
$sql_post_hashtags = "CREATE TABLE IF NOT EXISTS post_hashtags (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    post_id VARCHAR(20) NOT NULL,
    hashtag_id INT(11) NOT NULL,
    FOREIGN KEY (post_id) REFERENCES postagens(id) ON DELETE CASCADE,
    FOREIGN KEY (hashtag_id) REFERENCES hashtags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

// SQL para criar tabela mensagens (Chat)
$sql_mensagens = "CREATE TABLE IF NOT EXISTS mensagens (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_remetente VARCHAR(36) NOT NULL,
    id_destinatario VARCHAR(36) NOT NULL,
    mensagem TEXT NOT NULL,
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    lida TINYINT(1) DEFAULT 0,
    FOREIGN KEY (id_remetente) REFERENCES login(id) ON DELETE CASCADE,
    FOREIGN KEY (id_destinatario) REFERENCES login(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

// Executar queries
if ($con->query($sql_hashtags) === TRUE) {
    echo "Tabela hashtags criada/verificada com sucesso.<br>";
} else {
    echo "Erro ao criar tabela hashtags: " . $con->error . "<br>";
}

if ($con->query($sql_post_hashtags) === TRUE) {
    echo "Tabela post_hashtags criada/verificada com sucesso.<br>";
} else {
    echo "Erro ao criar tabela post_hashtags: " . $con->error . "<br>";
}

if ($con->query($sql_mensagens) === TRUE) {
    echo "Tabela mensagens criada/verificada com sucesso.<br>";
} else {
    echo "Erro ao criar tabela mensagens: " . $con->error . "<br>";
}

$con->close();
?>