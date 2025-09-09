<?php

require "../php/telainicial_post.php";
$posts = postagensFeitas();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <?php foreach($posts as $post): ?>
        <h3><?= $post['titulo'] ?></h3>
        <div class="postagem" data-id=<?= $post['id'] ?>>
            <p id="postagem_txt"><?= $post['texto'] ?></p>
            <button class="like">curtir</button><br><br>
            <button class="dislike">descurtir</button>
            <button id="comment">comentar</button>
        </div>
    <?php endforeach; ?>
    
</body>
<script src="../js/criarID.js"></script>
<script>

const curtida = document.querySelectorAll('.like');
const descurtida = document.querySelectorAll('.dislike');

const estadosPosts = {}; // mapa id => estado

curtida.forEach(botao => {
  botao.addEventListener('click', () => {
    const post = botao.closest('.postagem');
    const id = post.dataset.id;
    const estadoAtual = estadosPosts[id] || null;

    if (estadoAtual === 'like') {
      estadosPosts[id] = null; // desfazer like
    } else {
      estadosPosts[id] = 'like'; // curtir
    }

    atualizarBotoesPost(post, estadosPosts[id]);
    enviarReacao(id, estadosPosts[id]);
  });
});

descurtida.forEach(botao => {
  botao.addEventListener('click', () => {
    const post = botao.closest('.postagem');
    const id = post.dataset.id;
    const estadoAtual = estadosPosts[id] || null;

    if (estadoAtual === 'dislike') {
      estadosPosts[id] = null; // desfazer dislike
    } else {
      estadosPosts[id] = 'dislike'; // descurtir
    }

    atualizarBotoesPost(post, estadosPosts[id]);
    enviarReacao(id, estadosPosts[id]);
  });
});

function atualizarBotoesPost(post, estado) {
  const likeBtn = post.querySelector('.like');
  const dislikeBtn = post.querySelector('.dislike');

  likeBtn.innerText = estado === 'like' ? 'curtido' : 'curtir';
  dislikeBtn.innerText = estado === 'dislike' ? 'descurtido' : 'descurtir';
}

async function enviarReacao(id, estado) {
  let like = false, dislike = false;

  if (estado === 'like') like = true;
  else if (estado === 'dislike') dislike = true;

  const response = await fetch('../php/reagir.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, like, dislike})
  });

  data = await response.json();

  if (!response.ok) {
    alert('Erro na requisição: ' + response.status);
  } else{
    console.log('Resposta do servidor (reacao):', data);

    criarID(14, '../php/reagir.php');
  }
}



</script>
</html>