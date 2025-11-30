<?php
// Página para visualizar uma obra específica

session_start();
require_once 'php/config.php';
include 'vlibras_include.php';


// Verifica se o ID da obra foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: telainicial.php');
    exit;
}

$obraId = $_GET['id'];
$obraTipo = $_GET['tipo'] ?? 'serie'; // Tipo padrão

// Busca o tipo da obra na tabela obras usando o id_obra da postagem
try {
    $sql = "SELECT tipo FROM obras WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $obraId);
    $stmt->execute();
    $result = $stmt->get_result();
    $obraData = $result->fetch_assoc();

    if ($obraData && $obraData['tipo']) {
        $obraTipo = $obraData['tipo'];
        error_log("Obra ID: $obraId, Tipo encontrado na tabela obras: $obraTipo");
    } else {
        $obraTipo = $_GET['tipo'] ?? 'serie';
        error_log("Obra ID: $obraId, Tipo não encontrado na tabela obras, usando padrão: $obraTipo");
    }
} catch (Exception $e) {
    $obraTipo = $_GET['tipo'] ?? 'serie';
    error_log("Erro ao buscar tipo da obra: " . $e->getMessage());
}

// Busca as análises da obra usando o ID da obra
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
                END as foto
            FROM postagens p
            INNER JOIN login u ON p.id_usuario = u.id
            LEFT JOIN perfil pf ON p.id_usuario = pf.id
            WHERE p.id_obra = ?
            ORDER BY p.data_post DESC
            LIMIT 10";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $obraId);
    $stmt->execute();
    $result = $stmt->get_result();
    $analises = $result->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    $analises = [];
}

// Busca estatísticas da obra
try {
    $sql = "SELECT 
                COUNT(*) as total_analises,
                AVG(CASE WHEN v.tipo = 'like' THEN 1 ELSE 0 END) as media_likes
            FROM postagens p
            LEFT JOIN votos v ON p.id = v.id_postagem
            WHERE p.id_obra = ?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $obraId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();
} catch (Exception $e) {
    $stats = ['total_analises' => 0, 'media_likes' => 0];
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="page-title">Carregando obra... - RepArte</title>
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
            <a href="php/logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </header>

    <main>
        <div class="obra-completa">
            <!-- Informações da Obra -->
            <div class="obra-info">
                <div class="obra-imagem">
                    <img id="obra-imagem" src="" alt="Imagem da obra" />
                </div>
                <div class="obra-detalhes">
                    <h1 id="obra-titulo" class="obra-titulo">Carregando...</h1>
                    <div class="obra-meta">
                        <span id="obra-tipo" class="obra-tipo"><?= ucfirst($obraTipo) ?></span>
                        <span id="obra-ano" class="obra-ano">-</span>
                        <span id="obra-autor" class="obra-autor">-</span>
                    </div>
                    <div class="obra-descricao">
                        <p id="obra-descricao">Carregando descrição...</p>
                    </div>
                    <div class="obra-stats">
                        <div class="stat-item">
                            <i class="fas fa-comment-dots"></i>
                            <span><?= $stats['total_analises'] ?> análises</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Análises da Obra -->
            <div class="analises-section">
                <h2><i class="fas fa-comment-dots"></i> Análises da Obra</h2>
                <?php if (!empty($analises)): ?>
                    <div class="analises-grid">
                        <?php foreach ($analises as $analise): ?>
                            <div class="analise-card">
                                <div class="analise-header">
                                    <div class="analise-user">
                                        <img src="<?= htmlspecialchars($analise['foto']) ?>" alt="Foto do Usuário"
                                            class="analise-user-photo" />
                                        <span class="analise-username"><?= htmlspecialchars($analise['usuario']) ?></span>
                                    </div>
                                    <span class="analise-data"><?= date('d/m/Y H:i', strtotime($analise['data'])) ?></span>
                                </div>
                                <div class="analise-content">
                                    <h3 class="analise-titulo"><?= htmlspecialchars($analise['titulo']) ?></h3>
                                    <p class="analise-texto"><?= nl2br(htmlspecialchars($analise['texto'])) ?></p>
                                </div>
                                <div class="analise-actions">
                                    <a href="postagem.php?id=<?= $analise['id'] ?>" class="btn-ver-analise">
                                        <i class="fas fa-external-link-alt"></i> Ver análise completa
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-analises">
                        <i class="fas fa-comment-slash"></i>
                        <p>Nenhuma análise encontrada para esta obra.</p>
                        <p>Seja o primeiro a compartilhar sua opinião!</p>
                    </div>
                <?php endif; ?>
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

        // Carrega os dados da obra via API
        async function carregarDadosObra() {
            const obraId = '<?= $obraId ?>';
            const obraTipo = '<?= $obraTipo ?>';

            try {
                console.log('=== CARREGANDO DADOS DA OBRA ===');
                console.log('Obra ID:', obraId);
                console.log('Obra Tipo:', obraTipo);

                const obra = await obterDetalhesObra({
                    apiId: obraId,
                    tipo: obraTipo
                });

                console.log('✅ Dados da obra carregados:', obra);
                console.log('Título:', obra.titulo);
                console.log('Autor:', obra.autor);
                console.log('Ano:', obra.ano);
                console.log('Imagem:', obra.imagem);
                console.log('Descrição:', obra.descricao);

                // Determinar cores baseadas no tipo da obra
                let corPrimaria, corSecundaria;
                switch (obraTipo) {
                    case 'filme':
                        corPrimaria = '#ff6600';
                        corSecundaria = '#cc5200';
                        break;
                    case 'serie':
                        corPrimaria = '#9C27B0';
                        corSecundaria = '#7B1FA2';
                        break;
                    case 'livro':
                        corPrimaria = '#4CAF50';
                        corSecundaria = '#388E3C';
                        break;
                    case 'arte':
                        corPrimaria = '#2196F3';
                        corSecundaria = '#1976D2';
                        break;
                    case 'musica':
                        corPrimaria = '#FFC107';
                        corSecundaria = '#FFA000';
                        break;
                    default:
                        corPrimaria = '#ff6600';
                        corSecundaria = '#cc5200';
                }

                console.log('Cores aplicadas para', obraTipo, ':', corPrimaria);

                // Aplicar cores à imagem da obra
                const imgElement = document.getElementById('obra-imagem');
                if (imgElement) {
                    imgElement.style.border = `3px solid ${corPrimaria}`;
                    imgElement.style.boxShadow = `0 8px 25px rgba(0, 0, 0, 0.3), 0 0 20px ${corPrimaria}40`;
                    console.log('✅ Borda aplicada na imagem');
                } else {
                    console.error('❌ Elemento obra-imagem não encontrado');
                }

                // Aplicar cores ao tipo da obra
                const tipoElement = document.getElementById('obra-tipo');
                if (tipoElement) {
                    tipoElement.style.background = corPrimaria;
                    tipoElement.style.color = '#fff';
                    console.log('✅ Cores aplicadas no tipo');
                } else {
                    console.error('❌ Elemento obra-tipo não encontrado');
                }

                // Aplicar cores aos stats
                const statItems = document.querySelectorAll('.stat-item');
                statItems.forEach(item => {
                    const icon = item.querySelector('i');
                    if (icon) {
                        icon.style.color = corPrimaria;
                    }
                });
                console.log('✅ Cores aplicadas nos stats:', statItems.length, 'itens');

                // Aplicar cores ao título das análises
                const analisesTitle = document.querySelector('.analises-section h2 i');
                if (analisesTitle) {
                    analisesTitle.style.color = corPrimaria;
                    console.log('✅ Cor aplicada no título das análises');
                }

                // Aplicar cores aos botões de ver análise
                const btnVerAnalise = document.querySelectorAll('.btn-ver-analise');
                btnVerAnalise.forEach(btn => {
                    btn.style.background = corPrimaria;
                });
                console.log('✅ Cores aplicadas nos botões:', btnVerAnalise.length, 'botões');

                // Aplicar cores aos cards de análise no hover
                const analiseCards = document.querySelectorAll('.analise-card');
                analiseCards.forEach(card => {
                    card.addEventListener('mouseenter', () => {
                        card.style.borderColor = corPrimaria;
                    });
                    card.addEventListener('mouseleave', () => {
                        card.style.borderColor = '#333';
                    });
                });
                console.log('✅ Event listeners aplicados nos cards:', analiseCards.length, 'cards');

                // Atualiza a imagem com loading (apenas após 10 segundos)
                const imgElement2 = document.getElementById('obra-imagem');
                if (imgElement2 && obra.imagem) {
                    let loadingDiv = null;
                    let loadingTimeout = null;
                    let imageLoaded = false;

                    // Configura timeout para mostrar loading após 10 segundos
                    loadingTimeout = setTimeout(() => {
                        if (!imageLoaded) {
                            loadingDiv = criarLoadingPlaceholder();
                            imgElement2.parentNode.style.position = 'relative';
                            imgElement2.parentNode.appendChild(loadingDiv);
                        }
                    }, 10000); // 10 segundos

                    // Quando a imagem carregar, remove o loading e cancela timeout
                    imgElement2.onload = () => {
                        imageLoaded = true;
                        if (loadingTimeout) {
                            clearTimeout(loadingTimeout);
                        }
                        if (loadingDiv) {
                            loadingDiv.remove();
                        }
                    };

                    imgElement2.onerror = () => {
                        imageLoaded = true;
                        if (loadingTimeout) {
                            clearTimeout(loadingTimeout);
                        }
                        if (loadingDiv) {
                            loadingDiv.remove();
                        }
                    };

                    imgElement2.src = obra.imagem;
                    imgElement2.alt = obra.titulo;
                    console.log('✅ Imagem atualizada:', obra.imagem);
                } else {
                    console.error('❌ Erro ao atualizar imagem');
                }

                // Atualiza o título
                const tituloElement = document.getElementById('obra-titulo');
                if (tituloElement && obra.titulo) {
                    tituloElement.textContent = obra.titulo;
                    console.log('✅ Título atualizado:', obra.titulo);
                } else {
                    console.error('❌ Erro ao atualizar título');
                }

                // Atualiza o título da página
                const pageTitleElement = document.getElementById('page-title');
                if (pageTitleElement && obra.titulo) {
                    pageTitleElement.textContent = `${obra.titulo} - RepArte`;
                    console.log('✅ Título da página atualizado:', obra.titulo);
                }

                // Atualiza o ano
                if (obra.ano) {
                    const anoElement = document.getElementById('obra-ano');
                    if (anoElement) {
                        anoElement.textContent = obra.ano;
                        console.log('✅ Ano atualizado:', obra.ano);
                    }
                }

                // Atualiza o autor/criador - só mostra se existir
                const autorElement = document.getElementById('obra-autor');
                if (autorElement) {
                    if (obra.autor && obra.autor.trim() !== '') {
                        autorElement.textContent = obra.autor;
                        autorElement.style.display = 'inline';
                        console.log('✅ Autor atualizado:', obra.autor);
                    } else {
                        autorElement.style.display = 'none';
                        console.log('ℹ️ Autor oculto (não encontrado)');
                    }
                }

                // Atualiza a descrição
                if (obra.descricao) {
                    const descricaoElement = document.getElementById('obra-descricao');
                    if (descricaoElement) {
                        descricaoElement.textContent = obra.descricao;
                        console.log('✅ Descrição atualizada');
                    }
                }

                // Atualiza o tipo
                const tipoElement2 = document.getElementById('obra-tipo');
                if (tipoElement2) {
                    tipoElement2.textContent = obra.tipo || obraTipo;
                    console.log('✅ Tipo atualizado:', obra.tipo || obraTipo);
                }

                console.log('=== CARREGAMENTO CONCLUÍDO COM SUCESSO ===');

            } catch (error) {
                console.error('❌ ERRO ao carregar dados da obra:', error);
                console.error('Tipo detectado:', obraTipo);
                console.error('ID da obra:', obraId);

                // Fallback para dados básicos
                const tituloElement = document.getElementById('obra-titulo');
                if (tituloElement) {
                    tituloElement.textContent = 'Obra ID: ' + obraId + ' (Tipo: ' + obraTipo + ')';
                }

                const descricaoElement = document.getElementById('obra-descricao');
                if (descricaoElement) {
                    descricaoElement.textContent = 'Não foi possível carregar os detalhes desta obra. Erro: ' + error.message;
                }

                // Imagem placeholder
                const imgElement = document.getElementById('obra-imagem');
                if (imgElement) {
                    imgElement.src = 'https://placehold.co/400x600/333333/FFFFFF?text=Sem+Imagem';
                }

                // Aplicar cores mesmo com erro
                aplicarCoresTema(obraTipo);
            }
        }

        // Função para aplicar cores após carregamento completo
        function aplicarCoresTema(obraTipo) {
            console.log('=== APLICANDO CORES TEMA ===');
            console.log('Tipo recebido:', obraTipo);

            let corPrimaria, corSecundaria;
            switch (obraTipo) {
                case 'filme':
                    corPrimaria = '#ff6600';
                    corSecundaria = '#cc5200';
                    break;
                case 'serie':
                    corPrimaria = '#9C27B0';
                    corSecundaria = '#7B1FA2';
                    break;
                case 'livro':
                    corPrimaria = '#4CAF50';
                    corSecundaria = '#388E3C';
                    break;
                case 'arte':
                    corPrimaria = '#2196F3';
                    corSecundaria = '#1976D2';
                    break;
                case 'musica':
                    corPrimaria = '#FFC107';
                    corSecundaria = '#FFA000';
                    break;
                default:
                    corPrimaria = '#ff6600';
                    corSecundaria = '#cc5200';
            }

            console.log('Cores definidas:', corPrimaria, corSecundaria);

            // Aplicar cores à imagem da obra
            const imgElement = document.getElementById('obra-imagem');
            if (imgElement) {
                imgElement.style.border = `3px solid ${corPrimaria}`;
                imgElement.style.boxShadow = `0 8px 25px rgba(0, 0, 0, 0.3), 0 0 20px ${corPrimaria}40`;
                console.log('✅ Borda aplicada na imagem:', corPrimaria);
            } else {
                console.error('❌ Elemento obra-imagem não encontrado');
            }

            // Aplicar cores ao tipo da obra
            const tipoElement = document.getElementById('obra-tipo');
            if (tipoElement) {
                tipoElement.style.background = corPrimaria;
                tipoElement.style.color = '#fff';
                console.log('✅ Cores aplicadas no tipo:', corPrimaria);
            } else {
                console.error('❌ Elemento obra-tipo não encontrado');
            }

            // Aplicar cores aos stats
            const statItems = document.querySelectorAll('.stat-item');
            statItems.forEach(item => {
                const icon = item.querySelector('i');
                if (icon) {
                    icon.style.color = corPrimaria;
                }
            });
            console.log('✅ Cores aplicadas nos stats:', statItems.length, 'itens');

            // Aplicar cores ao título das análises
            const analisesTitle = document.querySelector('.analises-section h2 i');
            if (analisesTitle) {
                analisesTitle.style.color = corPrimaria;
                console.log('✅ Cor aplicada no título das análises');
            } else {
                console.error('❌ Elemento título das análises não encontrado');
            }

            // Aplicar cores aos botões de ver análise
            const btnVerAnalise = document.querySelectorAll('.btn-ver-analise');
            btnVerAnalise.forEach(btn => {
                btn.style.background = corPrimaria;
            });
            console.log('✅ Cores aplicadas nos botões:', btnVerAnalise.length, 'botões');

            // Aplicar cores aos cards de análise no hover
            const analiseCards = document.querySelectorAll('.analise-card');
            analiseCards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.borderColor = corPrimaria;
                });
                card.addEventListener('mouseleave', () => {
                    card.style.borderColor = '#333';
                });
            });
            console.log('✅ Event listeners aplicados nos cards:', analiseCards.length, 'cards');
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', function () {
            console.log('=== INICIANDO CARREGAMENTO DA OBRA ===');
            console.log('Obra ID:', '<?= $obraId ?>');
            console.log('Obra Tipo detectado:', '<?= $obraTipo ?>');
            console.log('Tipo da URL:', '<?= $_GET['tipo'] ?? 'não definido' ?>');

            // Verificar se a função obterDetalhesObra existe
            if (typeof obterDetalhesObra === 'function') {
                console.log('✅ Função obterDetalhesObra encontrada');
            } else {
                console.error('❌ Função obterDetalhesObra NÃO encontrada');
            }

            carregarDadosObra();

            // Aplicar cores após um pequeno delay para garantir que todos os elementos estejam carregados
            setTimeout(() => {
                console.log('Aplicando cores para tipo:', '<?= $obraTipo ?>');
                aplicarCoresTema('<?= $obraTipo ?>');
            }, 100);
        });
    </script>
</body>

</html>