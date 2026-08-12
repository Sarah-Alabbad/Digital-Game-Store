<!-- Log-in page - Developed by Zahra AL-mari -->

<?php
// Start the session so we can save the user's login information
session_start();

// Connect this page to the database
include "db_connect.php";

// Variables used for popup messages
$message = "";
$show_popup = false;
$popup_title = "";
$popup_text = "";

// This variable stores the remembered username from the cookie
// If the cookie exists, the username input will be filled automatically
$remembered_username = "";

// Check if the remember username cookie exists
if (isset($_COOKIE["remember_username"])) {
    $remembered_username = $_COOKIE["remember_username"];
}

// Check if the form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the username and password entered by the user
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if the Remember Me checkbox was selected
    $remember_me = isset($_POST["remember_me"]);

    // Check if the username or password field is empty
    if (empty($username) || empty($password)) {
        $show_popup = true;
        $popup_title = "Missing Information";
        $popup_text = "Please enter both username and password.";

    } else {

        // Search for a user with the entered username
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);

        // Check if exactly one user was found
        if (mysqli_num_rows($result) == 1) {

            // Get the user's data from the database result
            $user = mysqli_fetch_assoc($result);

            // Check if the entered password matches the hashed password in the database
            if (password_verify($password, $user["password"])) {

                // Save user information in the session
                // This means the user is now logged in
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"] = $user["role"];

                // If Remember Me is checked, save the username in a cookie for 30 days
                if ($remember_me) {
                    setcookie("remember_username", $username, time() + (86400 * 30), "/");
                } else {
                    // If Remember Me is not checked, delete the cookie if it already exists
                    setcookie("remember_username", "", time() - 3600, "/");
                }

                // Send the user to the home page after successful login
                header("Location: home.php");
                exit();

            } else {

                // If the password is wrong, show a login failed popup
                $show_popup = true;
                $popup_title = "Login Failed";
                $popup_text = "Wrong password. Please try again.";
            }

        } else {

            // If no username was found, show a login failed popup
            $show_popup = true;
            $popup_title = "Login Failed";
            $popup_text = "Username not found. Please check your username.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<!-- Set the character encoding for the page -->
<meta charset="UTF-8">

<!-- Page title shown in the browser tab -->
<title>Login | Digital Game Store</title>

<!-- Link the external CSS file -->
<link rel="stylesheet" href="Style/style.css">
</head>

<body>

<!-- Main wrapper that centers the login card on the page -->
<div style="display:flex; justify-content:center; align-items:center; min-height:100vh;">

    <!-- Card that contains the login form -->
    <div class="card" style="width:400px; margin-bottom:60px;">

        <h1>Login</h1>

        <!-- Login form sends the data to the same page using POST -->
        <form method="POST" action="" novalidate>

            <!-- Username input
                 If a cookie exists, the username will appear here automatically -->
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter username" value="<?php echo $remembered_username; ?>">

            <!-- Password input -->
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password">

            <!-- Remember Me checkbox
     This lets the user save their username in a browser cookie -->
<div style="
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    margin-top:15px;
">

    <input type="checkbox" name="remember_me" id="remember_me"
           style="
           width:auto;
           height:auto;
           margin:0;
           padding:0;
           accent-color:#3b9b5f;
           "
           <?php if ($remembered_username != "") { echo "checked"; } ?>>

    <label for="remember_me" style="
        margin:0;
        color:#f1efff;
        font-size:14px;
        cursor:pointer;
    ">
        Remember my username
    </label>

</div>

            <!-- Submit button -->
            <button type="submit" class="btn" style="display:block; margin:25px auto 0; width:fit-content;">
                Login
            </button>

        </form>

        <!-- Link for users who do not have an account yet -->
        <div class="signup-link">
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>

    </div>
</div>

<!-- Page footer -->
<footer>
© 2026 Digital Game Store | All Rights Reserved
</footer>

<!-- Login popup
     It starts hidden and appears when the user enters missing or wrong information -->
<div id="loginPopup" style="
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

    <!-- Inner popup box -->
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

        <!-- Popup title -->
        <h2 id="popupTitle" style="color:#bfa67a; margin-top:0;">Missing Information</h2>

        <!-- Popup message text -->
        <p id="popupText" style="font-size:16px; margin:20px 0;">
            Please enter both username and password.
        </p>

        <!-- Button to close the popup -->
        <button id="closeLoginPopup" class="btn">Okay</button>
    </div>

</div>

<script>
// Get the login form from the page
const loginForm = document.querySelector("form");

// Get the username and password input fields
const usernameInput = document.querySelector('input[name="username"]');
const loginPasswordInput = document.querySelector('input[name="password"]');

// Get the popup elements from the page
const loginPopup = document.getElementById("loginPopup");
const closeLoginPopup = document.getElementById("closeLoginPopup");
const popupTitle = document.getElementById("popupTitle");
const popupText = document.getElementById("popupText");

// Function used to show the login popup with a custom title and message
function showLoginPopup(title, text) {
    popupTitle.textContent = title;
    popupText.textContent = text;
    loginPopup.style.display = "flex";
}

// This runs when the user clicks the Login button
loginForm.addEventListener("submit", function(event) {

    // Check if the username or password input is empty before sending the form to PHP
    if (usernameInput.value.trim() === "" || loginPasswordInput.value.trim() === "") {

        // Stop the form from submitting
        event.preventDefault();

        // Show missing information popup
        showLoginPopup("Missing Information", "Please enter both username and password.");
    }
});

// Close the popup when the user clicks Okay
closeLoginPopup.addEventListener("click", function() {
    loginPopup.style.display = "none";
});

// Close the popup when the user clicks the dark background outside the popup box
loginPopup.addEventListener("click", function(event) {
    if (event.target === loginPopup) {
        loginPopup.style.display = "none";
    }
});

<?php
// If PHP found a login problem after submitting,
// print JavaScript code that shows the popup with the correct message
if ($show_popup == true) {
    echo "showLoginPopup(" . json_encode($popup_title) . ", " . json_encode($popup_text) . ");";
}
?>
</script>

</body>
</html>