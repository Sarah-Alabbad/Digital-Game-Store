<?php
session_start();
include "db_connect.php";

if (!isset($_GET["genre"]) || empty($_GET["genre"])) {
    die("No category selected.");
}

$genre = $_GET["genre"];

$stmt = $conn->prepare("SELECT game_id, title, price, image, genre FROM games WHERE genre = ?");
$stmt->bind_param("s", $genre);
$stmt->execute();
$result = $stmt->get_result();

$safe_genre = htmlspecialchars($genre, ENT_QUOTES);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $safe_genre; ?> Games | Digital Game Store</title>
<link rel="stylesheet" href="Style/style.css">

<script>
var priceIds = [];
var gameIds = [];
var categoryIds = [];
</script>

</head>

<body style="min-height:100vh; display:flex; flex-direction:column;">

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

<div class="container" style="flex:1;">

<h1><?php echo $safe_genre; ?> Games</h1>

<div class="card">

<p id="gameCount" style="text-align:center; color:#bfa67a; font-weight:bold;"></p>

<div style="
    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    gap:25px;
    margin-top:25px;
">

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $game_title = htmlspecialchars($row["title"], ENT_QUOTES);
?>

    <div class="card"
         id="card<?php echo $row['game_id']; ?>"
         style="
            width:260px;
            margin:0;
            text-align:center;
            padding:18px;
         "
         onmouseover="enlargeCard('card<?php echo $row['game_id']; ?>')"
         onmouseout="normalCard('card<?php echo $row['game_id']; ?>')">

        <img src="<?php echo $row['image']; ?>" 
             alt="<?php echo $game_title; ?>"
             style="
                width:200px;
                height:260px;
                object-fit:cover;
                border:2px solid #4a475e;
                display:block;
                margin:0 auto 15px;
             ">

        <h3 style="min-height:45px;">
            <?php echo $row["title"]; ?>
        </h3>

        <p>
            Price:
            <span id="price<?php echo $row['game_id']; ?>">
                <?php echo number_format($row["price"], 2); ?>
            </span>
        </p>

        <script>
            priceIds[priceIds.length] = "price<?php echo $row['game_id']; ?>";
            gameIds[gameIds.length] = "card<?php echo $row['game_id']; ?>";
        </script>

        <a href="game_details.php?id=<?php echo $row['game_id']; ?>" 
           class="btn view-details-link"
           data-title="<?php echo $game_title; ?>">
            View Details
        </a>

    </div>

<?php
    }
} else {
    echo "<p style='text-align:center;'>No games found in this category.</p>";
}
?>

</div>

<div style="text-align:center; margin-top:30px;">
    <a href="categories.php" class="btn">Back to Categories</a>
</div>

</div>

</div>

<footer style="
    width:100%;
    text-align:center;
    padding:15px;
    background-color:#24222f;
    border-top:2px solid #4a475e;
    color:#d1d1e0;
    font-size:14px;
    margin-top:auto;
">
    © 2026 Digital Game Store | All Rights Reserved
</footer>

<!-- Game Details Popup -->
<div id="gameDetailsPopup" style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background-color:rgba(0,0,0,0.65);
    justify-content:center;
    align-items:center;
    z-index:9999;
">

    <div style="
        background-color:#2e2c39;
        color:#d1d1e0;
        border:2px solid #4a475e;
        border-radius:15px;
        padding:30px;
        width:360px;
        text-align:center;
        box-shadow:0 0 20px rgba(0,0,0,0.5);
    ">
        <h2 style="color:#bfa67a; margin-top:0;">Open Game Details?</h2>

        <p id="gameDetailsPopupText" style="font-size:16px; margin:20px 0;">
            Do you want to view this game?
        </p>

        <div style="display:flex; justify-content:center; gap:15px;">
            <button id="cancelDetails" class="btn">Cancel</button>
            <button id="confirmDetails" class="btn">View Details</button>
        </div>
    </div>

</div>

<script src="script.js"></script>

<script>
const detailsLinks = document.querySelectorAll(".view-details-link");
const gameDetailsPopup = document.getElementById("gameDetailsPopup");
const gameDetailsPopupText = document.getElementById("gameDetailsPopupText");
const cancelDetails = document.getElementById("cancelDetails");
const confirmDetails = document.getElementById("confirmDetails");

let detailsUrl = "";

detailsLinks.forEach(function(link) {
    link.addEventListener("click", function(event) {
        event.preventDefault();

        const gameTitle = this.getAttribute("data-title");
        detailsUrl = this.href;

        gameDetailsPopupText.textContent = "Do you want to view details for " + gameTitle + "?";
        gameDetailsPopup.style.display = "flex";
    });
});

cancelDetails.addEventListener("click", function() {
    gameDetailsPopup.style.display = "none";
    detailsUrl = "";
});

confirmDetails.addEventListener("click", function() {
    window.location.href = detailsUrl;
});

gameDetailsPopup.addEventListener("click", function(event) {
    if (event.target === gameDetailsPopup) {
        gameDetailsPopup.style.display = "none";
        detailsUrl = "";
    }
});
</script>

<?php include "logout_popup.php"; ?>

</body>
</html>