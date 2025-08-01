<?php
include_once('../PhP/session.php');

function renderHeader($currentPage = '') {
    $isLoggedIn = isLoggedIn();
    $userData = getUserData();
?>
<?php if ($isLoggedIn == null) { ?>
    <header>
        <div class="container">
            <div class="header-top">
                <h1>🎮 Game Shelf</h1>
            </div>
            <div class="header-bottom">
                <nav>
                    <ul>
                        <li><a href="index.php">Início</a></li>
                        <li><a href="login.php">Meus Jogos</a></li>
                        <li><a href="games.php">Explorar</a></li>
                        <li><a href="login.php">Amigos</a></li>
                    </ul>
                </nav>
                <div class="search-bar" style="position:relative;">
                    <span class="search-icon">&#128269;</span> 
                    <input type="search" placeholder="Pesquisar jogos, pessoas..." id="main-search" oninput="rendersearch(this.value)">
                    <div class="search-modal-results" id="modaldebusca" style="display:none;"></div>
                </div>
                <div class="auth-buttons">
                  <a href="login.php"><button class="login-btn">Login</button></a>
                    <a href="register.php"><button class="register-btn">Registrar</button></a>
                </div>
            </div>
        </div>
    </header>
<?php } if ($isLoggedIn) { ?>
    <header>
        <div class="container">
            <div class="header-top">
                <h1>🎮 Game Shelf</h1>
            </div>
            <div class="header-bottom">
                <nav>
                    <ul>
                        <li><a href="index.php">Início</a></li>
                        <li><a href="gamelist.php">Meus Jogos</a></li>
                        <li><a href="games.php">Explorar</a></li>
                        <li><a href="#">Amigos</a></li>
                    </ul>
                </nav>
                <div class="search-bar" style="position:relative;">
                    <span class="search-icon">&#128269;</span> 
                    <input type="search" placeholder="Pesquisar jogos, pessoas..." id="main-search" oninput="rendersearch(this.value)">
                    <div class="search-modal-results" id="modaldebusca" style="display:none;"></div>
                </div>
                <div class="auth-buttons">
                <p>Olá, <?php echo $_SESSION["username"] ?> </p>
                <a href="../PhP/logout.php"><button class="login-btn">Logout</button></a>
                </div>
            </div>
        </div>
    </header>
<?php } ?>


<?php
}
?>
<script>
    function rendersearch(searchtext) {
    const busca = document.getElementById('modaldebusca');
    busca.style.display = searchtext.length > 0 ? 'block' : 'none';
    busca.innerHTML = '';

    if (searchtext.length === 0) return;

    fetch('../apis/search.php?term=' + encodeURIComponent(searchtext))
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                busca.innerHTML = '<p class="search-result-empty">Nenhum resultado encontrado.</p>';
            } else {
                data.slice(0, 5).forEach(item => {
                    let img = item.type === 'game' && item.game_cover_url

                        
                        ? `<img src="${item.game_cover_url}" alt="Capa do Jogo" class="search-result-img">`
                        : `<span class="search-result-img">👤</span>`;
                    busca.innerHTML +=`

                    <a href="${item.type === 'game' ? 'Gamepage.php?id=' + item.id : 'gamelist.php?username=' + item.name}">
                        <div class="search-result-item">
                            ${img}
                            <div class="search-result-info">
                                <span class="search-result-name">${item.name}</span>
                                <span class="search-result-type">${item.type === 'game' ? 'Jogo' : 'Usuário'}</span>
                            </div>
                        </div>

                    </a>
                    `;
                });
            }
        });
}



// Fecha o modal ao clicar fora ou pressionar ESC
document.addEventListener('click', function(e) {
    const busca = document.getElementById('modaldebusca');
    const searchBar = document.getElementById('main-search');
    if (busca.style.display === 'block' && !busca.contains(e.target) && e.target !== searchBar) {
        busca.style.display = 'none';
    }
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('modaldebusca').style.display = 'none';
    }
});
</script>
