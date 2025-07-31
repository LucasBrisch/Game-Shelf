<?php
include('../database/connection.php');
header('Content-Type: application/json');

$term = isset($_GET['term']) ? $_GET['term'] : '';

if (empty($term)) {
    echo json_encode([]);
    exit;
}

$likeTerm = "%" . $term . "%";

// Adicionado id ao select e usando prepared statements
$sql = "
    (SELECT id, title AS name, 'game' AS type, game_cover_url FROM games WHERE title LIKE ?)
    UNION
    (SELECT id, username AS name, 'user' AS type, NULL AS game_cover_url FROM users WHERE username LIKE ?)
    LIMIT 10
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $likeTerm, $likeTerm);
$stmt->execute();
$result = $stmt->get_result();

$searchRequests = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $searchRequests[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'type' => $row['type'],
            'game_cover_url' => $row['game_cover_url']
        ];
    }
}

echo json_encode($searchRequests);
$conn->close();
?>