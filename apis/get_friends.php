<?php
include('../database/connection.php');
include('../PhP/session.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Você precisa estar logado.']);
    exit;
}

$current_user_id = $_SESSION['user_id'];

// Get all accepted friends
$stmt = $conn->prepare("
    SELECT 
        u.id, 
        u.username, 
        u.full_name,
        (SELECT COUNT(*) FROM messages 
         WHERE sender_id = u.id AND receiver_id = ? AND is_read = FALSE) as unread_count
    FROM users u
    JOIN friendships f ON (
        (f.requester_id = ? AND f.receiver_id = u.id) OR 
        (f.receiver_id = ? AND f.requester_id = u.id)
    )
    WHERE f.status = 'accepted'
    ORDER BY u.username ASC
");
$stmt->bind_param("iii", $current_user_id, $current_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$friends = [];
while ($row = $result->fetch_assoc()) {
    $friends[] = [
        'id' => (int)$row['id'],
        'username' => $row['username'],
        'full_name' => $row['full_name'],
        'unread_count' => (int)$row['unread_count']
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'friends' => $friends
]);
?>
