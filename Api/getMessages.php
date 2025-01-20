<?php
include 'db.php';  // Importeer de databaseverbinding
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
$movieId = $_GET['movieId'];  // Haal de movieId op uit de query parameters

// Haal berichten op uit de database
$query = "SELECT * FROM chat_messages WHERE movie_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $movieId);  // Bind de movieId parameter
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

// Zend de berichten als JSON
echo json_encode(['messages' => $messages]);

$stmt->close();
$conn->close();
?>
