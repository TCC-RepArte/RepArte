const options = {
    method: 'GET',
    headers: {
      accept: 'application/json',
      Authorization: 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJkNGY1NWJmYmRkYWU5MTRlMTI4NDE1YjczOTVhNzQ3NSIsIm5iZiI6MTc0ODAwMjMzOC4yNDgsInN1YiI6IjY4MzA2NjIyM2E3ZjBiNTc4MTgzNmY3NyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.QTXRWLYChv0Kly7kwVjvAKWxiuYGOW5hA6m9JHfIKHI'
    }
};

async function desc() {

    // Puxa texto do input 'inp_ser'
    const procFilme = document.getElementById('inp_ser').value;

    // Moldando url para busca do filme
    const baseUrl = 'https://api.themoviedb.org/3/search/movie';
    const url = `${baseUrl}?include_adult=true&language=en-US&page=1&query=${encodeURIComponent(procFilme)}`;

    try {

        // Armazenando resposta, em JSON, na constante data
        const response = await fetch(url, options);
        const data = await response.json();
        
        if (data.results && data.results.length > 0) {
            // Encontrar o filme com o título mais similar
            const maisParecido = data.results.reduce((parecido, corresponde) => {
                const correspondeTitle = corresponde.title.toLowerCase();
                const procTitle = procFilme.toLowerCase();
                
                // Calcular similaridade usando distância de Levenshtein
                const similaridade = levenshteinDistance(correspondeTitle, procTitle);
                
                if (!parecido || similaridade < parecido.similaridade) {
                    return { movie: corresponde, similaridade };
                }
                return parecido;
            }, null);

            // Segunda requisição para garantir dados completos em pt-BR
            const filmeId = maisParecido.movie.id;
            const filmeUrl = `https://api.themoviedb.org/3/movie/${filmeId}?language=pt-BR`;

            const filmeDetalhesResp = await fetch(filmeUrl, options);
            const filmeDetalhes = await filmeDetalhesResp.json();

            const nomefilme = document.querySelector('.nomefilme');
            const descrifilme = document.querySelector('.descrifilme');

            nomefilme.innerHTML = filmeDetalhes.title || maisParecido.movie.title;
            descrifilme.innerHTML = filmeDetalhes.overview || maisParecido.movie.overview || 'Descrição não disponível';

            // Puxando poster do filme:

            const urlfilmePoster = `https://api.themoviedb.org/3/movie/${filmeId}/images?include_image_language=pt%2Cnull&language=pt-BR`;

            const responseImg = await fetch(urlfilmePoster, options);
            const dataImg = await responseImg.json();
            const caminho_poster = dataImg.posters[1].file_path;      
            const imgFilme = document.querySelector('#imgFilmeSel');
            imgFilme.setAttribute("src", `https://image.tmdb.org/t/p/original/${caminho_poster}`);
            imgFilme.setAttribute("alt", caminho_poster);

        } else {
            console.log('Nenhum filme encontrado');
            document.querySelector('.nomefilme').innerHTML = 'Nenhum filme encontrado';
            document.querySelector('.descrifilme').innerHTML = '';
        }
    } catch (err) {
        console.error('Erro:', err);
        document.querySelector('.nomefilme').innerHTML = 'Erro ao buscar filme';
        document.querySelector('.descrifilme').innerHTML = '';
    }
}

// Função para calcular a distância de Levenshtein (similaridade entre strings)
function levenshteinDistance(a, b) {
    // Se uma das strings estiver vazia, a distância será o tamanho da outra string
    if (a.length === 0) return b.length;
    if (b.length === 0) return a.length;

    const matriz = [];

    // Inicializa a primeira coluna da matriz com valores de 0 até o tamanho de b
    for (let i = 0; i <= b.length; i++) {
        matriz[i] = [i];
    }

    // Inicializa a primeira linha da matriz com valores de 0 até o tamanho de a
    for (let j = 0; j <= a.length; j++) {
        matriz[0][j] = j;
    }

    // Preenche a matriz calculando a distância entre as substrings
    for (let i = 1; i <= b.length; i++) {
        for (let j = 1; j <= a.length; j++) {
            // Se os caracteres são iguais, mantém o valor da diagonal
            if (b.charAt(i - 1) === a.charAt(j - 1)) {
                matriz[i][j] = matriz[i - 1][j - 1];
            } else {
                // Calcula o menor número de operações necessárias:
                matriz[i][j] = Math.min(
                    matriz[i - 1][j - 1] + 1, // substituição
                    matriz[i][j - 1] + 1,     // inserção
                    matriz[i - 1][j] + 1      // deleção
                );
            }
        }
    }

    // Retorna o valor final que representa o número mínimo de operações
    // necessárias para transformar a string 'a' na string 'b'
    return matriz[b.length][a.length];
}