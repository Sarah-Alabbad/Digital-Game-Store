<?php
// Home Page - Modified by Roaa Alhaddad
session_start();
include "db_connect.php";

// Get games data including discount price from database
$sql = "SELECT game_id, title, genre, price, discount_percent, image FROM games";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Digital Game Distribution Platform</title>

<link rel="stylesheet" href="Style/style.css">

<script>
// Arrays used by JavaScript functions
var priceIds = [];
var gameIds = [];
var categoryIds = [];
</script>

</head>

<body>

<!-- Navigation Bar - Modified by Roaa -->
<nav>

    <a href="home.php">Home</a>
    <a href="profile.php">Profile</a>
    <a href="cart.php">Cart</a>
    <a href="support.php">Support</a>
    <a href="FAQ.html">FAQ</a>
    <a href="Terms & Conditions.html">Terms</a>
    <a href="about_us.php">About Us</a>
    <a href="Join_Team.php">Join Team</a>
    <a href="logout.php" class="logout-link">Logout</a>

</nav>


<div class="container">

    <h1>Digital Game Distribution Platform</h1>
    <?php
if (isset($_COOKIE["past_purchases"])) {
    $past_purchases = json_decode($_COOKIE["past_purchases"], true);

    if (!empty($past_purchases)) {
?>

<div class="card" style="text-align:center;">
    <h2>Your Past Purchases</h2>

    <p style="color:#bfa67a;">
        Welcome back! Here are games you bought before:
    </p>

    <ul style="list-style:none; padding:0;">
        <?php foreach ($past_purchases as $game_title) { ?>
            <li style="margin:8px 0;">
                🎮 <?php echo $game_title; ?>
            </li>
        <?php } ?>
    </ul>
</div>

<?php
    }
}
?>

    <div class="card">

        <h2>Filter Options</h2>

        <div class="category-btn-wrapper">
            <a href="categories.php" class="btn btn-full">
                Browse All Categories
            </a>
        </div>

        <ul class="filter-list">

            <li>
                Genre
                <ul>
                    <li><a href="category_games.php?genre=Action">Action</a></li>
                    <li><a href="category_games.php?genre=Racing">Racing</a></li>
                    <li><a href="category_games.php?genre=Sports">Sports</a></li>
                    <li><a href="category_games.php?genre=Strategy">Strategy</a></li>
                    <li><a href="category_games.php?genre=Adventure">Adventure</a></li>
                </ul>
            </li>

            <li>
                Price
                <ul>
                    <li><a href="#">Free</a></li>
                    <li><a href="#">Under 50</a></li>
                    <li><a href="#">Under 60</a></li>
                    <li><a href="#">Under 100</a></li>
                </ul>
            </li>

            <li>
                Sort By
                <ul>
                    <li><a href="#">Newest</a></li>
                </ul>
            </li>

        </ul>

    </div>


    <div class="card">

        <h2>Game List</h2>

        <p id="gameCount" style="text-align:center; color:#bfa67a;"></p>
        <p id="apiMessage" style="text-align:center; color:#bfa67a;"></p>

        <?php
        // Check if there are games in database
        if (mysqli_num_rows($result) > 0) {

            // Display each game
            while ($game = mysqli_fetch_assoc($result)) {

                $game_title = htmlspecialchars($game['title'], ENT_QUOTES);
        ?>

        <div class="game-card"
             id="card<?php echo $game['game_id']; ?>"
             onmouseover="enlargeCard('card<?php echo $game['game_id']; ?>')"
             onmouseout="normalCard('card<?php echo $game['game_id']; ?>')">

            <img
                src="<?php echo $game['image']; ?>"
                alt="<?php echo $game_title; ?>"
                class="game-img"
                style="width:180px; height:140px; object-fit:cover;">

            <div class="game-info">

                <p>
                    <strong><?php echo $game['title']; ?></strong>
                </p>

                <?php
$price = $game["price"];
$discount_percent = $game["discount_percent"];

if ($discount_percent > 0) {
    $final_price = $price - ($price * ($discount_percent / 100));
?>

    <p>
        Price:
        <span style="text-decoration:line-through;">
            <?php echo number_format($price, 2); ?>
        </span>
        <br>

        Discount:
        <span style="color:lime; font-weight:bold;">
            <?php echo number_format($discount_percent, 0); ?>%
        </span>
        <br>

        Final Price:
        <strong><?php echo number_format($final_price, 2); ?></strong>
    </p>

<?php
} else {
?>

    <p>
        Price:
        <?php echo number_format($price, 2); ?>
    </p>

<?php
}
?>

                <p>
                    Genre:
                    <?php echo $game['genre']; ?>
                </p>

                <script>
                    // Store game card ID for JavaScript count and hover functions
                    priceIds[priceIds.length] =
                        "price<?php echo $game['game_id']; ?>";

                    gameIds[gameIds.length] =
                        "card<?php echo $game['game_id']; ?>";
                </script>

                <a
                    href="game_details.php?id=<?php echo $game['game_id']; ?>"
                    class="btn view-details-link"
                    data-title="<?php echo $game_title; ?>">

                    View Details

                </a>

            </div>

        </div>

        <?php
            }

        } else {

            echo "<p style='text-align:center;'>No games found.</p>";

        }
        ?>

    </div>

</div>


<footer>
    © 2026 Digital Game Store | All Rights Reserved
</footer>


<!-- Game Details Popup -->
<div id="gameDetailsPopup"
     style="
     display:none;
     position:fixed;
     top:0;
     left:0;
     width:100%;
     height:100%;
     background-color:rgba(0,0,0,0.65);
     justify-content:center;
     align-items:center;
     z-index:9999;">

    <div
        style="
        background-color:#2e2c39;
        color:#d1d1e0;
        border:2px solid #4a475e;
        border-radius:15px;
        padding:30px;
        width:360px;
        text-align:center;
        box-shadow:0 0 20px rgba(0,0,0,0.5);">

        <h2 style="color:#bfa67a; margin-top:0;">
            Open Game Details?
        </h2>

        <p id="gameDetailsPopupText" style="font-size:16px; margin:20px 0;">
            Do you want to view this game?
        </p>

        <div style="display:flex; justify-content:center; gap:15px;">

            <button id="cancelDetails" class="btn">
                Cancel
            </button>

            <button id="confirmDetails" class="btn">
                View Details
            </button>

        </div>

    </div>

</div>


<!-- External JavaScript file -->
<script src="script.js"></script>

<?php include "logout_popup.php"; ?>

</body>
</html>