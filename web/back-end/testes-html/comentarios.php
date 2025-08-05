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
<script>

const idPostagem = document.querySelector('id_postagem');
const curtida = document.querySelectorAll('.like');
const descurtida = document.querySelectorAll('.dislike');
let like = false;
let dislike = false;
let desfLike = false;
let desfDislike = false;

curtida.forEach(botao => {
    botao.addEventListener('click', function(){
        postagem = this.closest('.postagem');
        id = postagem.dataset.id;

        definirLike(this);
        envLike(id);

    });
});

descurtida.forEach(botao => {
    botao.addEventListener('click', function(){
        postagem = this.closest('.postagem');
        id = postagem.dataset.id;

        definirDeslike(this);
        envLike(id);
       
    });
});

function definirLike(botao) {
    like = !like;
    if (like) {
        dislike = false;
        desfLike = false;
        desfDislike = true;
        atualizarBotoes();
    } else {
        botao.innerHTML = 'curtir';
        desfLike = true;
    }
    console.log('like:', like, 'dislike:', dislike);
}

function definirDeslike(botao) {
    dislike = !dislike;
    if (dislike) {
        like = false;
        desfDislike = false;
        desfLike = true;
        atualizarBotoes();
    } else {
        botao.innerHTML = 'descurtir';
        desfDislike = true;
    }
    console.log('like:', like, 'dislike:', dislike);
}

function atualizarBotoes() {
    curtida.forEach(botao => {
        botao.innerHTML = like ? 'curtido' : 'curtir';
    });
    descurtida.forEach(botao => {
        botao.innerHTML = dislike ? 'descurtido' : 'descurtir';
    });
}

async function envLike(id){

    let response;

    if(like == true || dislike == true){
        response = await fetch('../php/reagir.php',{
            method: 'POST',
            headers:{
                'content-type': 'application/json',
            },
            body: JSON.stringify({like: like, dislike: dislike, id: id})
        });
    } else if(desfLike == true || desfDislike == true){
        response = await fetch('../php/reagir.php',{
            method: 'POST',
            headers:{
                'content-type': 'application/json'
            },
            body: JSON.stringify({DesfazerLike: desfLike, DesfazerDislike: desfDislike, id: id})
        });
    }

    like = false;
    dislike = false;
    desfLike = false;
    desfDislike = false;

    if(response){
        if(response.ok){
                alert("deu certo!!");
                window.location.href = '../php/reagir.php';
        }else{
            alert("erro na requisição:" + response.status);
        }

    } else {
        alert("nenhum fecth realizado!");
    }
    return;
}



</script>
</html>