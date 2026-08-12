<?php
/* User Reviews API page - Developed by Zahra AL-mari & Batool Al Fardan */

// Start the session so we can access the logged-in user's session data
session_start();

// Connect this file to the database using the connection file
include "db_connect.php";

// Tell the browser that this page will return JSON data, not normal HTML
header("Content-Type: application/json");

// Check if the user is logged in
// If there is no user_id in the session, return an error message as JSON
if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "status" => "error",
        "message" => "User not logged in"
    ]);
    exit();
}

// Store the logged-in user's ID in a variable
$user_id = $_SESSION["user_id"];

// Select all reviews written by this user
// It also gets the game title by joining the reviews table with the games table
$sql = "SELECT 
            reviews.review_rating,
            reviews.comment,
            reviews.review_date,
            games.title
        FROM reviews
        JOIN games ON reviews.game_id = games.game_id
        WHERE reviews.user_id = '$user_id'
        ORDER BY reviews.review_date DESC";

// Run the SQL query
$result = mysqli_query($conn, $sql);

// Create an empty array to store all the user's reviews
$reviews = [];

// Check if the query worked successfully
if ($result) {

    // Go through each review found in the database
    while ($row = mysqli_fetch_assoc($result)) {

        // Add each review row into the reviews array
        $reviews[] = $row;
    }

    // Return a success response with all the reviews as JSON
    echo json_encode([
        "status" => "success",
        "reviews" => $reviews
    ]);

} else {

    // If the query failed, return an error response with the database error message
    echo json_encode([
        "status" => "error",
        "message" => mysqli_error($conn)
    ]);
}
?>