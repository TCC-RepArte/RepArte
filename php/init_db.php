<?php
$con = new mysqli("localhost", "root", "");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$db_name = "if0_40154094_reparte";
$sql = "CREATE DATABASE IF NOT EXISTS $db_name";
if ($con->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: " . $con->error . "\n";
}

$con->select_db($db_name);

// Read SQL file
$sql_file = __DIR__ . '/../bd/if0_40154094_reparte (2).sql';
if (file_exists($sql_file)) {
    $sql_content = file_get_contents($sql_file);
    // Basic split for import (multi_query can be tricky with large files but this one is small enough)
    if ($con->multi_query($sql_content)) {
        do {
            /* store first result set */
            if ($result = $con->store_result()) {
                $result->free();
            }
        } while ($con->next_result());
        echo "SQL dump imported successfully.\n";
    } else {
        echo "Error importing SQL dump: " . $con->error . "\n";
    }
} else {
    echo "SQL file not found: $sql_file\n";
}

// Now create my new tables
$sql_hashtags = "CREATE TABLE IF NOT EXISTS hashtags (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    contagem INT(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

$sql_post_hashtags = "CREATE TABLE IF NOT EXISTS post_hashtags (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    post_id VARCHAR(20) NOT NULL,
    hashtag_id INT(11) NOT NULL,
    FOREIGN KEY (post_id) REFERENCES postagens(id) ON DELETE CASCADE,
    FOREIGN KEY (hashtag_id) REFERENCES hashtags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

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

if ($con->query($sql_hashtags))
    echo "Hashtags table checked.\n";
else
    echo "Hashtags error: " . $con->error . "\n";

if ($con->query($sql_post_hashtags))
    echo "Post_hashtags table checked.\n";
else
    echo "Post_hashtags error: " . $con->error . "\n";

if ($con->query($sql_mensagens))
    echo "Mensagens table checked.\n";
else
    echo "Mensagens error: " . $con->error . "\n";

echo "Tables updated.\n";
$con->close();
?>