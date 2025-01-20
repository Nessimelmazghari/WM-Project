<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
// Inclusief databaseverbinding
require_once 'db.php';

// Zorg ervoor dat de response JSON is
header('Content-Type: application/json');

// Haal de ID op uit de querystring
$movieId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($movieId > -1) {
    // Query om de gegevens van een specifieke film op te halen
    $sql = "SELECT title, trailer, description, image FROM movies WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $movieId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Film gevonden
        $movie = $result->fetch_assoc();
        echo json_encode($movie);
    } else {
        // Geen film gevonden
        echo json_encode(['error' => 'Film niet gevonden']);
    }

    $stmt->close();
} else {
    // Geen geldige ID opgegeven
    echo json_encode(['error' => 'Ongeldige ID']);
}

$conn->close();
?>
