let indexAtual = <?php echo $indexAtual; ?>;
function passarPagina(direcao) {
    indexAtual += direcao;
    if (indexAtual < 0) indexAtual = 0;
    window.location.href = `games.php?offset=${indexAtual}`;
}