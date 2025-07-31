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
                        <li><a href="#">Meus Jogos</a></li>
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
