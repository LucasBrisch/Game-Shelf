<?php
include('../database/connection.php');
header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

$id = intval($_GET['id']);

$sql = "
    SELECT g.*, AVG(r.rating) AS average_rating
    FROM games g
    LEFT JOIN ratings r ON g.id = r.rated_game_id
    WHERE g.id = ?
    GROUP BY g.id
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $row['average_rating'] = is_null($row['average_rating']) ? null : round(floatval($row['average_rating']), 2);
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Jogo não encontrado']);
}
$stmt->close();
$conn->close();
