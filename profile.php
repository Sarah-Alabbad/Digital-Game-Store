<!-- Profile page - Developed by Zahra AL-mari & Batool Al Fardan -->

<?php
// Start the session so we can access the logged-in user's data
session_start();

// Connect this page to the database
include "db_connect.php";

// Check if the user is logged in
// If not, send them back to the login page
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Store the logged-in user's ID
$user_id = $_SESSION["user_id"];

/* Get user info */

// Get all information for the current user
$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Store username and role to use later in the page
$username = $user["username"];
$role = $user["role"];

// Check if the user has uploaded a profile image
if (!empty($user["profile_image"])) {
    $profile_image = $user["profile_image"];
} else {
    // Use a default profile image if the user does not have one
    $profile_image = "Images/6522516.png";
}

/* Admin stats */

// Default values for admin statistics
$total_games = 0;
$total_users = 0;
$total_reviews = 0;
$total_applications = 0;

// Only calculate these statistics if the logged-in user is an admin
if ($role == "admin") {

    // Get all games, users, reviews, and job applications
    $games_result = mysqli_query($conn, "SELECT * FROM games");
    $users_result = mysqli_query($conn, "SELECT * FROM users");
    $reviews_result = mysqli_query($conn, "SELECT * FROM reviews");
    $applications_result = mysqli_query($conn, "SELECT * FROM applications");

    // Count total games if the query worked
    if ($games_result) {
        $total_games = mysqli_num_rows($games_result);
    }

    // Count total users if the query worked
    if ($users_result) {
        $total_users = mysqli_num_rows($users_result);
    }

    // Count total reviews if the query worked
    if ($reviews_result) {
        $total_reviews = mysqli_num_rows($reviews_result);
    }

    // Count total job applications if the query worked
    if ($applications_result) {
        $total_applications = mysqli_num_rows($applications_result);
    }
}

/* Normal user library */

// Get the games owned by the current user
// user_library connects users with games they bought
$library_sql = "SELECT games.game_id, games.title, games.image
                FROM user_library
                JOIN games ON user_library.game_id = games.game_id
                WHERE user_library.user_id = '$user_id'";

$library_result = mysqli_query($conn, $library_sql);

// Count how many games the user owns
$games_count = mysqli_num_rows($library_result);

// If the user owns games, create simple profile stats
if ($games_count > 0) {
    $achievements_count = 3;
    $hours_played = $games_count * 12;
} else {
    // If the user owns no games, all stats stay at 0
    $achievements_count = 0;
    $hours_played = 0;
}
?>

<!DOCTYPE html>
<html>
<head>
<!-- Set the character encoding for the page -->
<meta charset="UTF-8">

<!-- Page title shown in the browser tab -->
<title>Profile | Digital Game Store</title>

<!-- Link the external CSS file -->
<link rel="stylesheet" href="Style/style.css">
</head>

<body>

<!-- Navigation bar -->
<nav>
    <a href="home.php">Home</a>

    <!-- Show Admin link only if the logged-in user is an admin -->
    <?php if ($role == "admin") { ?>
        <a href="admin.php">Admin</a>
    <?php } ?>

    <a href="cart.php">Cart</a>
    <a href="support.php">Support</a>
    <a href="FAQ.html">FAQ</a>
    <a href="Terms & Conditions.html">Terms</a>
    <a href="about_us.php">About Us</a>
    <a href="Join_Team.php">Join Team</a>
    <a href="logout.php" class="logout-link">Logout</a>
</nav>

<!-- Main page container -->
<div class="container">

    <!-- Profile header card -->
    <div class="card profile-header">

        <!-- Display the user's profile picture -->
        <img src="<?php echo $profile_image; ?>" class="profile-pic" alt="Profile Picture"
             style="width:140px; height:140px; object-fit:cover; border:4px solid #4a475e;">

        <div>
            <!-- Display the username -->
            <h1><?php echo $username; ?></h1>

            <!-- If the user is admin, show admin profile information -->
            <?php if ($role == "admin") { ?>

                <p style="color:#bfa67a; font-weight:bold;">Administrator Account</p>

                <!-- Admin quick statistics -->
                <div class="stats">
                    <span><strong><?php echo $total_games; ?></strong> Games</span>
                    <span><strong><?php echo $total_users; ?></strong> Users</span>
                    <span><strong><?php echo $total_reviews; ?></strong> Reviews</span>
                </div>

            <?php } else { ?>

                <!-- If normal user owns games, show member since text -->
                <?php if ($games_count > 0) { ?>
                    <p>Member Since: January 2026</p>
                <?php } else { ?>
                    <p>New Member</p>
                <?php } ?>

                <!-- Normal user statistics -->
                <div class="stats">
                    <span><strong><?php echo $games_count; ?></strong> Games</span>
                    <span><strong><?php echo $achievements_count; ?></strong> Achievements</span>
                    <span><strong><?php echo $hours_played; ?></strong> Hours Played</span>
                </div>

            <?php } ?>

            <!-- Link to edit profile page -->
            <a href="edit_profile.php" class="btn">Edit Profile</a>
        </div>

    </div>

    <!-- Admin-only section -->
    <?php if ($role == "admin") { ?>

        <!-- Admin dashboard summary card -->
        <div class="card">
            <h2>Admin Dashboard</h2>

            <p style="text-align:center;">
                Welcome, <?php echo $username; ?>. This account is used to manage the digital game store.
            </p>

            <!-- Admin stats grid -->
            <div style="
                display:grid;
                grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));
                gap:20px;
                margin-top:25px;
                text-align:center;
            ">

                <div class="card" style="margin:0;">
                    <h3><?php echo $total_games; ?></h3>
                    <p>Total Games</p>
                </div>

                <div class="card" style="margin:0;">
                    <h3><?php echo $total_users; ?></h3>
                    <p>Registered Users</p>
                </div>

                <div class="card" style="margin:0;">
                    <h3><?php echo $total_reviews; ?></h3>
                    <p>User Reviews</p>
                </div>

                <div class="card" style="margin:0;">
                    <h3><?php echo $total_applications; ?></h3>
                    <p>Job Applications</p>
                </div>

            </div>
        </div>

        <!-- Admin tools card -->
        <div class="card">
            <h2>Admin Tools</h2>

            <p style="text-align:center;">
                Use these options to manage the store, users, support, and website content.
            </p>

            <!-- Admin action buttons -->
            <div style="display:flex; justify-content:center; gap:15px; flex-wrap:wrap; margin-top:25px;">
                <a href="admin.php" class="btn">Manage Games</a>
                <a href="home.php" class="btn">View Store</a>
                <a href="support.php" class="btn">Support Page</a>
                <a href="Join_Team.php" class="btn">Job Applications</a>
            </div>
        </div>

        <!-- Admin responsibilities card -->
        <div class="card">
            <h2>Admin Responsibilities</h2>

            <ul>
                <li>Manage games displayed in the store</li>
                <li>Check user activity and submitted reviews</li>
                <li>Review support messages from customers</li>
                <li>Keep store information updated</li>
            </ul>
        </div>

    <?php } else { ?>

        <!-- Normal user library section -->
        <div class="card">
            <h2>My Library</h2>

            <!-- If the user has games, show the library -->
            <?php if ($games_count > 0) { ?>

                <p style="text-align:center; color:#bfa67a;">
                    Click a game to write a review.
                </p>

                <div class="library-grid">

                    <?php
                    // Reset the library result pointer back to the first row
                    // This allows us to loop through the games again
                    mysqli_data_seek($library_result, 0);

                    // Display each owned game inside the library
                    while ($game = mysqli_fetch_assoc($library_result)) {
                    ?>

                        <!-- One game box in the user's library -->
                        <div class="game-box"
     data-game-id="<?php echo $game['game_id']; ?>"
     data-game-title="<?php echo $game['title']; ?>">

    <!-- Game image -->
    <img src="<?php echo $game['image']; ?>" 
         class="game-img" 
         alt="<?php echo $game['title']; ?>"
         style="width:100%; height:180px; object-fit:cover; border:2px solid #4a475e;">

    <!-- Game title -->
    <p><?php echo $game["title"]; ?></p>

    <!-- Play button opens the game launcher popup -->
    <button type="button" 
            class="btn play-btn"
            data-game-title="<?php echo $game['title']; ?>"
            style="margin-top:10px;">
        Play
    </button>
</div>

                    <?php } ?>

                </div>

            <?php } else { ?>

                <!-- Message shown when the user's library is empty -->
                <p style="text-align:center;">
                    Your library is empty.
                </p>

                <!-- Button to browse the store -->
                <div style="text-align:center; margin-top:20px;">
                    <a href="home.php" class="btn">Browse Store</a>
                </div>

            <?php } ?>

        </div>

        <!-- Achievements section -->
        <div class="card">
            <h2>My Achievements</h2>

            <!-- Show achievements only if the user has games -->
            <?php if ($games_count > 0) { ?>

                <!-- Button used to hide or show the achievement list -->
                <button id="toggleAchievements" class="btn" style="margin-bottom:15px;">
                    Hide Achievements
                </button>

                <!-- List of simple achievements -->
                <ul id="achievementsList">
                    <li>🏆 First Purchase</li>
                    <li>🎮 Game Owner</li>
                    <li>⭐ Store Member</li>
                </ul>

            <?php } else { ?>

                <!-- Message shown if the user has no achievements yet -->
                <p style="text-align:center;">
                    No achievements yet.
                </p>

            <?php } ?>

        </div>

        <!-- Reviews section loaded using API -->
        <div class="card">
            <h2>My Reviews</h2>

            <div id="reviewsBox">
                <p style="text-align:center;">Loading reviews...</p>
            </div>
        </div>

    <?php } ?>

</div>

<!-- Page footer -->
<footer style="position:fixed; bottom:0; width:100%; text-align:center; padding:15px; background-color:#24222f; border-top:2px solid #4a475e; color:#d1d1e0; font-size:14px;">
    © 2026 Digital Game Store | All Rights Reserved
</footer>

<!-- Game review popup only appears for normal users who own games -->
<?php if ($role != "admin" && $games_count > 0) { ?>

<!-- Review popup container -->
<div id="gamePopup" style="
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

    <!-- Inner review popup box -->
    <div style="
        background-color:#2e2c39;
        color:#d1d1e0;
        border:2px solid #4a475e;
        border-radius:15px;
        padding:30px;
        width:400px;
        text-align:center;
        box-shadow:0 0 20px rgba(0,0,0,0.5);
    ">

        <h2 style="color:#bfa67a; margin-top:0;">Game Review</h2>

        <!-- The selected game name will be inserted here by JavaScript -->
        <p id="popupGameName" style="font-size:18px; margin:15px 0;"></p>

        <!-- Review form sends the review to add_review.php -->
        <form method="POST" action="add_review.php">

            <!-- Hidden input stores the selected game ID -->
            <input type="hidden" name="game_id" id="reviewGameId">

            <!-- Rating dropdown -->
            <label>Rating</label>
            <select name="rating" required style="width:100%; padding:10px; margin:10px 0; border-radius:8px;">
                <option value="">Choose Rating</option>
                <option value="1">1 ⭐</option>
                <option value="2">2 ⭐⭐</option>
                <option value="3">3 ⭐⭐⭐</option>
                <option value="4">4 ⭐⭐⭐⭐</option>
                <option value="5">5 ⭐⭐⭐⭐⭐</option>
            </select>

            <!-- Review comment input -->
            <label>Review</label>
            <textarea name="comment" placeholder="Write your review..." required style="
                width:100%;
                height:100px;
                padding:10px;
                margin:10px 0;
                border-radius:8px;
                resize:none;
            "></textarea>

            <!-- Popup action buttons -->
            <div style="display:flex; justify-content:center; gap:15px; margin-top:15px;">
                <button type="button" id="closePopup" class="btn">Cancel</button>
                <button type="submit" class="btn">Submit Review</button>
            </div>

        </form>

    </div>

</div>

<script>
// Get all game boxes from the user's library
const gameBoxes = document.querySelectorAll(".game-box");

// Get review popup elements
const gamePopup = document.getElementById("gamePopup");
const popupGameName = document.getElementById("popupGameName");
const closePopup = document.getElementById("closePopup");
const reviewGameId = document.getElementById("reviewGameId");

// Add a click event to each game box
gameBoxes.forEach(function(game) {
    game.addEventListener("click", function() {

        // Get game title and ID from the data attributes
        const gameName = game.getAttribute("data-game-title");
        const gameId = game.getAttribute("data-game-id");

        // Show the selected game name in the popup
        popupGameName.textContent = "Review: " + gameName;

        // Put the selected game ID into the hidden input
        reviewGameId.value = gameId;

        // Show the review popup
        gamePopup.style.display = "flex";
    });

    // Make the game box look clickable
    game.style.cursor = "pointer";
});

// Close the review popup when Cancel is clicked
closePopup.addEventListener("click", function() {
    gamePopup.style.display = "none";
});

// Close the popup when clicking the dark background outside the popup box
gamePopup.addEventListener("click", function(event) {
    if (event.target === gamePopup) {
        gamePopup.style.display = "none";
    }
});
</script>

<?php } ?>

<!-- Achievement toggle script only for normal users who own games -->
<?php if ($role != "admin" && $games_count > 0) { ?>

<script>
// Get the achievement button and achievement list
const toggleButton = document.getElementById("toggleAchievements");
const achievementsList = document.getElementById("achievementsList");

// Check that the button exists before adding the event
if (toggleButton) {
    toggleButton.addEventListener("click", function() {

        // If achievements are hidden, show them
        if (achievementsList.style.display === "none") {
            achievementsList.style.display = "block";
            toggleButton.textContent = "Hide Achievements";

        // If achievements are showing, hide them
        } else {
            achievementsList.style.display = "none";
            toggleButton.textContent = "Show Achievements";
        }
    });
}
</script>

<?php } ?>

<!-- Reviews API script only for normal users -->
<?php if ($role != "admin") { ?>

<script>
// Request the current user's reviews from the API page
fetch("api_user_reviews.php")
    .then(function(response) {
        // Convert the API response into JSON
        return response.json();
    })
    .then(function(data) {

        // Get the reviews box where reviews will be displayed
        const reviewsBox = document.getElementById("reviewsBox");

        // Stop if the reviews box does not exist
        if (!reviewsBox) {
            return;
        }

        // If the API returns an error, show an error message
        if (data.status !== "success") {
            reviewsBox.innerHTML = "<p style='text-align:center;'>Could not load reviews.</p>";
            return;
        }

        // If the user has no reviews, show a no reviews message
        if (data.reviews.length === 0) {
            reviewsBox.innerHTML = "<p style='text-align:center;'>No reviews yet.</p>";
            return;
        }

        // This variable will hold the HTML for all reviews
        let output = "";

        // Loop through each review and add it to the output
        data.reviews.forEach(function(review) {
            output += `
                <div style="
                    border:1px solid #4a475e;
                    padding:15px;
                    border-radius:10px;
                    margin:12px 0;
                    background-color:#24222f;
                ">
                    <h3 style="margin-top:0;">${review.title}</h3>

                    <p style="color:#bfa67a; font-weight:bold;">
                        ${review.review_rating} ⭐
                    </p>

                    <p>${review.comment}</p>

                    <small>${review.review_date}</small>
                </div>
            `;
        });

        // Display all reviews inside the reviews box
        reviewsBox.innerHTML = output;
    })
    .catch(function(error) {

        // If the API request fails, show an API error message
        const reviewsBox = document.getElementById("reviewsBox");

        if (reviewsBox) {
            reviewsBox.innerHTML = "<p style='text-align:center;'>API error loading reviews.</p>";
        }

        // Print the error in the browser console for debugging
        console.log(error);
    });
</script>

<?php } ?>

<!-- Include the logout confirmation popup file -->
<?php include "logout_popup.php"; ?>

<!-- Game launcher popup
     It appears when the user clicks the Play button -->
<div id="playPopup" style="
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

    <!-- Inner play popup box -->
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
        <h2 style="color:#bfa67a; margin-top:0;">Game Launcher</h2>

        <!-- This text changes based on which game is clicked -->
        <p id="playPopupText" style="font-size:16px; margin:20px 0;">
            Starting game...
        </p>

        <!-- Close play popup button -->
        <button id="closePlayPopup" class="btn">Close</button>
    </div>

</div>

<script>
// Get all Play buttons
const playButtons = document.querySelectorAll(".play-btn");

// Get play popup elements
const playPopup = document.getElementById("playPopup");
const playPopupText = document.getElementById("playPopupText");
const closePlayPopup = document.getElementById("closePlayPopup");

// Add click event to each Play button
playButtons.forEach(function(button) {
    button.addEventListener("click", function(event) {

        // Stop the click from also opening the review popup on the game box
        event.stopPropagation();

        // Get the game title from the button data attribute
        const gameTitle = button.getAttribute("data-game-title");

        // Change popup text to show the selected game title
        playPopupText.textContent = "Starting " + gameTitle + "...";

        // Show the game launcher popup
        playPopup.style.display = "flex";
    });
});

// Close the play popup when Close is clicked
closePlayPopup.addEventListener("click", function() {
    playPopup.style.display = "none";
});

// Close the play popup when clicking the dark background outside the box
playPopup.addEventListener("click", function(event) {
    if (event.target === playPopup) {
        playPopup.style.display = "none";
    }
});
</script>

</body>
</html>