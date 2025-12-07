<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

// Verificação de segurança: Só o admin pode acessar
if (!isset($_SESSION['id']) || $_SESSION['id'] !== 'rFRCxqU-Yze') {
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

$type = $_GET['type'] ?? '';
$criteria = $_GET['criteria'] ?? '';
$q = $_GET['q'] ?? '';

if (empty($q)) {
    echo json_encode([]);
    exit;
}

$results = [];
$term = "%$q%";

try {
    if ($type === 'postagem') {
        if ($criteria === 'texto') {
            $sql = "SELECT p.id, p.titulo, p.texto, l.usuario 
                    FROM postagens p 
                    JOIN login l ON p.id_usuario = l.id 
                    WHERE p.titulo LIKE ? OR p.texto LIKE ? 
                    ORDER BY p.data_post DESC LIMIT 20";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ss", $term, $term);
        } elseif ($criteria === 'id') {
            $sql = "SELECT p.id, p.titulo, p.texto, l.usuario 
                    FROM postagens p 
                    JOIN login l ON p.id_usuario = l.id 
                    WHERE p.id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $q);
        } elseif ($criteria === 'usuario') {
            $sql = "SELECT p.id, p.titulo, p.texto, l.usuario 
                    FROM postagens p 
                    JOIN login l ON p.id_usuario = l.id 
                    WHERE l.usuario LIKE ? 
                    ORDER BY p.data_post DESC LIMIT 20";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $term);
        } elseif ($criteria === 'hashtag') {
            $sql = "SELECT p.id, p.titulo, p.texto, l.usuario 
                    FROM postagens p 
                    JOIN login l ON p.id_usuario = l.id 
                    JOIN post_hashtags ph ON p.id = ph.post_id 
                    JOIN hashtags h ON ph.hashtag_id = h.id 
                    WHERE h.nome LIKE ? 
                    ORDER BY p.data_post DESC LIMIT 20";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $term);
        }
    } elseif ($type === 'usuario') {
        if ($criteria === 'nome') {
            $sql = "SELECT l.id, l.usuario, p.nomexi, l.email 
                    FROM login l 
                    LEFT JOIN perfil p ON l.id = p.id 
                    WHERE l.usuario LIKE ? OR p.nomexi LIKE ? 
                    LIMIT 20";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ss", $term, $term);
        } elseif ($criteria === 'id') {
            $sql = "SELECT l.id, l.usuario, p.nomexi, l.email 
                    FROM login l 
                    LEFT JOIN perfil p ON l.id = p.id 
                    WHERE l.id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $q);
        } elseif ($criteria === 'email') {
            $sql = "SELECT l.id, l.usuario, p.nomexi, l.email 
                     FROM login l 
                     LEFT JOIN perfil p ON l.id = p.id 
                     WHERE l.email LIKE ? 
                     LIMIT 20";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $term);
        }
    } elseif ($type === 'comentario') {
        if ($criteria === 'texto') {
            $sql = "SELECT c.id, c.texto, l.usuario 
                    FROM comentarios c 
                    JOIN login l ON c.id_usuario = l.id 
                    WHERE c.texto LIKE ? 
                    ORDER BY c.data DESC LIMIT 20";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $term);
        } elseif ($criteria === 'id') {
            $sql = "SELECT c.id, c.texto, l.usuario 
                    FROM comentarios c 
                    JOIN login l ON c.id_usuario = l.id 
                    WHERE c.id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $q);
        } elseif ($criteria === 'usuario') {
            $sql = "SELECT c.id, c.texto, l.usuario 
                    FROM comentarios c 
                    JOIN login l ON c.id_usuario = l.id 
                    WHERE l.usuario LIKE ? 
                    ORDER BY c.data DESC LIMIT 20";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $term);
        }
    }

    if (isset($stmt)) {
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $results[] = $row;
        }
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

echo json_encode($results);
