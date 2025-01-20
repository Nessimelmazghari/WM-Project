<?php
$servername = "ftp.nessimelmazghari-odiseebe.webhosting.be";
$username = "ID449469_nessim";  // Replace with your actual database username
$password = "Olala1234";
$dbname = "ID449469_nessim";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
