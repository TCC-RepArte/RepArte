// Endpoints das APIs - caminhos corrigidos para ambiente InfinityFree
// Define as URLs para acessar as diferentes APIs (filmes, séries, músicas, etc.)
const API_ENDPOINTS = {
  movies: 'php/api-endpoints.php?action=search_movies',      // Buscar filmes
  tv: 'php/api-endpoints.php?action=search_tv',               // Buscar séries
  tracks: 'php/api-endpoints.php?action=search_tracks',       // Buscar músicas
  movieDetails: 'php/api-endpoints.php?action=movie_details', // Detalhes do filme
  tvDetails: 'php/api-endpoints.php?action=tv_details',      // Detalhes da série
  trackDetails: 'php/api-endpoints.php?action=track_details'  // Detalhes da música
};

// Calcula a distância entre duas strings (algoritmo de Levenshtein)
// Usado para ordenar os resultados de busca por relevância
// Quanto menor a distância, mais similar são as strings
function levenshteinDistance(a, b) {
  if (a.length === 0) return b.length;
  if (b.length === 0) return a.length;
  const matriz = [];
  for (let i = 0; i <= b.length; i++) {
    matriz[i] = [i];
  }
  for (let j = 0; j <= a.length; j++) {
    matriz[0][j] = j;
  }
  for (let i = 1; i <= b.length; i++) {
    for (let j = 1; j <= a.length; j++) {
      if (b.charAt(i - 1) === a.charAt(j - 1)) {
        matriz[i][j] = matriz[i - 1][j - 1];
      } else {
        matriz[i][j] = Math.min(
          matriz[i - 1][j - 1] + 1,
          matriz[i][j - 1] + 1,
          matriz[i - 1][j] + 1
        );
      }
    }
  }
  return matriz[b.length][a.length];
}

// Buscar filmes na API TMDB
// Retorna lista de filmes que correspondem ao termo de busca
async function buscarFilmes(termoBusca) {
  try {
    const url = `${API_ENDPOINTS.movies}&query=${encodeURIComponent(termoBusca)}`;
    console.log('Buscando filmes:', url);
    const response = await fetch(url);
    const data = await response.json();
    console.log('Resposta da API de filmes:', data);
    if (data.results && data.results.length > 0) {
      return data.results.slice(0, 10).map(filme => ({
        id: filme.id,
        tipo: 'filme',
        subtipo: filme.genre_ids?.includes(99) ? 'documentário' : 'filme',
        titulo: filme.title,
        ano: filme.release_date ? filme.release_date.split('-')[0] : 'Desconhecido',
        imagem: filme.poster_path 
          ? `https://image.tmdb.org/t/p/w92${filme.poster_path}`
          : 'https://placehold.co/92x138/333333/FFFFFF?text=Sem+Imagem',
        apiId: filme.id
      }));
    }
    return [];
  } catch (err) {
    console.error('Erro ao buscar filmes:', err);
    return [];
  }
}

// Buscar séries na API TMDB
// Retorna lista de séries que correspondem ao termo de busca
async function buscarSeries(termoBusca) {
  try {
    const url = `${API_ENDPOINTS.tv}&query=${encodeURIComponent(termoBusca)}`;
    console.log('Buscando séries:', url);
    const response = await fetch(url);
    const data = await response.json();
    console.log('Resposta da API de séries:', data);
    if (data.results && data.results.length > 0) {
      return data.results.slice(0, 10).map(serie => ({
        id: serie.id,
        tipo: 'serie',
        subtipo: serie.genre_ids?.includes(99) ? 'documentário' : 'série',
        titulo: serie.name,
        ano: serie.first_air_date ? serie.first_air_date.split('-')[0] : 'Desconhecido',
        imagem: serie.poster_path 
          ? `https://image.tmdb.org/t/p/w92${serie.poster_path}`
          : 'https://placehold.co/92x138/333333/FFFFFF?text=Sem+Imagem',
        apiId: serie.id
      }));
    }
    return [];
  } catch (err) {
    console.error('Erro ao buscar séries:', err);
    return [];
  }
}

// Buscar livros na API Google Books
// Retorna lista de livros que correspondem ao termo de busca
async function buscarLivros(termoBusca) {
  try {
    const url = `https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(termoBusca)}&maxResults=10&langRestrict=pt`;
    console.log('Buscando livros:', url);
    const response = await fetch(url);
    const data = await response.json();
    console.log('Resposta da API de livros:', data);
    if (data.items && data.items.length > 0) {
      return data.items.map(livro => {
        const volumeInfo = livro.volumeInfo || {};
        return {
          id: livro.id,
          tipo: 'livro',
          autor: volumeInfo.authors,
          titulo: volumeInfo.title || 'Título desconhecido',
          ano: volumeInfo.publishedDate ? volumeInfo.publishedDate.split('-')[0] : 'Desconhecido',
          imagem: (() => {
            const img = volumeInfo.imageLinks || {};
            const prioridades = ['large', 'medium', 'thumbnail', 'smallThumbnail'];
          
            for (const chave of prioridades) {
              let link = img[chave];
              if (typeof link === 'string' && link.startsWith('http')) {
                return `proxy.php?url=${encodeURIComponent(link)}`;
              }
            }
          
            return 'https://placehold.co/92x138/333333/FFFFFF?text=Sem+Capa';
          })(),
          
          autor: volumeInfo.authors ? volumeInfo.authors.join(', ') : 'Autor desconhecido',
          apiId: livro.id
        };
      });
    }
    return [];
  } catch (err) {
    console.error('Erro ao buscar livros:', err);
    return [];
  }
}

// Cache simples para evitar requisições repetidas
// Armazena resultados da API de arte para evitar chamadas desnecessárias
const cacheArte = new Map();
const CACHE_DURATION = 5 * 60 * 1000; // 5 minutos

// Função para processar requisições em lotes
// Evita sobrecarregar as APIs fazendo requisições em grupos pequenos
async function processarEmLotes(array, tamanhoLote, funcaoProcessamento) {
  const resultados = [];
  
  for (let i = 0; i < array.length; i += tamanhoLote) {
    const lote = array.slice(i, i + tamanhoLote);
    const promessasLote = lote.map(funcaoProcessamento);
    const resultadosLote = await Promise.all(promessasLote);
    resultados.push(...resultadosLote);
    
    // Pequena pausa entre lotes para não sobrecarregar a API
    if (i + tamanhoLote < array.length) {
      await new Promise(resolve => setTimeout(resolve, 100));
    }
  }
  
  return resultados;
}

// Função para fazer requisição com retry automático
// Se uma requisição falhar, tenta novamente algumas vezes antes de desistir
async function fetchComRetry(url, maxTentativas = 2, delay = 1000) {
  for (let tentativa = 1; tentativa <= maxTentativas; tentativa++) {
    try {
      console.log(`Tentativa ${tentativa}/${maxTentativas} para: ${url}`);
      const response = await fetch(url);
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }
      
      const data = await response.json();
      
      // Verificar se há erro na resposta
      if (data.error) {
        throw new Error(data.error);
      }
      
      return data;
    } catch (err) {
      console.warn(`Tentativa ${tentativa} falhou:`, err.message);
      
      if (tentativa === maxTentativas) {
        throw err;
      }
      
      // Aguardar antes da próxima tentativa
      await new Promise(resolve => setTimeout(resolve, delay * tentativa));
    }
  }
}

// Buscar obras de arte na API do Met Museum
// Retorna lista de obras de arte que correspondem ao termo de busca
async function buscarArte(termoBusca) {
  try {
    const searchUrl = `php/api-endpoints.php?action=search_art&query=${encodeURIComponent(termoBusca)}`;
    console.log('Buscando arte:', searchUrl);
    
    // Tentar buscar com retry
    const searchData = await fetchComRetry(searchUrl, 2, 1000);
    console.log('Resposta da API de arte (pesquisa):', searchData);
    
    if (searchData.total === 0 || !searchData.objectIDs) {
      return [];
    }
    
    // Limitar a 10 objetos para evitar sobrecarga
    const objectIds = searchData.objectIDs.slice(0, 10);
    
    // Função para buscar detalhes de um objeto com cache e retry
    const buscarDetalhesObjeto = async (id) => {
      const chaveCache = `arte_${id}`;
      const agora = Date.now();
      
      // Verificar cache
      if (cacheArte.has(chaveCache)) {
        const { dados, timestamp } = cacheArte.get(chaveCache);
        if (agora - timestamp < CACHE_DURATION) {
          console.log(`Usando cache para obra ${id}`);
          return dados;
        } else {
          cacheArte.delete(chaveCache);
        }
      }
      
      try {
        const url = `php/api-endpoints.php?action=art_details&id=${id}`;
        const data = await fetchComRetry(url, 1, 500); // Apenas 1 retry para detalhes
        
        // Armazenar no cache
        cacheArte.set(chaveCache, {
          dados: data,
          timestamp: agora
        });
        
        return data;
      } catch (err) {
        console.warn(`Erro ao buscar detalhes da obra ${id}:`, err.message);
        return null;
      }
    };
    
    // Processar em lotes de 2 para evitar sobrecarga
    const objetos = await processarEmLotes(objectIds, 2, buscarDetalhesObjeto);
    const objetosValidos = objetos.filter(objeto => objeto !== null);
    
    console.log('Resposta da API de arte (objetos válidos):', objetosValidos.length);
    
    // Se não conseguiu nenhum objeto válido, mostrar mensagem informativa
    if (objetosValidos.length === 0) {
      console.warn('Nenhuma obra de arte válida encontrada - API pode estar instável');
      return [{
        id: 'arte_indisponivel',
        tipo: 'arte',
        titulo: 'API de Arte Temporariamente Indisponível',
        ano: 'N/A',
        imagem: 'https://placehold.co/92x138/666666/FFFFFF?text=API+Indisponível',
        autor: 'Serviço temporariamente fora do ar',
        apiId: 'arte_indisponivel'
      }];
    }
    
    return objetosValidos.map(objeto => ({
      id: objeto.objectID,
      tipo: 'arte',
      titulo: objeto.title || 'Sem título',
      ano: objeto.objectDate || 'Data desconhecida',
      imagem: objeto.primaryImageSmall || 'https://placehold.co/92x138/333333/FFFFFF?text=Sem+Imagem',
      autor: objeto.artistDisplayName || 'Artista desconhecido',
      apiId: objeto.objectID
    }));
  } catch (err) {
    console.error('Erro ao buscar arte:', err);
    
    // Retornar mensagem informativa em caso de erro
    return [{
      id: 'arte_erro',
      tipo: 'arte',
      titulo: 'Erro na Busca de Arte',
      ano: 'N/A',
      imagem: 'https://placehold.co/92x138/666666/FFFFFF?text=Erro+na+Busca',
      autor: 'Tente novamente em alguns minutos',
      apiId: 'arte_erro'
    }];
  }
}

// Buscar músicas na API do Spotify
// Retorna lista de músicas que correspondem ao termo de busca
async function buscarMusicas(termoBusca) {
  try {
    const url = `${API_ENDPOINTS.tracks}&query=${encodeURIComponent(termoBusca)}`;
    console.log('Buscando músicas:', url);
    const response = await fetch(url);
    const data = await response.json();
    console.log('Resposta da API de músicas:', data);
    if (data.tracks && data.tracks.items && data.tracks.items.length > 0) {
      return data.tracks.items.map(track => ({
        id: track.id,
        tipo: 'musica',
        titulo: track.name,
        ano: track.album?.release_date ? track.album.release_date.split('-')[0] : 'Desconhecido',
        imagem: track.album?.images?.[0]?.url || 'https://placehold.co/92x138/333333/FFFFFF?text=Sem+Capa',
        autor: track.artists.map(artist => artist.name).join(', '),
        album: track.album?.name || 'Álbum desconhecido',
        duracao: formatarDuracao(track.duration_ms),
        popularidade: track.popularity,
        amostra: track.preview_url,
        apiId: track.id
      }));
    }
    return [];
  } catch (err) {
    console.error('Erro ao buscar músicas:', err);
    return [];
  }
}

// Formata duração em mm:ss
// Converte milissegundos para formato de tempo legível
function formatarDuracao(ms) {
  const segundos = Math.floor(ms / 1000);
  const minutos = Math.floor(segundos / 60);
  const segundosRestantes = segundos % 60;
  return `${minutos}:${segundosRestantes.toString().padStart(2, '0')}`;
}

// Função para limpar tags HTML das descrições
// Remove tags HTML e converte entidades HTML para texto legível
function limparDescricaoHTML(htmlString) {
  if (!htmlString) return 'Sem descrição disponível';
  
  // Criar um elemento temporário para processar o HTML
  const tempDiv = document.createElement('div');
  tempDiv.innerHTML = htmlString;
  
  // Obter o texto puro sem tags HTML
  let textoLimpo = tempDiv.textContent || tempDiv.innerText || '';
  
  // Limpar espaços extras e quebras de linha desnecessárias
  textoLimpo = textoLimpo.replace(/\s+/g, ' ').trim();
  
  // Limitar o tamanho da descrição para evitar textos muito longos
  if (textoLimpo.length > 500) {
    textoLimpo = textoLimpo.substring(0, 500) + '...';
  }
  
  return textoLimpo;
}

// Obter detalhes de uma obra específica
// Busca informações completas de uma obra (filme, série, livro, etc.)
async function obterDetalhesObra({ apiId, tipo }) {
  try {
    
    console.log('Obtendo detalhes para:', tipo, apiId);
    
    let url;
    let data;
    
    switch (tipo) {
      case 'filme':
        url = `${API_ENDPOINTS.movieDetails}&id=${apiId}`;
        break;
      case 'serie':
        url = `${API_ENDPOINTS.tvDetails}&id=${apiId}`;
        break;
      case 'musica':
        url = `${API_ENDPOINTS.trackDetails}&id=${apiId}`;
        break;
      case 'livro':
        // Para livros, usamos a API do Google Books diretamente
        url = `https://www.googleapis.com/books/v1/volumes/${apiId}`;
        break;
      case 'arte':
        // Para arte, usamos o endpoint do backend
        url = `php/api-endpoints.php?action=art_details&id=${apiId}`;
        break;
      default:
        throw new Error('Tipo de obra não suportado');
    }
    
    console.log('URL para obter detalhes:', url);
    
    try {
      const response = await fetch(url);
      console.log('Status da resposta:', response.status);
      
      if (!response.ok) {
        throw new Error(`Erro HTTP: ${response.status} - ${response.statusText}`);
      }
      
      const responseText = await response.text();
      console.log('Resposta bruta:', responseText.substring(0, 100) + '...');
      
      try {
        data = JSON.parse(responseText);
      } catch (parseError) {
        console.error('Erro ao analisar JSON:', parseError);
        throw new Error('Resposta inválida do servidor');
      }
      
      console.log('Dados recebidos para detalhes:', data);
    } catch (fetchError) {
      console.error('Erro na requisição fetch:', fetchError);
      throw fetchError;
    }
    
    if (data.error) {
      console.error('Erro retornado pela API:', data.error);
      throw new Error(data.error);
    }
    
    switch (tipo) {
      case 'filme':
        return {
          id: data.id,
          tipo: 'filme',
          subtipo: data.genres?.some(g => g.id === 99) ? 'documentário' : 'filme',
          titulo: data.title,
          ano: data.release_date ? data.release_date.split('-')[0] : 'Desconhecido',
          imagem: data.poster_path 
            ? `https://image.tmdb.org/t/p/w500${data.poster_path}`
            : 'https://placehold.co/500x750/333333/FFFFFF?text=Sem+Imagem',
          descricao: data.overview,
          genero: data.genres?.map(g => g.name).join(', '),
          apiId: data.id
        };
      case 'serie':
        return {
          id: data.id,
          tipo: 'serie',
          subtipo: data.genres?.some(g => g.id === 99) ? 'documentário' : 'série',
          titulo: data.name,
          ano: data.first_air_date ? data.first_air_date.split('-')[0] : 'Desconhecido',
          imagem: data.poster_path 
            ? `https://image.tmdb.org/t/p/w500${data.poster_path}`
            : 'https://placehold.co/500x750/333333/FFFFFF?text=Sem+Imagem',
          descricao: data.overview,
          criador: data.created_by?.map(c => c.name).join(', '),
          temporadas: data.number_of_seasons,
          apiId: data.id
        };
      case 'musica':
        return {
          id: data.id,
          tipo: 'musica',
          titulo: data.name,
          ano: data.album?.release_date ? data.album.release_date.split('-')[0] : 'Desconhecido',
          imagem: data.album?.images?.[0]?.url || 'https://placehold.co/500x500/333333/FFFFFF?text=Sem+Capa',
          autor: data.artists.map(artist => artist.name).join(', '),
          album: data.album?.name || 'Álbum desconhecido',
          duracao: formatarDuracao(data.duration_ms),
          popularidade: data.popularity,
          amostra: data.preview_url,
          genero: data.album?.genres ? data.album.genres.join(', ') : 'Gênero desconhecido',
          apiId: data.id
        };
      case 'livro':
        const volumeInfo = data.volumeInfo || {};
        return {
          id: data.id,
          tipo: 'livro',
          titulo: volumeInfo.title || 'Título desconhecido',
          autor: volumeInfo.authors ? volumeInfo.authors.join(', ') : 'Autor desconhecido',
          ano: volumeInfo.publishedDate ? volumeInfo.publishedDate.split('-')[0] : 'Desconhecido',
          imagem: (() => {
            const img = volumeInfo.imageLinks || {};
            const prioridades = ['large', 'medium', 'thumbnail', 'smallThumbnail'];
          
            for (const chave of prioridades) {
              let link = img[chave];
              if (typeof link === 'string' && link.startsWith('http')) {
                return `proxy.php?url=${encodeURIComponent(link)}`;
              }
            }
          
            return 'https://placehold.co/92x138/333333/FFFFFF?text=Sem+Capa';
          })(),          
          descricao: limparDescricaoHTML(volumeInfo.description),
          genero: volumeInfo.categories ? volumeInfo.categories.join(', ') : 'Categoria desconhecida',
          apiId: data.id
        };
      case 'arte':
        return {
          id: data.objectID,
          tipo: 'arte',
          titulo: data.title || 'Sem título',
          ano: data.objectDate || 'Data desconhecida',
          imagem: data.primaryImage || data.primaryImageSmall || 'https://placehold.co/500x750/333333/FFFFFF?text=Sem+Imagem',
          autor: data.artistDisplayName || 'Artista desconhecido',
          descricao: data.objectDescription || data.objectName || 'Sem descrição disponível',
          genero: data.classification || data.department || 'Classificação desconhecida',
          apiId: data.objectID
        };
      default:
        throw new Error('Tipo de obra não suportado');
    }
  } catch (err) {
    console.error('Erro ao obter detalhes da obra:', err);
    throw err;
  }
}

// Busca principal - função que coordena todas as buscas
// Recebe um termo de busca e tipos de obra, retorna resultados de todas as APIs
async function buscarObras(termoBusca, tipos) {
  try {
    const resultados = [];
    const promessas = tipos.map(tipo => {
      switch (tipo) {
        case 'filme':
          return buscarFilmes(termoBusca);
        case 'serie':
          return buscarSeries(termoBusca);
        case 'livro':
          return buscarLivros(termoBusca);
        case 'arte':
          return buscarArte(termoBusca);
        case 'musica':
          return buscarMusicas(termoBusca);
        default:
          return Promise.resolve([]);
      }
    });
    const resultadosPorTipo = await Promise.all(promessas);
    resultadosPorTipo.forEach(resultado => {
      resultados.push(...resultado);
    });
    
    // Ordenar resultados por relevância do título
    const termoBuscaLowerCase = termoBusca.toLowerCase();
    resultados.sort((a, b) => {
      // Verificar correspondência exata no início do título
      const tituloA = a.titulo.toLowerCase();
      const tituloB = b.titulo.toLowerCase();
      
      const aStartsWithTerm = tituloA.startsWith(termoBuscaLowerCase);
      const bStartsWithTerm = tituloB.startsWith(termoBuscaLowerCase);
      
      if (aStartsWithTerm && !bStartsWithTerm) return -1;
      if (!aStartsWithTerm && bStartsWithTerm) return 1;
      
      // Verificar se o termo está contido no título
      const aContainsTerm = tituloA.includes(termoBuscaLowerCase);
      const bContainsTerm = tituloB.includes(termoBuscaLowerCase);
      
      if (aContainsTerm && !bContainsTerm) return -1;
      if (!aContainsTerm && bContainsTerm) return 1;
      
      // Usar distância de Levenshtein para similaridade
      const distA = levenshteinDistance(tituloA, termoBuscaLowerCase);
      const distB = levenshteinDistance(tituloB, termoBuscaLowerCase);
      
      return distA - distB; // Menor distância = maior relevância
    });
    
    return {
      resultados: resultados
    };
  } catch (err) {
    console.error('Erro na busca de obras:', err);
    return {
      mensagem: 'Erro ao buscar obras. Por favor, tente novamente.'
    };
  }
} 