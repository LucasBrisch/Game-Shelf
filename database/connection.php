<?php
    $host = 'localhost:3306'; 
    $user = 'root'; 
    $password = ''; 
    $database = 'gameshelf'; 

    $conn = new mysqli($host, $user, $password, $database);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
?>
