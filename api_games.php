<?php
include 'db_connect.php';

header('Content-Type: application/json');

$sql = "SELECT game_id, title, price, image, genre FROM games";
$result = $conn->query($sql);

if (!$result) {
    print json_encode(array("error" => "Database query failed"));
    exit();
}

$games = array();

while ($row = $result->fetch_assoc()) {
    $games[] = $row;
}

print json_encode($games);
?>