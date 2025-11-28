<?php
// Página dedicada para comentários de uma postagem
session_start();
require_once 'php/config.php';

// Verifica se o ID da postagem foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: telainicial.php');
    exit;
}

$postId = $_GET['id'];

// Busca os dados da postagem
try {
    $sql = "SELECT 
                p.id,
                p.titulo,
                p.texto,
                p.data_post as data,
                p.id_obra,
                COALESCE(pf.nomexi, u.usuario) as usuario,
                CASE 
                    WHEN pf.caminho IS NOT NULL AND pf.caminho != '' THEN pf.caminho
                    ELSE CONCAT('https://ui-avatars.com/api/?name=', REPLACE(COALESCE(pf.nomexi, u.usuario, 'User'), ' ', '+'), '&background=ff6600&color=fff&size=50')
                END as foto,
                o.titulo as titulo_obra,
                o.tipo as tipo_obra
            FROM postagens p
            INNER JOIN login u ON p.id_usuario = u.id
            LEFT JOIN perfil pf ON p.id_usuario = pf.id
            INNER JOIN obras o ON p.id_obra = o.id
            WHERE p.id = ?";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    
    if (!$post) {
        header('Location: telainicial.php');
        exit;
    }
} catch (Exception $e) {
    header('Location: telainicial.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comentários - <?= htmlspecialchars($post['titulo']) ?> - RepArte</title>
    <link rel="stylesheet" href="css/telainicial.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="telainicial.php"><img src="images/logo.png" alt="Logo do site"></a>
        </div>

        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-text" placeholder="Procure uma obra...">
        </div>

        <div class="header-controls">
            <a href="postagem.php?id=<?= $post['id'] ?>" class="btn-back" title="Voltar para postagem">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="php/logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </header>

    <main>
        <div class="comentarios-page">
            <!-- Resumo da Postagem -->
            <div class="post-resumo">
                <div class="post-resumo-header">
                    <div class="post-resumo-user">
                        <img src="<?= htmlspecialchars($post['foto']) ?>" alt="Foto do Usuário" class="post-resumo-photo" />
                        <div class="post-resumo-user-info">
                            <h3><?= htmlspecialchars($post['usuario']) ?></h3>
                            <span class="post-resumo-data"><?= date('d/m/Y H:i', strtotime($post['data'])) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="post-resumo-content">
                    <div class="post-resumo-obra">
                        <img id="post-resumo-obra-imagem" src="" alt="Imagem da obra" class="post-resumo-obra-image" />
                        <div class="post-resumo-obra-info">
                            <h4><?= htmlspecialchars($post['titulo_obra']) ?></h4>
                            <span class="post-resumo-obra-tipo"><?= ucfirst($post['tipo_obra']) ?></span>
                        </div>
                    </div>
                    <h2 class="post-resumo-titulo"><?= htmlspecialchars($post['titulo']) ?></h2>
                    <p class="post-resumo-texto"><?= nl2br(htmlspecialchars($post['texto'])) ?></p>
                </div>
            </div>
            
            <!-- Seção de Comentários -->
            <div class="comentarios-section">
                <div class="comentarios-header">
                    <h3><i class="fas fa-comments"></i> Comentários</h3>
                    <span class="comentarios-count" id="comentariosCount">0 comentários</span>
                </div>
                
                <form class="comentario-form" id="comentarioForm">
                    <textarea 
                        class="comentario-input" 
                        placeholder="Escreva seu comentário..." 
                        id="comentarioText"
                        required
                    ></textarea>
                    <button type="submit" class="comentario-submit">
                        <i class="fas fa-paper-plane"></i> Enviar Comentário
                    </button>
                </form>
                
                <div class="comentarios-list" id="comentariosList">
                    <div class="loading-comments">
                        <div class="loading-spinner"></div>
                        <span>Carregando comentários...</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="js/apis-obras.js"></script>
    <script>
        // Sistema de Loading para Imagens (apenas após 10 segundos)
        function criarLoadingPlaceholder() {
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'image-loading';
            loadingDiv.innerHTML = `
                <div class="loading-spinner"></div>
            `;
            return loadingDiv;
        }

        // Carrega a imagem da obra com loading (apenas após 10 segundos)
        async function carregarImagemObra() {
            const imgElement = document.getElementById('post-resumo-obra-imagem');
            const obraId = '<?= $post['id_obra'] ?>';
            const obraTipo = '<?= $post['tipo_obra'] ?>';
            
            let loadingDiv = null;
            let loadingTimeout = null;
            let imageLoaded = false;
            
            // Configura timeout para mostrar loading após 10 segundos
            loadingTimeout = setTimeout(() => {
                if (!imageLoaded) {
                    loadingDiv = criarLoadingPlaceholder();
                    imgElement.parentNode.style.position = 'relative';
                    imgElement.parentNode.appendChild(loadingDiv);
                }
            }, 10000); // 10 segundos
            
            try {
                const obra = await obterDetalhesObra({ 
                    apiId: obraId, 
                    tipo: obraTipo 
                });
                
                // Quando a imagem carregar, remove o loading e cancela timeout
                imgElement.onload = () => {
                    imageLoaded = true;
                    if (loadingTimeout) {
                        clearTimeout(loadingTimeout);
                    }
                    if (loadingDiv) {
                        loadingDiv.remove();
                    }
                };
                
                imgElement.onerror = () => {
                    imageLoaded = true;
                    if (loadingTimeout) {
                        clearTimeout(loadingTimeout);
                    }
                    if (loadingDiv) {
                        loadingDiv.remove();
                    }
                };
                
                imgElement.src = obra.imagem;
                imgElement.alt = obra.titulo;
                
            } catch (error) {
                console.error('Erro ao carregar imagem da obra:', error);
                imageLoaded = true;
                if (loadingTimeout) {
                    clearTimeout(loadingTimeout);
                }
                if (loadingDiv) {
                    loadingDiv.remove();
                }
            }
        }

        // Carrega comentários
        async function carregarComentarios() {
            const comentariosList = document.getElementById('comentariosList');
            const comentariosCount = document.getElementById('comentariosCount');
            const postId = '<?= $post['id'] ?>';
            
            try {
                const url = `php/buscar_comentarios.php?id_postagem=${postId}`;
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error('Erro ao carregar comentários');
                }
                
                const data = await response.json();
                
                if (data.success) {
                    // Atualiza contador
                    const count = data.comentarios ? data.comentarios.length : 0;
                    comentariosCount.textContent = `${count} comentário${count !== 1 ? 's' : ''}`;
                    
                    if (data.comentarios && data.comentarios.length > 0) {
                        comentariosList.innerHTML = renderizarComentariosHierarquicos(data.comentarios);
                    } else {
                        comentariosList.innerHTML = '<div class="no-comments">Nenhum comentário ainda. Seja o primeiro a comentar!</div>';
                    }
                } else {
                    comentariosList.innerHTML = '<div class="no-comments">Erro ao carregar comentários.</div>';
                }
            } catch (error) {
                console.error('Erro ao carregar comentários:', error);
                comentariosList.innerHTML = '<div class="no-comments">Erro ao carregar comentários.</div>';
            }
        }

        // Formata data
        function formatarData(dataString) {
            const data = new Date(dataString);
            const agora = new Date();
            const diffMs = agora - data;
            const diffMinutos = Math.floor(diffMs / 60000);
            const diffHoras = Math.floor(diffMs / 3600000);
            const diffDias = Math.floor(diffMs / 86400000);
            
            if (diffMinutos < 1) return 'Agora mesmo';
            if (diffMinutos < 60) return `${diffMinutos}min atrás`;
            if (diffHoras < 24) return `${diffHoras}h atrás`;
            if (diffDias < 7) return `${diffDias}d atrás`;
            
            return data.toLocaleDateString('pt-BR');
        }

        // Envia comentário
        async function enviarComentario(postId, texto, comentarioPaiId = null) {
            try {
                const body = {
                    id_postagem: postId,
                    texto: texto
                };
                
                if (comentarioPaiId) {
                    body.comentario_pai_id = comentarioPaiId;
                }
                
                const response = await fetch('php/criar_comentario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(body)
                });
                
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`Erro do servidor: ${response.status}`);
                }
                
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Erro ao enviar comentário:', error);
                return { success: false, message: 'Erro de conexão: ' + error.message };
            }
        }

        // Função para renderizar comentários hierárquicos
        function renderizarComentariosHierarquicos(comentarios) {
            const comentariosMap = new Map();
            const comentariosRaiz = [];
            
            // Organiza comentários em um mapa
            comentarios.forEach(comment => {
                comentariosMap.set(comment.id, { ...comment, subcomentarios: [] });
            });
            
            // Organiza hierarquia
            comentarios.forEach(comment => {
                if (comment.comentario_pai_id) {
                    const pai = comentariosMap.get(comment.comentario_pai_id);
                    if (pai) {
                        pai.subcomentarios.push(comentariosMap.get(comment.id));
                    }
                } else {
                    comentariosRaiz.push(comentariosMap.get(comment.id));
                }
            });
            
            // Renderiza recursivamente
            function renderizarComentario(comment, nivel = 0) {
                const marginLeft = nivel * 30;
                const isSubcomentario = nivel > 0;
                
                let html = `
                    <div class="comentario-item ${isSubcomentario ? 'subcomentario' : ''}" style="margin-left: ${marginLeft}px;">
                        <div class="comentario-header">
                            <div class="comentario-user-info">
                                <img src="${comment.foto_usuario || 'img/default-avatar.png'}" alt="Foto do usuário" class="comentario-avatar" />
                                <div class="comentario-user-details">
                                    <span class="comentario-author">${comment.usuario}</span>
                                    <span class="comentario-date">${formatarData(comment.data)}</span>
                                </div>
                            </div>
                        </div>
                        <p class="comentario-text">${comment.texto}</p>
                        <div class="comentario-actions">
                            <button class="comentario-action-btn" onclick="responderComentario(${comment.id})">
                                <i class="fas fa-reply"></i> Responder
                            </button>
                            <button class="comentario-action-btn" onclick="curtirComentario(${comment.id})">
                                <i class="fas fa-heart"></i> Curtir
                            </button>
                        </div>
                `;
                
                // Renderiza subcomentários
                if (comment.subcomentarios && comment.subcomentarios.length > 0) {
                    html += '<div class="subcomentarios">';
                    comment.subcomentarios.forEach(subcomment => {
                        html += renderizarComentario(subcomment, nivel + 1);
                    });
                    html += '</div>';
                }
                
                html += '</div>';
                return html;
            }
            
            return comentariosRaiz.map(comment => renderizarComentario(comment)).join('');
        }

        // Funções para ações dos comentários
        function responderComentario(comentarioId) {
            console.log('Responder comentário:', comentarioId);
            // Mostra formulário de resposta
            mostrarFormularioResposta(comentarioId);
        }

        function curtirComentario(comentarioId) {
            console.log('Curtir comentário:', comentarioId);
            // TODO: Implementar sistema de curtidas em comentários
        }

        // Função para mostrar formulário de resposta
        function mostrarFormularioResposta(comentarioPaiId) {
            // Remove formulários de resposta existentes
            const formulariosExistentes = document.querySelectorAll('.formulario-resposta');
            formulariosExistentes.forEach(form => form.remove());
            
            // Cria novo formulário de resposta
            const comentarioItem = document.querySelector(`[onclick*="responderComentario(${comentarioPaiId})"]`).closest('.comentario-item');
            const formularioResposta = document.createElement('div');
            formularioResposta.className = 'formulario-resposta';
            formularioResposta.innerHTML = `
                <form class="comentario-form resposta-form">
                    <textarea 
                        class="comentario-input" 
                        placeholder="Escreva sua resposta..." 
                        required
                    ></textarea>
                    <div class="resposta-actions">
                        <button type="submit" class="comentario-submit">
                            <i class="fas fa-paper-plane"></i> Responder
                        </button>
                        <button type="button" class="cancelar-resposta" onclick="cancelarResposta()">
                            Cancelar
                        </button>
                    </div>
                </form>
            `;
            
            comentarioItem.appendChild(formularioResposta);
            
            // Configura o formulário
            const form = formularioResposta.querySelector('form');
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const texto = form.querySelector('textarea').value.trim();
                if (!texto) return;
                
                const submitButton = form.querySelector('.comentario-submit');
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
                
                try {
                    const result = await enviarComentario('<?= $post['id'] ?>', texto, comentarioPaiId);
                    
                    if (result.success) {
                        // Recarrega comentários
                        await carregarComentarios();
                    } else {
                        alert('Erro: ' + result.message);
                    }
                } catch (error) {
                    console.error('Erro ao enviar resposta:', error);
                    alert('Erro ao enviar resposta');
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Responder';
                }
            });
        }

        function cancelarResposta() {
            const formulariosExistentes = document.querySelectorAll('.formulario-resposta');
            formulariosExistentes.forEach(form => form.remove());
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            carregarImagemObra();
            carregarComentarios();
            
            // Configurar formulário de comentários
            const comentarioForm = document.getElementById('comentarioForm');
            comentarioForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const comentarioText = document.getElementById('comentarioText');
                const submitButton = comentarioForm.querySelector('.comentario-submit');
                
                if (!comentarioText.value.trim()) {
                    return;
                }
                
                // Desabilita o botão durante o envio
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
                
                try {
                    const result = await enviarComentario('<?= $post['id'] ?>', comentarioText.value.trim());
                    
                    if (result.success) {
                        // Limpa o campo de texto
                        comentarioText.value = '';
                        
                        // Recarrega os comentários
                        await carregarComentarios();
                        
                        // Mostra mensagem de sucesso
                        const successMsg = document.createElement('div');
                        successMsg.style.cssText = 'background: #00ff00; color: #000; padding: 10px; border-radius: 8px; margin-bottom: 10px; font-weight: 600;';
                        successMsg.textContent = 'Comentário enviado com sucesso!';
                        comentarioForm.parentNode.insertBefore(successMsg, comentarioForm);
                        
                        // Remove a mensagem após 3 segundos
                        setTimeout(() => {
                            successMsg.remove();
                        }, 3000);
                    } else {
                        console.error('Erro ao enviar comentário:', result.message);
                        
                        // Mostra mensagem de erro
                        const errorMsg = document.createElement('div');
                        errorMsg.style.cssText = 'background: #ff3333; color: #fff; padding: 10px; border-radius: 8px; margin-bottom: 10px; font-weight: 600;';
                        errorMsg.textContent = 'Erro: ' + result.message;
                        comentarioForm.parentNode.insertBefore(errorMsg, comentarioForm);
                        
                        // Remove a mensagem após 5 segundos
                        setTimeout(() => {
                            errorMsg.remove();
                        }, 5000);
                    }
                } catch (error) {
                    console.error('Erro ao enviar comentário:', error);
                    
                    // Mostra mensagem de erro
                    const errorMsg = document.createElement('div');
                    errorMsg.style.cssText = 'background: #ff3333; color: #fff; padding: 10px; border-radius: 8px; margin-bottom: 10px; font-weight: 600;';
                    errorMsg.textContent = 'Erro de conexão. Tente novamente.';
                    comentarioForm.parentNode.insertBefore(errorMsg, comentarioForm);
                    
                    // Remove a mensagem após 5 segundos
                    setTimeout(() => {
                        errorMsg.remove();
                    }, 5000);
                } finally {
                    // Reabilita o botão
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar Comentário';
                }
            });
        });
    </script>
</body>
</html>
