<?php
// Página para visualizar uma postagem específica
session_start();

require_once 'php/config.php';
include 'vlibras_include.php';

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
                p.id_usuario,
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
    <title><?= htmlspecialchars($post['titulo']) ?> - RepArte</title>
    <link rel="stylesheet" href="css/telainicial.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <header>
        <div class="logo">
            <a href="telainicial.php"><img src="images/logo.png" alt="Logo do site"></a>
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
                        <button class="marcar-todas-lidas" onclick="marcarTodasLidas()">Marcar todas como lidas</button>
                    </div>
                    <div class="notif-list" id="notif-list">
                        <div class="notif-empty">Carregando...</div>
                    </div>
                    <a href="notificacoes.php" class="notif-footer">Ver preferências</a>
                </div>
            </div>
            <a href="configuracoes.php" style="color: inherit; text-decoration: none;"><i class="fas fa-cog"></i></a>
            <?php if (isset($_SESSION['id']) && $_SESSION['id'] === 'rFRCxqU-Yze'): ?>
                <a href="admin.php" style="color: inherit; text-decoration: none;" title="Painel Admin">
                    <i class="fas fa-shield-alt"></i>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <div class="postagem-completa">
            <div class="post">
                <div class="post-header">
                    <div class="post-user">
                        <a href="usuario_perfil.php?id=<?= $post['id_usuario'] ?>"
                            style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px;">
                            <img src="<?= htmlspecialchars($post['foto']) ?>" alt="Foto do Usuário"
                                class="post-user-photo" />
                            <h3><?= htmlspecialchars($post['usuario']) ?></h3>
                        </a>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span class="post-data"><?= date('d/m/Y H:i', strtotime($post['data'])) ?></span>

                        <!-- Container para o menu de opções do post -->
                        <div class="post-set-container">
                            <i class="post-set fa-solid fa-bars" style="color: rgb(255, 102, 0);" title="Opções"></i>

                            <!-- Menu Dropdown -->
                            <div class="post-options-dropdown">
                                <!-- Copiar Link -->
                                <a href="#" onclick="copiarLink(event, '<?= $post['id'] ?>')">
                                    <i class="fas fa-link"></i> Copiar Link
                                </a>

                                <a href="#" class="btn-favorito" data-id="<?= $post['id'] ?>"
                                    onclick="toggleFavorito(event, '<?= $post['id'] ?>')">
                                    <i class="far fa-star" id="fav-icon-<?= $post['id'] ?>"></i>
                                    <span id="fav-text-<?= $post['id'] ?>">Favoritar</span>
                                </a>

                                <!-- Deletar: Só aparece se o usuário for o dono do post -->
                                <?php if (isset($_SESSION['id']) && $_SESSION['id'] == $post['id_usuario']): ?>
                                    <a href="php/deletar_post.php?id=<?= $post['id'] ?>"
                                        onclick="return confirm('Tem certeza que deseja deletar este post?')">
                                        <i class="fas fa-trash" style="color: red;"></i> Deletar
                                    </a>
                                <?php endif; ?>

                                <!-- Denunciar -->
                                <a href="#" onclick="abrirDenuncia(event, '<?= $post['id'] ?>', 'postagem')">
                                    <i class="fas fa-flag"></i> Denunciar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="post-body">
                    <a href="obra.php?id=<?= $post['id_obra'] ?>" class="obra-link">
                        <img id="postagem-obra-imagem" src="" alt="Imagem da obra" class="post-obra-image" />
                    </a>
                    <div class="post-content">
                        <div class="post-obra-info">
                            <a href="obra.php?id=<?= $post['id_obra'] ?>" class="obra-title-link">
                                <h4><?= htmlspecialchars($post['titulo_obra']) ?></h4>
                            </a>
                            <span class="post-obra-tipo"><?= ucfirst($post['tipo_obra']) ?></span>
                        </div>
                        <p class="post-titulo"><?= htmlspecialchars($post['titulo']) ?></p>
                        <div class="post-text-container" data-post-id="<?= $post['id'] ?>">
                            <div class="post-text-truncated"><?= nl2br(htmlspecialchars($post['texto'])) ?></div>
                            <button class="expand-button" onclick="expandirTexto('<?= $post['id'] ?>')"
                                style="display: none;">
                                Ver mais...
                            </button>
                        </div>
                    </div>
                </div>

                <div class="post-buttons">
                    <a href="comentarios.php?id=<?= $post['id'] ?>" class="comment-button"
                        title="Ver todos os comentários">
                        <i class="fas fa-comment-dots"></i>
                    </a>
                    <div class="vote-buttons" data-id="<?= $post['id'] ?>">
                        <button class="like-btn">
                            <i class="fas fa-arrow-up"></i>
                            <span class="like-text">Curtir</span>
                        </button>
                        <span class="like-count">0</span>
                        <button class="dislike-btn">
                            <i class="fas fa-arrow-down"></i>
                            <span class="dislike-text">Descurtir</span>
                        </button>
                        <span class="dislike-count">0</span>
                    </div>
                </div>
            </div>

            <div class="comentarios-section">
                <div class="comentarios-header">
                    <h3>Comentários</h3>
                </div>

                <form class="comentario-form" id="comentarioForm">
                    <textarea class="comentario-input" placeholder="Escreva seu comentário..." id="comentarioText"
                        required></textarea>
                    <button type="submit" class="comentario-submit">
                        <i class="fas fa-paper-plane"></i> Enviar
                    </button>
                </form>

                <div class="comentarios-list" id="comentariosList">
                </div>
            </div>
        </div>
    </main>

    <script src="js/apis-obras.js"></script>
    <script>
        // Sistema de Loading para Imagens (apenas após 10 segundos)
        function criarLoadingPlaceholder(tipo = 'post-image') {
            const loadingDiv = document.createElement('div');
            loadingDiv.className = `image-loading ${tipo}`;
            loadingDiv.innerHTML = `
                <div class="loading-spinner"></div>
            `;
            return loadingDiv;
        }

        function determinarTipoImagem(tipoObra) {
            switch (tipoObra) {
                case 'musica':
                    return 'square-size'; // 1:1 para músicas
                case 'filme':
                case 'serie':
                    return 'poster-size'; // 2:3 para filmes/séries
                case 'livro':
                    return 'cover-size'; // 3:4 para livros
                case 'arte':
                    return 'square-size'; // 1:1 para arte
                default:
                    return 'post-image'; // Padrão
            }
        }

        // Carrega a imagem da obra com loading (apenas após 10 segundos)
        async function carregarImagemObra() {
            const imgElement = document.getElementById('postagem-obra-imagem');
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

        // Sistema de curtidas e descurtidas
        const voteButtons = document.querySelector('.vote-buttons');
        const postId = '<?= $post['id'] ?>';
        let estadoPost = null;

        // Carrega o estado atual das reações do usuário
        async function carregarEstadoReacoes() {
            try {
                // Carregar estado da reação do usuário
                const reacaoResponse = await fetch('php/buscar_reacao.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: postId })
                });

                if (reacaoResponse.ok) {
                    const reacaoData = await reacaoResponse.json();
                    if (reacaoData.success && reacaoData.reacao) {
                        estadoPost = reacaoData.reacao.tipo;
                        atualizarBotoesPost(reacaoData.reacao.tipo);
                    }
                }

                // Carregar contadores de reações
                const contadorResponse = await fetch('php/contar_reacoes.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: postId })
                });

                if (contadorResponse.ok) {
                    const contadorData = await contadorResponse.json();
                    if (contadorData.success) {
                        atualizarContadores(contadorData.likes, contadorData.dislikes);
                    }
                }
            } catch (error) {
                console.log('Erro ao carregar dados da reação:', error);
            }
        }

        // Função para atualizar a aparência dos botões baseado no estado
        function atualizarBotoesPost(estado) {
            const likeBtn = document.querySelector('.like-btn');
            const dislikeBtn = document.querySelector('.dislike-btn');
            const likeText = document.querySelector('.like-text');
            const dislikeText = document.querySelector('.dislike-text');

            // Remove classes ativas dos dois botões
            likeBtn.classList.remove('ativo');
            dislikeBtn.classList.remove('ativo');

            // Aplicar estilos baseado no estado
            if (estado === 'like') {
                likeBtn.classList.add('ativo');
                likeText.textContent = 'Curtido';
                dislikeText.textContent = 'Descurtir';
            } else if (estado === 'dislike') {
                dislikeBtn.classList.add('ativo');
                dislikeText.textContent = 'Descurtido';
                likeText.textContent = 'Curtir';
            } else {
                // Estado null - ambos desativados
                likeText.textContent = 'Curtir';
                dislikeText.textContent = 'Descurtir';
            }
        }

        // Função para atualizar os contadores de reação
        function atualizarContadores(likes, dislikes) {
            const likeCount = document.querySelector('.like-count');
            const dislikeCount = document.querySelector('.dislike-count');

            if (likeCount) likeCount.textContent = likes;
            if (dislikeCount) dislikeCount.textContent = dislikes;
        }

        // Função para enviar reação para o servidor
        async function enviarReacao(estado) {
            let like = false, dislike = false;

            if (estado === 'like') like = true;
            else if (estado === 'dislike') dislike = true;

            try {
                const response = await fetch('php/reagir.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: postId, like: like, dislike: dislike })
                });

                const data = await response.json();

                if (!response.ok) {
                    console.error('Erro na requisição:', response.status);
                    alert('Erro ao processar reação. Tente novamente.');
                } else {
                    if (data.success) {
                        // Atualizar contadores após sucesso
                        await atualizarContadoresPost();
                    } else {
                        console.error('Erro do servidor:', data.error);
                        alert('Erro: ' + data.error);
                    }
                }
            } catch (error) {
                console.error('Erro detalhado ao enviar reação:', error);
                alert('Erro: ' + error.message);
            }
        }

        // Função para atualizar contadores de um post específico
        async function atualizarContadoresPost() {
            try {
                const response = await fetch('php/contar_reacoes.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: postId })
                });

                const data = await response.json();
                if (data.success) {
                    atualizarContadores(data.likes, data.dislikes);
                }
            } catch (error) {
                console.error('Erro ao atualizar contadores:', error);
            }
        }

        // Event listeners para os botões de curtir/descurtir
        document.addEventListener('DOMContentLoaded', function () {
            const likeBtn = document.querySelector('.like-btn');
            const dislikeBtn = document.querySelector('.dislike-btn');

            // Event listener para curtir
            likeBtn.addEventListener('click', () => {
                if (estadoPost === 'like') {
                    // Se já está curtido, remove a curtida
                    estadoPost = null;
                } else {
                    // Se não está curtido ou está descurtido, curte
                    estadoPost = 'like';
                }

                atualizarBotoesPost(estadoPost);
                enviarReacao(estadoPost);
            });

            // Event listener para descurtir
            dislikeBtn.addEventListener('click', () => {
                if (estadoPost === 'dislike') {
                    // Se já está descurtido, remove a descurtida
                    estadoPost = null;
                } else {
                    // Se não está descurtido ou está curtido, descurte
                    estadoPost = 'dislike';
                }

                atualizarBotoesPost(estadoPost);
                enviarReacao(estadoPost);
            });
        });

        // Carrega comentários
        async function carregarComentarios() {
            const comentariosList = document.getElementById('comentariosList');
            const postId = '<?= $post['id'] ?>';

            console.log('=== CARREGANDO COMENTÁRIOS ===');
            console.log('Post ID:', postId);
            console.log('Elemento comentariosList:', comentariosList);

            if (!comentariosList) {
                console.error('Elemento comentariosList não encontrado!');
                return;
            }

            try {
                const url = `php/buscar_comentarios.php?id_postagem=${postId}`;
                console.log('URL da requisição:', url);

                const response = await fetch(url);
                console.log('Status da resposta:', response.status);
                console.log('Headers da resposta:', response.headers);

                if (!response.ok) {
                    console.error('Erro HTTP:', response.status);
                    const errorText = await response.text();
                    console.error('Texto do erro:', errorText);
                    comentariosList.innerHTML = '<div class="no-comments">Erro ao carregar comentários.</div>';
                    return;
                }

                const responseText = await response.text();
                console.log('Resposta bruta:', responseText);

                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Erro ao fazer parse do JSON:', parseError);
                    console.error('Resposta que causou erro:', responseText);
                    comentariosList.innerHTML = '<div class="no-comments">Erro ao processar comentários.</div>';
                    return;
                }

                console.log('Dados dos comentários:', data);
                console.log('Success:', data.success);
                console.log('Comentários:', data.comentarios);
                console.log('Quantidade de comentários:', data.comentarios ? data.comentarios.length : 'undefined');

                if (data.success) {
                    if (data.comentarios && data.comentarios.length > 0) {
                        console.log('Renderizando comentários...');

                        // Renderiza comentários com curtidas
                        let html = '';
                        for (const comment of data.comentarios) {
                            // Busca curtidas do comentário
                            let likesData = { total_likes: 0, user_liked: false };
                            try {
                                const likesResponse = await fetch(`php/buscar_curtidas_comentario.php?comentario_id=${comment.id}`);
                                const likesResult = await likesResponse.json();
                                if (likesResult.success) {
                                    likesData = likesResult;
                                }
                            } catch (error) {
                                console.error('Erro ao buscar curtidas:', error);
                            }

                            const likesText = likesData.total_likes > 0 ? ` (${likesData.total_likes})` : '';
                            const heartIcon = likesData.user_liked ?
                                '<i class="fas fa-heart" style="color: #ff6600;"></i>' :
                                '<i class="fas fa-heart"></i>';
                            const heartText = likesData.user_liked ? 'Curtido' : 'Curtir';
                            const heartStyle = likesData.user_liked ?
                                'color: #ff6600; border-color: #ff6600; background: #444;' : '';

                            html += `
                                <div class="comentario-item">
                                    <div class="comentario-header">
                                        <div class="comentario-user-info">
                                            <img src="${comment.foto_usuario || 'img/default-avatar.png'}" alt="Foto do usuário" class="comentario-avatar" />
                                            <h3 class="comentario-author">${comment.usuario}</h3>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <span class="comentario-date">${formatarData(comment.data)}</span>
                                        </div>
                                    </div>
                                    <div class="comentario-body">
                                        <div class="comentario-text">${comment.texto}</div>
                                    </div>
                                    <div class="comentario-actions">
                                        <button class="comentario-action-btn" onclick="responderComentario('${comment.id}')">
                                            <i class="fas fa-reply"></i> Responder
                                        </button>
                                        <button class="comentario-action-btn" onclick="curtirComentario('${comment.id}')" style="${heartStyle}">
                                            ${heartIcon} ${heartText}${likesText}
                                        </button>
                                    </div>
                                </div>
                            `;
                        }

                        comentariosList.innerHTML = html;
                        console.log('Comentários renderizados com sucesso');
                    } else {
                        console.log('Nenhum comentário encontrado, mostrando mensagem');
                        comentariosList.innerHTML = '<div class="no-comments">Nenhum comentário ainda. Seja o primeiro a comentar!</div>';
                    }
                } else {
                    console.log('Erro no success, mostrando mensagem de erro');
                    comentariosList.innerHTML = '<div class="no-comments">Erro ao carregar comentários.</div>';
                }
            } catch (error) {
                console.error('Erro ao carregar comentários:', error);
                comentariosList.innerHTML = '<div class="no-comments">Erro ao carregar comentários.</div>';
            }
        }

        // Função para fazer scroll até os comentários e focar no input
        function scrollToComments() {
            const comentarioForm = document.getElementById('comentarioForm');
            const comentarioInput = document.getElementById('comentarioText');

            if (comentarioForm) {
                // Faz scroll suave até o formulário de comentários
                comentarioForm.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Foca no input após um pequeno delay para garantir que o scroll terminou
                setTimeout(() => {
                    if (comentarioInput) {
                        comentarioInput.focus();
                        console.log('Foco aplicado no input de comentários');
                    }
                }, 500);

                console.log('Scroll para comentários executado');
            } else {
                console.error('Formulário de comentários não encontrado');
            }
        }

        // Funções para ações dos comentários
        function responderComentario(comentarioId) {
            console.log('Responder comentário:', comentarioId);
            // Mostra formulário de resposta inline
            mostrarFormularioResposta(comentarioId);
        }

        async function curtirComentario(comentarioId) {
            console.log('Curtir comentário:', comentarioId);

            const btn = event.target.closest('.comentario-action-btn');
            if (!btn) return;

            // Desabilita o botão durante a requisição
            btn.disabled = true;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';

            try {
                const response = await fetch('php/curtir_comentario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        comentario_id: comentarioId
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Atualiza o visual do botão
                    if (result.action === 'added') {
                        btn.innerHTML = '<i class="fas fa-heart" style="color: #ff6600;"></i> Curtido';
                        btn.style.color = '#ff6600';
                        btn.style.borderColor = '#ff6600';
                        btn.style.background = '#444';
                    } else {
                        btn.innerHTML = '<i class="fas fa-heart"></i> Curtir';
                        btn.style.color = '#aaa';
                        btn.style.borderColor = '#555';
                        btn.style.background = '#333';
                    }

                    // Atualiza contador de curtidas
                    await atualizarContadorCurtidas(comentarioId);
                } else {
                    alert('Erro: ' + result.message);
                    btn.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Erro ao curtir comentário:', error);
                alert('Erro ao curtir comentário');
                btn.innerHTML = originalText;
            } finally {
                btn.disabled = false;
            }
        }

        // Função para atualizar contador de curtidas
        async function atualizarContadorCurtidas(comentarioId) {
            try {
                const response = await fetch(`php/buscar_curtidas_comentario.php?comentario_id=${comentarioId}`);
                const result = await response.json();

                if (result.success) {
                    const btn = document.querySelector(`[onclick*="curtirComentario('${comentarioId}')"]`);
                    if (btn) {
                        const likesText = result.total_likes > 0 ? ` (${result.total_likes})` : '';
                        if (result.user_liked) {
                            btn.innerHTML = `<i class="fas fa-heart" style="color: #ff6600;"></i> Curtido${likesText}`;
                        } else {
                            btn.innerHTML = `<i class="fas fa-heart"></i> Curtir${likesText}`;
                        }
                    }
                }
            } catch (error) {
                console.error('Erro ao atualizar contador:', error);
            }
        }

        // Função para mostrar formulário de resposta
        function mostrarFormularioResposta(comentarioPaiId) {
            console.log('Mostrando formulário de resposta para:', comentarioPaiId);

            // Remove formulários de resposta existentes
            const formulariosExistentes = document.querySelectorAll('.formulario-resposta');
            formulariosExistentes.forEach(form => form.remove());

            // Encontra o comentário pai
            const comentarioItem = document.querySelector(`[onclick*="responderComentario('${comentarioPaiId}')"]`).closest('.comentario-item');
            if (!comentarioItem) {
                console.error('Comentário pai não encontrado');
                return;
            }

            // Cria novo formulário de resposta
            const formularioResposta = document.createElement('div');
            formularioResposta.className = 'formulario-resposta';
            formularioResposta.innerHTML = `
                <div class="comentario-form resposta-form">
                    <textarea 
                        class="comentario-input" 
                        placeholder="Escreva sua resposta..." 
                        required
                    ></textarea>
                    <div class="resposta-actions">
                        <button type="button" class="comentario-submit" onclick="enviarResposta('${comentarioPaiId}')">
                            <i class="fas fa-paper-plane"></i> Responder
                        </button>
                        <button type="button" class="cancelar-resposta" onclick="cancelarResposta()">
                            Cancelar
                        </button>
                    </div>
                </div>
            `;

            comentarioItem.appendChild(formularioResposta);

            // Foca no textarea
            const textarea = formularioResposta.querySelector('textarea');
            textarea.focus();
        }

        // Função para enviar resposta
        async function enviarResposta(comentarioPaiId) {
            const formularioResposta = document.querySelector('.formulario-resposta');
            if (!formularioResposta) return;

            const textarea = formularioResposta.querySelector('textarea');
            const texto = textarea.value.trim();

            if (!texto) {
                alert('Digite uma resposta');
                return;
            }

            const submitButton = formularioResposta.querySelector('.comentario-submit');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

            try {
                const result = await enviarComentario('<?= $post['id'] ?>', texto, comentarioPaiId);

                if (result.success) {
                    // Recarrega comentários
                    await carregarComentarios();
                    cancelarResposta();
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
        }

        function cancelarResposta() {
            const formulariosExistentes = document.querySelectorAll('.formulario-resposta');
            formulariosExistentes.forEach(form => form.remove());
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
        async function enviarComentario(postId, texto) {
            try {
                console.log('Enviando comentário:', { postId, texto });

                const response = await fetch('php/criar_comentario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_postagem: postId,
                        texto: texto
                    })
                });

                console.log('Resposta do servidor:', response.status);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Erro HTTP:', response.status, errorText);
                    return { success: false, message: `Erro do servidor: ${response.status}` };
                }

                const data = await response.json();
                console.log('Dados recebidos:', data);
                return data;
            } catch (error) {
                console.error('Erro ao enviar comentário:', error);
                return { success: false, message: 'Erro de conexão: ' + error.message };
            }
        }

        // Função para verificar se o texto precisa ser truncado
        function verificarTruncamentoTexto() {
            console.log('=== VERIFICANDO TRUNCAMENTO EM POSTAGEM ===');

            const textContainers = document.querySelectorAll('.post-text-container');
            console.log('Encontrados', textContainers.length, 'containers de texto');

            textContainers.forEach((container, index) => {
                console.log(`\n--- Container ${index} ---`);
                const textElement = container.querySelector('.post-text-truncated');
                const expandButton = container.querySelector('.expand-button');

                if (textElement && expandButton) {
                    const limiteCaracteres = 200;

                    // Verifica se o texto já foi expandido pelo usuário
                    const limiteAtual = parseInt(textElement.getAttribute('data-limite-atual') || '0');
                    const foiExpandido = limiteAtual > limiteCaracteres;

                    // Se foi expandido pelo usuário, não re-truncar
                    if (foiExpandido) {
                        console.log(`ℹ️ Texto já foi expandido pelo usuário, mantendo estado atual`);
                        return;
                    }

                    // Obter texto original - se não existe, usar o atual
                    let textoOriginalHTML = textElement.getAttribute('data-texto-original');
                    if (!textoOriginalHTML) {
                        textoOriginalHTML = textElement.innerHTML;
                        textElement.setAttribute('data-texto-original', textoOriginalHTML);
                    }

                    const textoOriginalTexto = textElement.textContent.trim();

                    console.log(`Texto original: "${textoOriginalTexto}"`);
                    console.log(`Tamanho: ${textoOriginalTexto.length} caracteres`);
                    console.log(`Limite: ${limiteCaracteres} caracteres`);
                    console.log(`Limite atual: ${limiteAtual}, Foi expandido: ${foiExpandido}`);

                    // Verifica se precisa truncar por caracteres OU por largura
                    const precisaTruncar = textoOriginalTexto.length > limiteCaracteres ||
                        verificarEstouroLargura(textElement);

                    if (precisaTruncar) {
                        // Para textos com HTML, vamos usar uma abordagem mais simples
                        // Criar um elemento temporário para trabalhar com o HTML
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = textoOriginalHTML;

                        // Obter o texto puro para truncamento
                        const textoPuro = tempDiv.textContent || tempDiv.innerText || '';
                        const textoTruncado = textoPuro.substring(0, limiteCaracteres) + '...';

                        // Se o HTML original tinha <br>, manter a estrutura
                        if (textoOriginalHTML.includes('<br')) {
                            // Criar HTML truncado mantendo algumas quebras de linha
                            const linhas = textoOriginalHTML.split('<br');
                            let textoTruncadoHTML = '';
                            let contadorCaracteres = 0;

                            for (let i = 0; i < linhas.length && contadorCaracteres < limiteCaracteres; i++) {
                                const linhaTexto = linhas[i].replace(/<[^>]*>/g, ''); // Remove outras tags
                                if (contadorCaracteres + linhaTexto.length <= limiteCaracteres) {
                                    textoTruncadoHTML += linhas[i] + (i < linhas.length - 1 ? '<br' : '');
                                    contadorCaracteres += linhaTexto.length;
                                } else {
                                    const resto = limiteCaracteres - contadorCaracteres;
                                    textoTruncadoHTML += linhaTexto.substring(0, resto) + '...';
                                    break;
                                }
                            }

                            textElement.innerHTML = textoTruncadoHTML;
                        } else {
                            // Texto simples, truncar normalmente
                            textElement.innerHTML = textoTruncado.replace(/\n/g, '<br>');
                        }

                        textElement.setAttribute('data-texto-original', textoOriginalHTML);
                        textElement.setAttribute('data-limite-atual', limiteCaracteres);
                        expandButton.setAttribute('data-texto-original', textoOriginalHTML);

                        // Força a visibilidade do botão
                        expandButton.style.display = 'inline-block';
                        expandButton.style.visibility = 'visible';
                        expandButton.style.opacity = '1';
                        expandButton.classList.add('show');
                        expandButton.onclick = () => expandirTexto(container.dataset.postId);

                        console.log(`TRUNCADO: ${textoOriginalTexto.length} -> ${textElement.innerHTML.length} caracteres`);
                        console.log(`Texto truncado: "${textElement.innerHTML}"`);
                        console.log(`Botão expandir: display=${expandButton.style.display}, class=${expandButton.className}`);
                    } else {
                        // Se não precisa truncar, mas o botão não está visível, mostrar
                        if (textoOriginalTexto.length > limiteCaracteres || verificarEstouroLargura(textElement)) {
                            expandButton.style.display = 'inline-block';
                            expandButton.style.visibility = 'visible';
                            expandButton.style.opacity = '1';
                            expandButton.classList.add('show');
                            expandButton.onclick = () => expandirTexto(container.dataset.postId);

                            // Armazena o texto original
                            textElement.setAttribute('data-texto-original', textoOriginalHTML);
                            textElement.setAttribute('data-limite-atual', limiteCaracteres);
                            expandButton.setAttribute('data-texto-original', textoOriginalHTML);

                            console.log(`Botão mostrado para texto que precisa truncar`);
                        } else {
                            // Não esconder o botão se ele já está visível e tem onclick
                            if (expandButton.onclick && expandButton.classList.contains('show')) {
                                console.log(`Mantendo botão visível (já configurado)`);
                            } else {
                                expandButton.style.display = 'none';
                                expandButton.classList.remove('show');
                                console.log(`Não precisa truncar (${textoOriginalTexto.length} <= ${limiteCaracteres})`);
                            }
                        }
                    }
                }
            });
        }

        // Função para verificar se o texto está estourando em largura
        function verificarEstouroLargura(elemento) {
            // Cria uma cópia temporária para medir
            const tempElement = elemento.cloneNode(true);
            tempElement.style.position = 'absolute';
            tempElement.style.visibility = 'hidden';
            tempElement.style.whiteSpace = 'nowrap';
            tempElement.style.width = 'auto';

            document.body.appendChild(tempElement);

            const larguraTexto = tempElement.offsetWidth;
            const larguraContainer = elemento.parentElement.offsetWidth;

            document.body.removeChild(tempElement);

            const estourou = larguraTexto > larguraContainer;

            if (estourou) {
                console.log(`Texto estourou em largura: ${larguraTexto}px > ${larguraContainer}px`);
            }

            return estourou;
        }

        // Função para expandir texto da postagem (progressivo)
        function expandirTexto(postId) {
            console.log('=== EXPANDINDO TEXTO ===');
            console.log('Post ID:', postId);

            const container = document.querySelector(`[data-post-id="${postId}"]`);
            if (!container) {
                console.error('Container não encontrado para post', postId);
                return;
            }

            const textElement = container.querySelector('.post-text-truncated');
            const expandButton = container.querySelector('.expand-button');

            if (!textElement || !expandButton) {
                console.error('Elementos não encontrados para post', postId);
                return;
            }

            const textoOriginalHTML = textElement.getAttribute('data-texto-original') || expandButton.getAttribute('data-texto-original');
            const limiteAtual = parseInt(textElement.getAttribute('data-limite-atual') || '200');

            console.log('Texto original HTML:', !!textoOriginalHTML);
            console.log('Limite atual:', limiteAtual);

            if (!textoOriginalHTML) {
                console.error('Texto original não encontrado para post', postId);
                return;
            }

            // Obter texto puro do HTML original
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = textoOriginalHTML;
            const textoPuroOriginal = tempDiv.textContent || tempDiv.innerText || '';
            const novoLimite = limiteAtual + 100; // Aumenta 100 caracteres por vez

            console.log('Texto puro original:', textoPuroOriginal.substring(0, 100) + '...');
            console.log('Tamanho original:', textoPuroOriginal.length);
            console.log('Novo limite:', novoLimite);

            // Se o novo limite excede o texto original, mostra tudo
            if (novoLimite >= textoPuroOriginal.length) {
                textElement.innerHTML = textoOriginalHTML;
                expandButton.textContent = 'Ver menos...';
                expandButton.onclick = () => contrairTexto(postId);
                textElement.removeAttribute('data-limite-atual');
                console.log(`Texto expandido completamente para post ${postId}`);
            } else {
                // Expande progressivamente usando o texto original completo
                const textoTruncado = textoPuroOriginal.substring(0, novoLimite) + '...';

                textElement.innerHTML = textoTruncado.replace(/\n/g, '<br>');
                textElement.setAttribute('data-limite-atual', novoLimite);
                expandButton.textContent = 'Ver mais...';
                console.log(`Texto expandido progressivamente para post ${postId} (${novoLimite} chars)`);
            }
        }

        // Função para contrair texto da postagem
        function contrairTexto(postId) {
            console.log('Contraindo texto para post:', postId);
            const container = document.querySelector(`[data-post-id="${postId}"]`);
            if (container) {
                const textElement = container.querySelector('.post-text-truncated');
                const expandButton = container.querySelector('.expand-button');

                if (textElement && expandButton) {
                    const textoOriginalHTML = textElement.getAttribute('data-texto-original') || expandButton.getAttribute('data-texto-original');
                    if (textoOriginalHTML) {
                        const limiteCaracteres = 200;

                        // Trunca o HTML original mantendo as tags <br>
                        let textoTruncadoHTML = textoOriginalHTML;

                        // Se o HTML tem <br>, precisa truncar de forma inteligente
                        if (textoOriginalHTML.includes('<br>')) {
                            // Remove as tags <br> temporariamente para contar caracteres
                            const textoLimpo = textoOriginalHTML.replace(/<br\s*\/?>/gi, '\n');
                            const textoTruncadoLimpo = textoLimpo.substring(0, limiteCaracteres) + '...';

                            // Reconverte quebras de linha para <br>
                            textoTruncadoHTML = textoTruncadoLimpo.replace(/\n/g, '<br>');
                        } else {
                            // Se não tem <br>, trunca normalmente
                            const textoOriginalTexto = textElement.textContent.trim();
                            const textoTruncadoTexto = textoOriginalTexto.substring(0, limiteCaracteres) + '...';
                            textoTruncadoHTML = textoTruncadoTexto.replace(/\n/g, '<br>');
                        }

                        textElement.innerHTML = textoTruncadoHTML;
                        textElement.setAttribute('data-limite-atual', limiteCaracteres);
                        expandButton.textContent = 'Ver mais...';
                        expandButton.onclick = () => expandirTexto(postId);
                        console.log(`Texto contraído para post ${postId}`);
                    }
                }
            }
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', function () {
            carregarImagemObra();
            carregarEstadoReacoes();
            carregarComentarios();

            // Executar truncamento
            verificarTruncamentoTexto();

            // Executar truncamento após um delay
            setTimeout(() => {
                verificarTruncamentoTexto();
            }, 500);

            // Configurar formulário de comentários
            const comentarioForm = document.getElementById('comentarioForm');
            comentarioForm.addEventListener('submit', async function (e) {
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

                        console.log('Comentário enviado com sucesso!');

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
                    submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar';
                }
            });
        });

        // Função para copiar link do post
        function copiarLink(event, postId) {
            event.preventDefault();
            const url = `${window.location.origin}/postagem.php?id=${postId}`;
            navigator.clipboard.writeText(url).then(() => {
                alert('Link copiado para a área de transferência!');
            }).catch(err => {
                console.error('Erro ao copiar link:', err);
                alert('Erro ao copiar link');
            });
        }

        // Função para favoritar/desfavoritar post
        async function toggleFavorito(event, postId) {
            event.preventDefault();

            try {
                const response = await fetch('php/favoritar_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ post_id: postId })
                });

                const data = await response.json();

                if (data.success) {
                    const icon = document.getElementById(`fav-icon-${postId}`);
                    const text = document.getElementById(`fav-text-${postId}`);

                    if (data.action === 'added') {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        icon.style.color = '#ff6600';
                        text.textContent = 'Favoritado';
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        icon.style.color = '';
                        text.textContent = 'Favoritar';
                    }
                } else {
                    alert('Erro ao favoritar: ' + data.message);
                }
            } catch (error) {
                console.error('Erro ao favoritar:', error);
                alert('Erro ao favoritar post');
            }
        }

        // Função para abrir modal de denúncia
        function abrirDenuncia(event, id, tipo) {
            event.preventDefault();

            const motivo = prompt(`Por que você está denunciando ${tipo === 'postagem' ? 'esta postagem' : 'este comentário'}?\n\nEscreva o motivo:`);

            if (motivo && motivo.trim()) {
                fetch('php/criar_denuncia.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        tipo: tipo,
                        id_conteudo: id,
                        motivo: motivo.trim()
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Denúncia enviada com sucesso! Nossa equipe irá analisar.');
                        } else {
                            alert('Erro ao enviar denúncia: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao enviar denúncia');
                    });
            }
        }

        // Verificar se o post está favoritado ao carregar a página
        async function verificarFavoritos() {
            const postId = '<?= $post['id'] ?>';

            try {
                const response = await fetch(`php/verificar_favorito.php?post_id=${postId}`);
                const data = await response.json();

                if (data.success && data.is_favorited) {
                    const icon = document.getElementById(`fav-icon-${postId}`);
                    const text = document.getElementById(`fav-text-${postId}`);

                    if (icon && text) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        icon.style.color = '#ff6600';
                        text.textContent = 'Favoritado';
                    }
                }
            } catch (error) {
                console.error('Erro ao verificar favoritos:', error);
            }
        }

        // Chamar verificação de favoritos ao carregar
        document.addEventListener('DOMContentLoaded', function () {
            verificarFavoritos();
        });
    </script>
</body>

</html>