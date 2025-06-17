// Endpoints das APIs
const API_ENDPOINTS = {
  movies: '/back-end/php/api-endpoints.php?action=search_movies',
  tv: '/back-end/php/api-endpoints.php?action=search_tv',
  tracks: '/back-end/php/api-endpoints.php?action=search_tracks',
  movieDetails: '/back-end/php/api-endpoints.php?action=movie_details',
  tvDetails: '/back-end/php/api-endpoints.php?action=tv_details',
  trackDetails: '/back-end/php/api-endpoints.php?action=track_details'
};

// Buscar detalhes de um filme
async function buscarDetalhesFilme(filmeId) {
  try {
    const url = `${API_ENDPOINTS.movieDetails}&id=${filmeId}`;
    const response = await fetch(url);
    const filme = await response.json();
    if (!filme || !filme.id) {
      throw new Error('Filme não encontrado');
    }
    return {
      id: filme.id,
      tipo: 'filme',
      subtipo: filme.genres?.some(g => g.id === 99) ? 'documentário' : 'filme',
      titulo: filme.title,
      descricao: filme.overview || 'Descrição não disponível',
      ano: filme.release_date ? filme.release_date.split('-')[0] : '',
      imagem: filme.poster_path 
        ? `https://image.tmdb.org/t/p/w300${filme.poster_path}`
        : 'https://placehold.co/300x450/333333/FFFFFF?text=Sem+Imagem',
      diretor: 'Informação não disponível',
      genero: filme.genres ? filme.genres.map(g => g.name).join(', ') : ''
    };
  } catch (err) {
    console.error('Erro ao obter detalhes do filme:', err);
    throw new Error('Não foi possível carregar os detalhes do filme');
  }
}

const options = {
    method: 'GET',
    headers: {
      accept: 'application/json',
      Authorization: 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJkNGY1NWJmYmRkYWU5MTRlMTI4NDE1YjczOTVhNzQ3NSIsIm5iZiI6MTc0ODAwMjMzOC4yNDgsInN1YiI6IjY4MzA2NjIyM2E3ZjBiNTc4MTgzNmY3NyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.QTXRWLYChv0Kly7kwVjvAKWxiuYGOW5hA6m9JHfIKHI'
    }
};

async function desc() {
    const procFilme = document.getElementById('inp_ser').value;
    const baseUrl = 'https://api.themoviedb.org/3/search/movie';
    const url = `${baseUrl}?include_adult=true&language=en-US&page=1&query=${encodeURIComponent(procFilme)}`;
    try {
        const response = await fetch(url, options);
        const data = await response.json();
        if (data.results && data.results.length > 0) {
            const maisParecido = data.results.reduce((parecido, corresponde) => {
                const correspondeTitle = corresponde.title.toLowerCase();
                const procTitle = procFilme.toLowerCase();
                const similaridade = levenshteinDistance(correspondeTitle, procTitle);
                if (!parecido || similaridade < parecido.similaridade) {
                    return { movie: corresponde, similaridade };
                }
                return parecido;
            }, null);
            const filmeId = maisParecido.movie.id;
            const filmeUrl = `https://api.themoviedb.org/3/movie/${filmeId}?language=pt-BR`;
            const filmeDetalhesResp = await fetch(filmeUrl, options);
            const filmeDetalhes = await filmeDetalhesResp.json();
            const nomefilme = document.querySelector('.nomefilme');
            const descrifilme = document.querySelector('.descrifilme');
            nomefilme.innerHTML = filmeDetalhes.title || maisParecido.movie.title;
            descrifilme.innerHTML = filmeDetalhes.overview || maisParecido.movie.overview || 'Descrição não disponível';
            const urlfilmePoster = `https://api.themoviedb.org/3/movie/${filmeId}/images?include_image_language=pt%2Cnull&language=pt-BR`;
            const responseImg = await fetch(urlfilmePoster, options);
            const dataImg = await responseImg.json();
            const caminho_poster = dataImg.posters[1].file_path;      
            const imgFilme = document.querySelector('#imgFilmeSel');
            imgFilme.setAttribute("src", `https://image.tmdb.org/t/p/original/${caminho_poster}`);
            imgFilme.setAttribute("alt", caminho_poster);
        } else {
            document.querySelector('.nomefilme').innerHTML = 'Nenhum filme encontrado';
            document.querySelector('.descrifilme').innerHTML = '';
        }
    } catch (err) {
        console.error('Erro:', err);
        document.querySelector('.nomefilme').innerHTML = 'Erro ao buscar filme';
        document.querySelector('.descrifilme').innerHTML = '';
    }
}

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