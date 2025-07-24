<?php
include('../database/connection.php');

// Recebe o termo da busca
$term = isset($_GET['term']) ? $_GET['term'] : '';

// Escapa o termo para evitar SQL Injection
$term = $conn->real_escape_string($term);

// Monta o SQL filtrando pelo termo
$sql = "
    SELECT title AS name, 'game' AS type, game_cover_url 
    FROM games 
    WHERE title LIKE '%$term%'
    UNION
    SELECT username AS name, 'user' AS type, NULL AS game_cover_url 
    FROM users 
    WHERE username LIKE '%$term%'
";

$result = $conn->query($sql);

$searchRequests = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $searchRequests[] = [
            'name' => $row['name'],
            'type' => $row['type'],
            'game_cover_url' => $row['game_cover_url']
        ];
    }
}

// Retorna JSON
header('Content-Type: application/json');
echo json_encode($searchRequests);
?>