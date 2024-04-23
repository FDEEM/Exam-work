<?php
//$servername = "172.17.51.91";
//$username = "kab213_6";
//$password = "mok213_6";
//$dbname = "kab213_6_game_guides";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "game_guides";

// create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
