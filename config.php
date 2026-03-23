<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "booknomad";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erreur connexion: " . $conn->connect_error);
}
?>