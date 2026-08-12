<?php
// ==============================================
// Developed by: Fatima Alsayed
// About Us page with suggestion submission system
// ==============================================

session_start();

// Connect to database file
include "db_connect.php";

// Variable to display messages to the user
$message = "";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    $user_id = NULL;
} else {
    $user_id = $_SESSION["user_id"];
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get suggestion text from textarea
    $suggestion = $_POST["suggestion_text"];

    // Validate if suggestion is empty
    if (empty($suggestion)) {

        // Message if no suggestion entered
        $message = "Please write a suggestion first.";

    } else {

        // Insert suggestion into database
        $sql = "INSERT INTO suggestions (user_id, suggestion_text)
                VALUES ('$user_id', '$suggestion')";

        // Execute query
        if (mysqli_query($conn, $sql)) {

            // Success message
            $message = "Suggestion sent successfully!";

        } else {

            // Error message if query fails
            $message = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="UTF-8">

<!-- Page title -->
<title>About Us | Digital Game Store</title>

<!-- Link external CSS file -->
<link rel="stylesheet" href="Style/style.css">
</head>

<body>

<!-- Navigation bar -->
<nav>
    <a href="home.php">Home</a>
    <a href="profile.php">Profile</a>
    <a href="cart.php">Cart</a>
    <a href="support.php">Support</a>
    <a href="FAQ.html">FAQ</a>
    <a href="Terms & Conditions.html">Terms</a>
   
    <a href="Join_Team.php">Join Team</a>

    <!-- Logout button -->
    <a href="logout.php" class="logout-link">Logout</a>
</nav>

<!-- Main container -->
<div class="container">

<h1>About Us</h1>

<!-- About section -->
<div class="card">

<h2>About Digital Game Store</h2>

<p>
Digital Game Store is a digital game distribution platform designed to provide
players with easy access to a wide variety of video games. The platform allows
users to browse a large game library, purchase digital copies, and manage their
personal game collections in one place.
</p>

<p>
The system offers a modern and high-performance interface for gamers, making it
simple to explore games, discover new titles, and access purchased games
instantly.
</p>

<p>
In addition, the platform enables administrators or publishers to manage game
titles, update prices, and control digital license keys efficiently. The goal
of Digital Game Store is to deliver a fast, secure, and reliable environment
for buying and managing digital games.
</p>

</div>

<!-- Mission section -->
<div class="card">

<h2>Our Mission</h2>

<p>
Our mission is to provide gamers with a fast, secure, and easy platform
to discover, purchase, and manage their favorite video games anytime.
</p>

</div>

<!-- Features section -->
<div class="card">

<h2>Platform Features</h2>

<div class="features">

<div class="feature-item">

<h3>Large Game Library</h3>

<p>
Browse a wide range of digital games from different genres.
</p>

</div>

<div class="feature-item">

<h3>Secure Payments</h3>

<p>
Safe and reliable payment methods for purchasing games.
</p>

</div>

<div class="feature-item">

<h3>Game Library Management</h3>

<p>
Players can easily access and manage their purchased games.
</p>

</div>

</div>
</div>

<!-- Suggestion form section -->
<div class="card">

    <h2>Send Us Your Suggestion</h2>

    <?php
    // Display message if available
    if ($message != "") {

        echo "<p style='color:#bfa67a; text-align:center;'>$message</p>";
    }
    ?>

    <!-- Suggestion form -->
    <form method="POST" action="">

        <label>Your Suggestion</label>

        <!-- User suggestion input -->
        <textarea
            name="suggestion_text"
            rows="5"
            placeholder="Write your suggestion here">
        </textarea>

        <!-- Submit button -->
        <button
            type="submit"
            class="btn"
            style="display:block; margin:20px auto 0;">

            Submit Suggestion

        </button>

    </form>
</div>

</div>

<!-- Footer section -->
<footer style="position:fixed; bottom:0; width:100%; text-align:center; padding:15px; background-color:#24222f; border-top:2px solid #4a475e; color:#d1d1e0; font-size:14px;">

    © 2026 Digital Game Store | All Rights Reserved

</footer>

<!-- Logout popup include -->
<?php include "logout_popup.php"; ?>

</body>
</html>