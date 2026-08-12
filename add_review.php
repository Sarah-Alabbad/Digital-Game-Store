<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];
    $game_id = $_POST["game_id"];
    $rating = $_POST["rating"];
    $comment = $_POST["comment"];

    if ($game_id != "" && $rating != "" && $comment != "") {

        $sql = "INSERT INTO reviews (user_id, game_id, review_rating, comment)
                VALUES ('$user_id', '$game_id', '$rating', '$comment')";

        mysqli_query($conn, $sql);
    }
}

header("Location: profile.php");
exit();
?>