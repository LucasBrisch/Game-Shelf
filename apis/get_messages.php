<?php
include('../database/connection.php');
include('../PhP/session.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Você precisa estar logado.']);
    exit;
}

$friend_id = isset($_GET['friend_id']) ? intval($_GET['friend_id']) : 0;

// Validate input
if ($friend_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID de amigo inválido.']);
    exit;
}

$current_user_id = $_SESSION['user_id'];

// Check if they are friends
$friendship_check = $conn->prepare("
    SELECT id FROM friendships 
    WHERE ((requester_id = ? AND receiver_id = ?) OR (requester_id = ? AND receiver_id = ?))
    AND status = 'accepted'
");
$friendship_check->bind_param("iiii", $current_user_id, $friend_id, $friend_id, $current_user_id);
$friendship_check->execute();
$friendship_result = $friendship_check->get_result();

if ($friendship_result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Vocês não são amigos.']);
    exit;
}
$friendship_check->close();

// Get friend info
$friend_stmt = $conn->prepare("SELECT id, username, full_name FROM users WHERE id = ?");
$friend_stmt->bind_param("i", $friend_id);
$friend_stmt->execute();
$friend_result = $friend_stmt->get_result();
$friend_data = $friend_result->fetch_assoc();
$friend_stmt->close();

if (!$friend_data) {
    echo json_encode(['success' => false, 'error' => 'Usuário não encontrado.']);
    exit;
}

// Fetch messages between the two users
$stmt = $conn->prepare("
    SELECT m.id, m.sender_id, m.receiver_id, m.message, m.created_at, m.is_read,
           u.username, u.full_name
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
    ORDER BY m.created_at ASC
");
$stmt->bind_param("iiii", $current_user_id, $friend_id, $friend_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'id' => (int)$row['id'],
        'sender_id' => (int)$row['sender_id'],
        'receiver_id' => (int)$row['receiver_id'],
        'message' => $row['message'],
        'created_at' => $row['created_at'],
        'is_read' => (bool)$row['is_read'],
        'sender_username' => $row['username'],
        'sender_full_name' => $row['full_name'],
        'is_mine' => (int)$row['sender_id'] === $current_user_id
    ];
}

$stmt->close();

// Mark messages as read (messages sent by friend to current user)
$mark_read = $conn->prepare("UPDATE messages SET is_read = TRUE WHERE sender_id = ? AND receiver_id = ? AND is_read = FALSE");
$mark_read->bind_param("ii", $friend_id, $current_user_id);
$mark_read->execute();
$mark_read->close();

$conn->close();

echo json_encode([
    'success' => true,
    'friend' => [
        'id' => (int)$friend_data['id'],
        'username' => $friend_data['username'],
        'full_name' => $friend_data['full_name']
    ],
    'messages' => $messages
]);
?>
