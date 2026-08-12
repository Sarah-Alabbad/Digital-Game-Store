<?php
/* Sign-up Page - Developed by Zahra AL-mari */

// Start the session so we can store the user's login information after sign-up
session_start();

// Connect this page to the database
include "db_connect.php";

// Variables used to control the popup message
$show_popup = false;
$popup_title = "";
$popup_text = "";

// Check if the form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the form data entered by the user
    // trim() removes extra spaces from the beginning and end
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check if any field is empty
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $show_popup = true;
        $popup_title = "Missing Information";
        $popup_text = "Please fill in all fields.";

    // Check if the password and confirm password do not match
    } else if ($password != $confirm_password) {
        $show_popup = true;
        $popup_title = "Password Error";
        $popup_text = "Passwords do not match. Please try again.";

    } else {

        // Check if the username or email already exists in the users table
        $check_sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $check_result = mysqli_query($conn, $check_sql);

        // If a matching account is found, show an error popup
        if (mysqli_num_rows($check_result) > 0) {
            $existing_user = mysqli_fetch_assoc($check_result);

            $show_popup = true;
            $popup_title = "Account Already Exists";

            // Check whether the problem is the username or the email
            if ($existing_user["username"] == $username) {
                $popup_text = "This username is already taken. Please choose another username.";
            } else {
                $popup_text = "This email is already registered. Please use another email or login.";
            }

        } else {

            // Hash the password before saving it in the database
            // This is safer than saving the real password text
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user account into the users table
            $sql = "INSERT INTO users (username, email, password)
                    VALUES ('$username', '$email', '$hashed_password')";

            // If the account was created successfully
            if (mysqli_query($conn, $sql)) {

                // Get the ID of the new user that was just inserted
                $new_user_id = mysqli_insert_id($conn);

                // Save user information inside the session
                // This logs the user in automatically after sign-up
                $_SESSION["user_id"] = $new_user_id;
                $_SESSION["username"] = $username;
                $_SESSION["role"] = "user";

                // Redirect the new user to the home page
                header("Location: home.php");
                exit();

            } else {

                // If the insert query failed, show an error popup
                $show_popup = true;
                $popup_title = "Sign Up Error";
                $popup_text = "Something went wrong. Please try again.";
            }
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
<title>Sign Up | Digital Game Store</title>

<!-- Link the external CSS file -->
<link rel="stylesheet" href="Style/style.css">
</head>

<body>

<!-- Main wrapper that centers the sign-up card on the page -->
<div style="display:flex; justify-content:center; align-items:center; min-height:100vh;">

    <!-- Card that contains the sign-up form -->
    <div class="card" style="width:500px; margin-bottom:60px;">

        <h1>Create Account</h1>

        <!-- Sign-up form sends data to the same page using POST -->
        <form method="POST" action="" novalidate>

            <!-- Username input -->
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter username">

            <!-- Email input -->
            <label>Email</label>
            <input type="email" name="email" placeholder="Enter email">

            <!-- Password input -->
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password">

            <!-- Confirm password input -->
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Confirm password">

            <!-- Submit button -->
            <button type="submit" class="btn" style="display:block; margin:20px auto 0; width:fit-content;">
                Sign Up
            </button>
        </form>

        <!-- Link for users who already have an account -->
        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>

    </div>
</div>

<!-- Page footer -->
<footer>
© 2026 Digital Game Store | All Rights Reserved
</footer>

<!-- Sign-up popup
     It starts hidden and appears when there is an error -->
<div id="signupPopup" style="
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
        <h2 id="popupTitle" style="color:#bfa67a; margin-top:0;">Error</h2>

        <!-- Popup text message -->
        <p id="popupText" style="font-size:16px; margin:20px 0;">
            Something went wrong.
        </p>

        <!-- Button to close the popup -->
        <button id="closeSignupPopup" class="btn">Okay</button>
    </div>

</div>

<script>
// Get the sign-up form from the page
const signupForm = document.querySelector("form");

// Get all input fields from the form
const usernameInput = document.querySelector('input[name="username"]');
const emailInput = document.querySelector('input[name="email"]');
const passwordInput = document.querySelector('input[name="password"]');
const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');

// Get the popup elements from the page
const signupPopup = document.getElementById("signupPopup");
const closeSignupPopup = document.getElementById("closeSignupPopup");
const popupTitle = document.getElementById("popupTitle");
const popupText = document.getElementById("popupText");

// Function used to show the popup with a custom title and message
function showSignupPopup(title, text) {
    popupTitle.textContent = title;
    popupText.textContent = text;
    signupPopup.style.display = "flex";
}

// This runs when the user submits the sign-up form
signupForm.addEventListener("submit", function(event) {

    // Check if any field is empty before sending the form to PHP
    if (
        usernameInput.value.trim() === "" ||
        emailInput.value.trim() === "" ||
        passwordInput.value.trim() === "" ||
        confirmPasswordInput.value.trim() === ""
    ) {
        // Stop the form from submitting
        event.preventDefault();

        // Show missing information popup
        showSignupPopup("Missing Information", "Please fill in all fields.");

    // Check if the password and confirm password do not match
    } else if (passwordInput.value !== confirmPasswordInput.value) {

        // Stop the form from submitting
        event.preventDefault();

        // Show password error popup
        showSignupPopup("Password Error", "Passwords do not match. Please try again.");
    }
});

// Close the popup when the Okay button is clicked
closeSignupPopup.addEventListener("click", function() {
    signupPopup.style.display = "none";
});

// Close the popup when the user clicks the dark background outside the popup box
signupPopup.addEventListener("click", function(event) {
    if (event.target === signupPopup) {
        signupPopup.style.display = "none";
    }
});

<?php
// If PHP detected an error after submitting the form,
// print JavaScript code that shows the popup with the PHP error message
if ($show_popup == true) {
    echo "showSignupPopup(" . json_encode($popup_title) . ", " . json_encode($popup_text) . ");";
}
?>
</script>

</body>
</html>