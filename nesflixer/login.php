<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database configuration file (db.php)
include '../php/inc/db.php';

header('Content-Type: application/json');

try {
    // Create a connection to the database using the existing $conn from db.php
    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Fout bij verbinding met de database: ' . $conn->connect_error]));
    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the username and password from the POST request
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare and execute the query to fetch user data
        $stmt = $conn->prepare("SELECT * FROM login_users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // User found, fetch user data
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start user session
                session_start();
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = $user['is_admin']; // Set admin status

                // Send JSON response with success status and admin status
                echo json_encode([
                    'success' => true,
                    'is_admin' => $user['is_admin']
                ]);
            } else {
                // Invalid password
                echo json_encode(['success' => false, 'message' => 'Ongeldige gebruikersnaam of wachtwoord.']);
            }
        } else {
            // User not found
            echo json_encode(['success' => false, 'message' => 'Ongeldige gebruikersnaam of wachtwoord.']);
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Er is een fout opgetreden: ' . $e->getMessage()]);
}
?>
