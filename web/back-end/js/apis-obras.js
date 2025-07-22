// Endpoints das APIs
const API_ENDPOINTS = {
  movies: '/RepArte/web/back-end/php/api-endpoints.php?action=search_movies',
  tv: '/RepArte/web/back-end/php/api-endpoints.php?action=search_tv',
  tracks: '/RepArte/web/back-end/php/api-endpoints.php?action=search_tracks',
  movieDetails: '/RepArte/web/back-end/php/api-endpoints.php?action=movie_details',
  tvDetails: '/RepArte/web/back-end/php/api-endpoints.php?action=tv_details',
  trackDetails: '/RepArte/web/back-end/php/api-endpoints.php?action=track_details'
};

// Calcula a distância entre duas strings
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

// Buscar filmes
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

// Buscar séries
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

// Buscar livros
async function buscarLivros(termoBusca) {
  try {
    const url = `https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(termoBusca)}&maxResults=20&langRestrict=pt`;
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

// Buscar obras de arte
async function buscarArte(termoBusca) {
  try {
    const searchUrl = `https://collectionapi.metmuseum.org/public/collection/v1/search?q=${encodeURIComponent(termoBusca)}&hasImages=true`;
    console.log('Buscando arte:', searchUrl);
    const searchResponse = await fetch(searchUrl);
    const searchData = await searchResponse.json();
    console.log('Resposta da API de arte (pesquisa):', searchData);
    if (searchData.total === 0 || !searchData.objectIDs) {
      return [];
    }
    const objectIds = searchData.objectIDs.slice(0, 20);
    const objetosPromises = objectIds.map(id => 
      fetch(`https://collectionapi.metmuseum.org/public/collection/v1/objects/${id}`)
        .then(res => res.json())
    );
    const objetos = await Promise.all(objetosPromises);
    console.log('Resposta da API de arte (objetos):', objetos);
    return objetos.map(objeto => ({
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
    return [];
  }
}

// Buscar músicas
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
function formatarDuracao(ms) {
  const segundos = Math.floor(ms / 1000);
  const minutos = Math.floor(segundos / 60);
  const segundosRestantes = segundos % 60;
  return `${minutos}:${segundosRestantes.toString().padStart(2, '0')}`;
}

// Obter detalhes de uma obra
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
        // Para arte, usamos a API do Met Museum diretamente
        url = `https://collectionapi.metmuseum.org/public/collection/v1/objects/${apiId}`;
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
          apiId: data.id
        };
      case 'livro':
        const volumeInfo = data.volumeInfo || {};
        return {
          id: data.id,
          tipo: 'livro',
          titulo: volumeInfo.title || 'Título desconhecido',
          autor: volumeInfo.authors,
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
          descricao: volumeInfo.description || 'Sem descrição disponível',
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
          genero: data.classification || 'Classificação desconhecida',
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

// Busca principal
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