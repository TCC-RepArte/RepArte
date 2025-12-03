<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../back-end/js/preloginmenu.js" defer></script>
    <link rel="stylesheet" href="../front-end/prelogin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Hammersmith+One&display=swap" rel="stylesheet">
    <title>RepArte</title>
</head>
<body>
    <div id="bg-fixo"></div>
    <header>
        <div class="interface">
            <div class="logo">
                <a href="#">
                    <img src="../imagens/logo.png" alt="Logo do site">
                </a>
            </div>
            <nav class="menu">
                <ul>
                    <li><a href="#sobre-nos">Sobre Nós</a></li>
                    <li><a href="#cont">Contato</a></li>
                    <li><a href="#">Mobile</a></li>
                </ul>
            </nav>
            <div class="btn-Login">
                <a href="login1.php" class="btn-login-destaque">
                    <button type="button">Login</button>
                </a>
                <a href="cadastro.php" class="btn-cadastro-simples">
                    <button type="button">Cadastro</button>
                </a>
            </div>
        </div>
    </header>

    <main>
        <section class="topo-do-site">
            <div class="interface">
                <div class="flex">
                    <div class="txt-topo-site">
                        <h1>O <span class="destaque">melhor</span> amigo <br>dos seus estudos!</h1>
                        <p>Nossa campanha tem como principal intuito oferecer <span class="destaque">apoio</span> gratuito para estudantes e redatores que buscam aprimorar suas <span class="destaque">habilidades</span> de escrita e argumentação</p>
                        <div class="search-box search-box-custom">
                            <span class="search-icon">🔍</span>
                            <input type="text" class="search-text" placeholder="Pesquisar tema...">
                        </div>
                    </div>
                    <div class="carrossel-obra">
                        <button class="seta seta-esquerda">&#60;</button>
                        <div class="img-obra-container">
                            <img src="../imagens/vantagens.png" alt="As vantagens de ser invisível" class="img-obra">
                            <span class="tag-flutuante tag1">Sexualidade e Homofobia</span>
                            <span class="tag-flutuante tag2">Relações disfuncionais</span>
                            <span class="tag-flutuante tag3">Saúde Mental</span>
                        </div>
                        <button class="seta seta-direita">&#62;</button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Seção Sobre Nós com ID -->
    <section class="sobre-nos" id="sobre-nos">
        <div class="interface">
            <h2>Sobre nós</h2>
            <p>
                Nosso projeto nasceu da vontade de tornar o acesso ao conhecimento mais <b>democrático</b> e acessível para todos os estudantes que estão se preparando para os vestibulares no Brasil. Sabemos o quanto a construção de um bom repertório sociocultural é essencial para uma boa redação e desempenho geral nas provas, e é por isso que criamos este <b>espaço gratuito e colaborativo</b>.<br><br>
                Este site é parte do nosso <b>Trabalho de Conclusão de Curso (TCC)</b>, fruto de muita pesquisa, dedicação e debates sobre os desafios do ensino no país e as barreiras enfrentadas por tantos estudantes. Nosso objetivo é que este projeto continue crescendo e ajudando cada vez mais pessoas, <b>promovendo o acesso ao conhecimento</b> de forma aberta e transformadora.
            </p>
        </div>
    </section>

    <br><br><br>

    <section class="cont" id="cont"></section>
    <footer>
        <div class="interface">
            <div class="line-footer">
                <div class="flex">
                    <div class="logo-footer">
                        <img src="../imagens/logo.png" alt="">
                    </div>
                    <div class="btn-social">
                        <a href="https://x.com/RepArtetcc?t=_xabIrCKfrAsyM2Fn5QemA&s=09"><button><i class="bi bi-twitter-x"></i></button></a>
                        <a href="https://www.instagram.com/reparte_3y?igsh=MTF1aWNid3kyMWVi"><button><i class="bi bi-instagram"></i></button></a>
                    </div>
                </div>
            </div>

            <div class="line-footer borda">
                <p><a href="https://g.co/kgs/KGJ2CLJ">Av. Pereira Barreto, 400 - Baeta Neves, São Bernardo do Campo - SP, 09751-000</a></p>
                <p><a href="tel:+5511968464237">+55 (11) 96846-4237</a></p>
            </div>
        </div>
    </footer>

    <div class="btn-abrir-menu" id="btn-menu">
        <i class="bi bi-list"></i>
    </div>
    <div class="menu-mobile" id="menu-mobile">
        <div class="btn-fechar">
            <i class="bi bi-x"></i>
        </div>
           <nav>
                <ul>
                    <li><a href="#sobre-nos">Sobre Nós</a></li>
                    <li><a href="#cont">Contato</a></li>
                    <li><a href="#">Mobile</a></li>
                    <li><a href="login1.php">Login</a></li>
                    <li><a href="cadastro.php">Cadastro</a></li>
                </ul>
            </nav>
    </div>
    <div class="overlay-menu" id="overlay-menu">

    </div>
</body>
</html>
