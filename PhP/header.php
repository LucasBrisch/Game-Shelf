<?php
include_once('../PhP/session.php');

function renderHeader($currentPage = '') {
    $isLoggedIn = isLoggedIn();
    $userData = getUserData();
?>
    <header>
        <div class="container">
            <div class="header-top">
                <h1><a href="index.php" style="text-decoration: none; color: inherit;">🎮 Game Shelf</a></h1>
            </div>
            <div class="header-bottom">
                <nav>
                    <ul>
                        <li><a href="index.php" <?php echo $currentPage == 'index' ? 'class="active"' : ''; ?>>Início</a></li>
                        <?php if ($isLoggedIn): ?>
                            <li><a href="my-games.php" <?php echo $currentPage == 'my-games' ? 'class="active"' : ''; ?>>Meus Jogos</a></li>
                        <?php endif; ?>
                        <li><a href="games.php" <?php echo $currentPage == 'games' ? 'class="active"' : ''; ?>>Explorar</a></li>
                        <?php if ($isLoggedIn): ?>
                            <li><a href="community.php" <?php echo $currentPage == 'community' ? 'class="active"' : ''; ?>>Comunidade</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <div class="search-bar" style="position:relative;">
                    <span class="search-icon">&#128269;</span> 
                    <input type="search" placeholder="Pesquisar jogos, pessoas..." id="main-search" oninput="rendersearch(this.value)">
                    <div class="search-modal-results" id="modaldebusca" style="display:none;"></div>
                </div>
                <div class="auth-buttons">
                    <?php if ($isLoggedIn): ?>
                        <div class="user-menu">
                            <span class="user-greeting">Olá, <?php echo htmlspecialchars($userData['username']); ?>!</span>
                            <form method="POST" action="../PhP/auth.php" style="display: inline;">
                                <button type="submit" name="logout" class="logout-btn">Sair</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <button class="login-btn" onclick="window.location.href='login.php'">Login</button>
                        <button class="register-btn" onclick="window.location.href='register.php'">Registrar</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
<?php
}
?>
