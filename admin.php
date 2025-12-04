<?php
session_start();
require 'php/config.php';

// Verificação de segurança: Só o admin pode acessar
// ID do admin: rFRCxqU-Yze
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
            <img src="images/logo.png" alt="RepArte">
        </div>
        <div class="header-actions">
            <a href="telainicial.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar para Home</a>
        </div>
    </header>

    <main class="admin-container">
        <h1><i class="fas fa-user-shield"></i> Painel de Controle</h1><br><br>

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
                                                    class="fas fa-check"></i> Aceitar (Apagar Item)</button>
                                        </form>

                                        <!-- Botão para ignorar (apagar só a denúncia) -->
                                        <form action="php/admin_actions.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="dismiss_report">
                                            <input type="hidden" name="id_denuncia" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn-action btn-ignore" title="Ignorar Denúncia"><i
                                                    class="fas fa-times"></i> Ignorar</button>
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
</body>

</html>