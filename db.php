<?php
$servername = "localhost";
$username = "u373116035_digitalpaws";
$password = "#Bakitako2323";
$dbname = "u373116035_digitalpaws";

// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "u373116035_digitalpaws";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
