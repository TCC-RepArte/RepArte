<?php
session_start();
require 'php/config.php';

// Verificação de segurança: Só o admin pode acessar
if (!isset($_SESSION['id']) || $_SESSION['id'] !== 'rFRCxqU-Yze') {
    // Se não for admin, manda de volta pra home
    header("Location: telainicial.php");
    exit;
}

// Buscar denúncias pendentes
$sql_denuncias = "SELECT d.*, l.usuario as denunciante_nome 
                  FROM denuncias d 
                  LEFT JOIN login l ON d.id_denunciante = l.id 
                  ORDER BY d.data_denuncia DESC";
$result = $con->query($sql_denuncias);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - RepArte</title>
    <link rel="stylesheet" href="css/telainicial.css"> <!-- Estilos base -->
    <link rel="stylesheet" href="css/admin.css"> <!-- Estilos específicos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <header>
        <div class="logo">
            <a href="telainicial.php"><img src="images/logo.png" alt="RepArte"></a>
        </div>

        <div class="search-box">
            <form action="busca.php" method="GET" style="display: flex; width: 100%; align-items: center;">
                <button type="submit" class="search-icon"
                    style="background: none; border: none; cursor: pointer;">🔍</button>
                <input type="text" name="q" class="search-text" placeholder="Procure uma obra, usuário ou hashtag...">
            </form>
        </div>

        <div class="header-actions">
            <div class="notif-container">
                <i class="fas fa-bell" id="notif-icon"></i>
                <span class="notif-badge" id="notif-badge" style="display: none;">0</span>

                <!-- Dropdown de notificações -->
                <div class="notif-dropdown" id="notif-dropdown">
                    <div class="notif-header">
                        <h4>Notificações</h4>
                        <button class="marcar-todas-lidas" onclick="marcarTodasLidas()">Marcar todas como
                            lidas</button>
                    </div>
                    <div class="notif-list" id="notif-list">
                        <div class="notif-empty">Carregando...</div>
                    </div>
                    <a href="notificacoes.php" class="notif-footer">Ver preferências</a>
                </div>
            </div>
            <a href="configuracoes.php" style="color: inherit; text-decoration: none;"><i class="fas fa-cog"></i></a>
            <a href="admin.php" style="color: inherit; text-decoration: none;" title="Painel Admin">
                <i class="fas fa-shield-alt"></i>
            </a>
        </div>
    </header>

    <main class="admin-container">
        <h1><i class="fas fa-user-shield"></i> Painel de Controle</h1><br><br>

        <!-- Seção de Busca de IDs -->
        <section class="admin-card">
            <h2><i class="fas fa-search"></i> Buscar IDs</h2>
            <p>Encontre o ID de postagens, usuários ou comentários para realizar ações.</p><br>

            <div class="search-form" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px;">
                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label>Tipo:</label>
                    <select id="search-type"
                        style="width: 100%; padding: 10px; border-radius: 5px; background: #222; color: white; border: 1px solid #444;">
                        <option value="postagem">Postagem</option>
                        <option value="usuario">Usuário</option>
                        <option value="comentario">Comentário</option>
                    </select>
                </div>

                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label>Critério:</label>
                    <select id="search-criteria"
                        style="width: 100%; padding: 10px; border-radius: 5px; background: #222; color: white; border: 1px solid #444;">
                        <!-- Preenchido via JS -->
                    </select>
                </div>

                <div class="form-group" style="flex: 2; min-width: 200px;">
                    <label>Busca:</label>
                    <div class="search-input-container" style="display: flex; gap: 10px;">
                        <input type="text" id="search-input" placeholder="Digite para buscar..."
                            style="width: 100%; padding: 10px; border-radius: 5px; background: #222; color: white; border: 1px solid #444;">
                        <button id="btn-search-id"
                            style="background: #ff6600; color: white; border: none; padding: 0 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div id="search-results" style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
                <!-- Resultados aparecerão aqui -->
                <p style="color: #666; text-align: center; padding: 20px;">Os resultados da busca aparecerão
                    aqui.</p>
            </div>
        </section>

        <!-- Seção de Exclusão Manual -->
        <section class="admin-card">
            <h2><i class="fas fa-trash-alt"></i> Exclusão Manual</h2>
            <p>Use esta ferramenta para excluir qualquer item pelo ID, mesmo sem denúncia.</p><br><br>
            <form action="php/admin_actions.php" method="POST" class="admin-form"
                onsubmit="return confirm('Tem certeza absoluta? Isso não pode ser desfeito.');">
                <input type="hidden" name="action" value="delete_manual">

                <div class="form-group">
                    <label>ID do Item:</label>
                    <input type="text" name="id_item" required placeholder="Ex: 68f24bcd3dbee">
                </div>

                <div class="form-group">
                    <label>Tipo:</label>
                    <select name="tipo_item" required>
                        <option value="postagem">Postagem</option>
                        <option value="comentario">Comentário</option>
                        <option value="usuario">Usuário (Banir)</option>
                    </select>
                </div>

                <button type="submit" class="btn-delete">Excluir Item</button>
            </form>
        </section>

        <!-- Seção de Denúncias -->
        <section class="admin-card full-width">
            <h2><i class="fas fa-exclamation-triangle"></i> Denúncias Pendentes</h2>

            <?php if ($result && $result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Denunciante</th>
                                <th>Tipo</th>
                                <th>Motivo</th>
                                <th>Item</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($row['data_denuncia'])) ?></td>
                                    <td><?= htmlspecialchars($row['denunciante_nome'] ?? 'Desconhecido') ?></td>
                                    <td><span
                                            class="badge badge-<?= $row['tipo_denuncia'] ?>"><?= ucfirst($row['tipo_denuncia']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($row['motivo']) ?></td>
                                    <td>
                                        <span class="item-id"><?= $row['id_item_denunciado'] ?></span>
                                        <?php
                                        // Gera link para ver o item, se possível
                                        $link = "#";
                                        if ($row['tipo_denuncia'] == 'postagem')
                                            $link = "postagem.php?id=" . $row['id_item_denunciado'];
                                        if ($row['tipo_denuncia'] == 'usuario')
                                            $link = "usuario_perfil.php?id=" . $row['id_item_denunciado'];
                                        ?>
                                        <?php if ($link != "#"): ?>
                                            <a href="<?= $link ?>" target="_blank" class="btn-link"><i
                                                    class="fas fa-external-link-alt"></i> Ver</a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions-cell">
                                        <!-- Botão para excluir o ITEM denunciado (Aceitar denúncia) -->
                                        <form action="php/admin_actions.php" method="POST" style="display:inline;"
                                            onsubmit="return confirm('Isso vai apagar o ITEM denunciado permanentemente. Confirmar?');">
                                            <input type="hidden" name="action" value="delete_manual">
                                            <input type="hidden" name="id_item" value="<?= $row['id_item_denunciado'] ?>">
                                            <input type="hidden" name="tipo_item" value="<?= $row['tipo_denuncia'] ?>">
                                            <input type="hidden" name="id_denuncia" value="<?= $row['id'] ?>">
                                            <!-- Para apagar a denúncia junto -->
                                            <button type="submit" class="btn-action btn-ban" title="Apagar Item e Denúncia"><i
                                                    class="fas fa-check"></i> Aceitar
                                                (Apagar Item)</button>
                                        </form>

                                        <!-- Botão para ignorar (apagar só a denúncia) -->
                                        <form action="php/admin_actions.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="dismiss_report">
                                            <input type="hidden" name="id_denuncia" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn-action btn-ignore" title="Ignorar Denúncia"><i
                                                    class="fas fa-times"></i>
                                                Ignorar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="empty-state">Nenhuma denúncia pendente. Tudo limpo!</p>
            <?php endif; ?>
        </section>
    </main>
    <script src="js/notificacoes.js"></script>
    <script src="js/admin_search.js"></script>

</body>

</html>