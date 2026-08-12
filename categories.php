<?php
session_start();
include "db_connect.php";

$sql = "SELECT DISTINCT genre FROM games WHERE genre IS NOT NULL AND genre != ''";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Categories | Digital Game Store</title>
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

<h1>Game Categories</h1>

<div class="card">
    <p id="categoryCount" style="text-align:center; color:#bfa67a; font-weight:bold;"></p>

    <div style="
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(230px, 1fr));
        gap:20px;
        margin-top:20px;
    ">

<?php
$i = 0;

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        $genre = $row["genre"];

        if ($genre == "Action" || $genre == "Action Adventure") {
            $image = "Images/The Last of Us™ Part I.jpg";
            $description = "Fast-paced games with combat, missions, and intense gameplay.";
        } 
        else if ($genre == "Racing") {
            $image = "Images/car.jpg";
            $description = "High-speed games focused on cars, tracks, and competition.";
        } 
        else if ($genre == "Adventure") {
            $image = "Images/Hollow Knight1.jpg";
            $description = "Explore worlds, complete quests, and discover new stories.";
        } 
        else if ($genre == "Strategy") {
            $image = "Images/smart.jpg";
            $description = "Plan, build, and make smart decisions to win.";
        } 
        else if ($genre == "Sports") {
            $image = "Images/football.jpg";
            $description = "Sports games with teams, matches, and tournaments.";
        } 
        else if ($genre == "Shooter") {
            $image = "Images/Call_of_Duty_Infinite_Warfare_cover.jpg";
            $description = "Combat games focused on weapons, missions, and action.";
        } 
        else if ($genre == "Sandbox") {
            $image = "Images/Minecraft_2024_cover_art.png.webp";
            $description = "Creative games where players can build and explore freely.";
        } 
        else {
            $image = "Images/6522516.png";
            $description = "Games from this category.";
        }

        $safe_genre = htmlspecialchars($genre, ENT_QUOTES);
?>

        <div class="card" 
             id="category<?php echo $i; ?>"
             style="margin:0; text-align:center;"
             onmouseover="enlargeCard('category<?php echo $i; ?>')"
             onmouseout="normalCard('category<?php echo $i; ?>')">

            <img src="<?php echo $image; ?>" 
                 alt="<?php echo $safe_genre; ?>"
                 style="width:100%; height:160px; object-fit:cover; border:2px solid #4a475e;">

            <h3><?php echo $genre; ?></h3>

            <p style="min-height:60px;">
                <?php echo $description; ?>
            </p>

            <script>
                categoryIds[categoryIds.length] = "category<?php echo $i; ?>";
            </script>

            <a href="category_games.php?genre=<?php echo urlencode($genre); ?>" 
               class="btn category-link"
               data-title="<?php echo $safe_genre; ?>">
                View Games
            </a>

        </div>

<?php
        $i++;
    }
} else {
    echo "<p style='text-align:center;'>No categories found.</p>";
}
?>

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

<!-- Category Popup -->
<div id="categoryPopup" style="
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
        <h2 style="color:#bfa67a; margin-top:0;">Open Category?</h2>

        <p id="categoryPopupText" style="font-size:16px; margin:20px 0;">
            Do you want to view this category?
        </p>

        <div style="display:flex; justify-content:center; gap:15px;">
            <button id="cancelCategory" class="btn">Cancel</button>
            <button id="confirmCategory" class="btn">View Games</button>
        </div>
    </div>

</div>

<script src="script.js"></script>

<script>
const categoryLinks = document.querySelectorAll(".category-link");
const categoryPopup = document.getElementById("categoryPopup");
const categoryPopupText = document.getElementById("categoryPopupText");
const cancelCategory = document.getElementById("cancelCategory");
const confirmCategory = document.getElementById("confirmCategory");

let categoryUrl = "";

categoryLinks.forEach(function(link) {
    link.addEventListener("click", function(event) {
        event.preventDefault();

        const categoryName = this.getAttribute("data-title");
        categoryUrl = this.href;

        categoryPopupText.textContent = "Do you want to view " + categoryName + " games?";
        categoryPopup.style.display = "flex";
    });
});

cancelCategory.addEventListener("click", function() {
    categoryPopup.style.display = "none";
    categoryUrl = "";
});

confirmCategory.addEventListener("click", function() {
    window.location.href = categoryUrl;
});

categoryPopup.addEventListener("click", function(event) {
    if (event.target === categoryPopup) {
        categoryPopup.style.display = "none";
        categoryUrl = "";
    }
});
</script>

<?php include "logout_popup.php"; ?>

</body>
</html>