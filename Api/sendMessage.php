<?php
include 'db.php';  // Importeer de databaseverbinding
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Verkrijg de gegevens van de verzoekbody
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];  // Haal de gebruikersnaam op
$text = $data['text'];  // Haal de berichttekst op
$movieId = $data['movieId'];  // Haal de movieId op uit de JSON payload

// Voeg het nieuwe bericht toe aan de database
$query = "INSERT INTO chat_messages (username, text, movie_id, created_at) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param('ssi', $username, $text, $movieId);  // Bind de parameters
$stmt->execute();

// Haal het ID van het nieuwe bericht op
$message = [
    'id' => $conn->insert_id,
    'username' => $username,
    'text' => $text,
];

// Log het bericht naar een tekstbestand
$logMessage = "ID: {$message['id']} | Username: {$username} | Movie ID: {$movieId} | Message: {$text} | Timestamp: " . date('Y-m-d H:i:s') . "\n";
file_put_contents('messages.txt', $logMessage, FILE_APPEND);

// Zend het nieuwe bericht als JSON
echo json_encode(['message' => $message]);

$stmt->close();
$conn->close();
?>
