<?php
$servername = "localhost"; // Assuming localhost; change if your DB host is different
$username = "uxhc7qjwxxfub";
$password = "g4t0vezqttq6";
$dbname = "dbjuffbb0wtoai";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
