<!-- Eidt Profile page - Developed by Zahra AL-mari -->

<?php
// Start the session so we can access the logged-in user's data
session_start();

// Connect this page to the database
include "db_connect.php";

// Check if the user is logged in
// If not, send the user back to the login page
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Store the logged-in user's ID in a variable
$user_id = $_SESSION["user_id"];

// This variable will store any error message shown on the page
$message = "";

// Get the current user's information from the users table
$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the new profile information from the form
    $username = $_POST["username"];
    $email = $_POST["email"];
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Keep the old profile image by default
    // It will only change if the user uploads a new image or uses Gravatar
    $profile_image = $user["profile_image"];

    // Get the Gravatar URL from the hidden input if the user chooses Gravatar
    $gravatar_url = $_POST["gravatar_url"];

    // If the user clicked Use Gravatar, save the Gravatar URL as their profile image
    if (!empty($gravatar_url)) {
        $profile_image = $gravatar_url;
    }

    // Check if the user uploaded a new profile image
    if (!empty($_FILES["profile_image"]["name"])) {

        // Folder where profile images will be saved
        $folder = "Images/";

        // If the Images folder does not exist, create it
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        // Create a unique file name using the current time
        $file_name = time() . "_" . basename($_FILES["profile_image"]["name"]);

        // Save the full image path
        $profile_image = $folder . $file_name;

        // Move the uploaded image from temporary storage to the Images folder
        if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $profile_image)) {
            $message = "Profile image upload failed.";
        }
    }

    // Continue only if there is no upload error message
    if ($message == "") {

        // Check if the user wants to change their password
        if (!empty($new_password)) {

            // The user must enter the current password before changing it
            if (empty($current_password)) {
                $message = "Please enter your current password to change your password.";

            // Check if the current password is correct
            } else if (!password_verify($current_password, $user["password"])) {
                $message = "Current password is incorrect.";

            // Check if the new password and confirm password match
            } else if ($new_password != $confirm_password) {
                $message = "New password and confirm password do not match.";

            } else {

                // Hash the new password before saving it in the database
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update username, email, password, and profile image
                $update_sql = "UPDATE users 
                               SET username = '$username',
                                   email = '$email',
                                   password = '$hashed_password',
                                   profile_image = '$profile_image'
                               WHERE user_id = '$user_id'";
            }

        } else {

            // If password is not being changed, update only username, email, and profile image
            $update_sql = "UPDATE users 
                           SET username = '$username',
                               email = '$email',
                               profile_image = '$profile_image'
                           WHERE user_id = '$user_id'";
        }

        // If there are no errors, run the update query
        if ($message == "") {
            if (mysqli_query($conn, $update_sql)) {

                // Update the username stored in the session
                $_SESSION["username"] = $username;

                // Send the user back to the profile page after saving changes
                header("Location: profile.php");
                exit();

            } else {

                // Show database error if the update query fails
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<!-- Set the character encoding for the page -->
<meta charset="UTF-8">

<!-- Make the page responsive on different screen sizes -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Page title shown in the browser tab -->
<title>Edit Profile | Digital Game Store</title>

<!-- Link the external CSS file -->
<link rel="stylesheet" href="Style/style.css">
</head>

<body>

<!-- Navigation bar for moving between website pages -->
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

<!-- Main page container -->
<div class="container">

<!-- Card that contains the edit profile form -->
<div class="card">

<h1>Edit Profile</h1>

<?php
// If there is an error message, display it above the form
if ($message != "") {
    echo "<p style='color:red; text-align:center;'>$message</p>";
}
?>

<!-- Edit profile form
     enctype is needed because the form can upload an image file -->
<form method="POST" action="" enctype="multipart/form-data">

<!-- Username input filled with the current username -->
<label>Username</label>
<input type="text" name="username" value="<?php echo $user['username']; ?>" required>

<!-- Email input filled with the current email -->
<label>Email</label>
<input type="email" name="email" value="<?php echo $user['email']; ?>" required>

<!-- Gravatar option
     This lets the user use an online avatar based on their email -->
<button type="button" id="useGravatar" class="btn" style="margin-top:10px;">
    Use Gravatar from Email
</button>

<!-- Hidden input stores the Gravatar image URL so PHP can save it -->
<input type="hidden" name="gravatar_url" id="gravatar_url">

<!-- Preview image for the Gravatar picture -->
<img id="gravatarPreview" 
     src="" 
     alt="Gravatar Preview"
     style="
     display:none;
     width:120px;
     height:120px;
     object-fit:cover;
     border:3px solid #4a475e;
     margin-top:15px;
     ">

<!-- Current password is needed only if the user wants to change password -->
<label>Current Password</label>
<input type="password" name="current_password" placeholder="Enter current password">

<!-- New password input -->
<label>New Password</label>
<input type="password" name="new_password" placeholder="Enter new password">

<!-- Confirm new password input -->
<label>Confirm New Password</label>
<input type="password" name="confirm_password" placeholder="Confirm new password">

<!-- Profile image upload input -->
<label>Profile Picture</label>
<input type="file" name="profile_image">

<!-- Submit button -->
<button type="submit" class="btn" style="margin-top:20px;">Save Changes</button>

</form>

</div>

</div>

<!-- Page footer -->
<footer style="position:fixed; bottom:0; width:100%; text-align:center; padding:15px; background-color:#24222f; border-top:2px solid #4a475e; color:#d1d1e0; font-size:14px;">
    © 2026 Digital Game Store | All Rights Reserved
</footer>

<script>
// Get the profile image input from the page
const imageInput = document.querySelector('input[name="profile_image"]');

// Check that the image input exists before adding the event
if (imageInput) {

    // This runs when the user chooses an image file
    imageInput.addEventListener("change", function () {

        // Get the selected file
        const file = this.files[0];

        // Continue only if the user selected a file
        if (file) {

            // FileReader is used to preview the image before uploading
            const reader = new FileReader();

            // This runs after the image file is loaded
            reader.onload = function (e) {

                // Try to find an existing preview image
                let preview = document.getElementById("profilePreview");

                // If the preview image does not exist, create it
                if (!preview) {
                    preview = document.createElement("img");
                    preview.id = "profilePreview";
                    preview.style.width = "120px";
                    preview.style.height = "120px";
                    preview.style.objectFit = "cover";
                    preview.style.border = "3px solid #4a475e";
                    preview.style.marginTop = "15px";
                    preview.style.display = "block";

                    // Place the preview image after the file input
                    imageInput.insertAdjacentElement("afterend", preview);
                }

                // Show the selected image inside the preview
                preview.src = e.target.result;
            };

            // Read the selected image file as a temporary browser URL
            reader.readAsDataURL(file);
        }
    });
}
</script>

<script>
// This function creates a SHA-256 hash from the user's email
// Gravatar uses the hashed email to find the user's avatar
async function sha256(text) {
    const encoder = new TextEncoder();
    const data = encoder.encode(text.trim().toLowerCase());
    const hashBuffer = await crypto.subtle.digest("SHA-256", data);
    const hashArray = Array.from(new Uint8Array(hashBuffer));

    return hashArray.map(function(byte) {
        return byte.toString(16).padStart(2, "0");
    }).join("");
}

// Get the Gravatar button and related elements
const useGravatar = document.getElementById("useGravatar");
const gravatarEmailInput = document.querySelector('input[name="email"]');
const gravatarUrlInput = document.getElementById("gravatar_url");
const gravatarPreview = document.getElementById("gravatarPreview");

// When the user clicks the Gravatar button, create and preview the Gravatar image
if (useGravatar) {
    useGravatar.addEventListener("click", async function() {

        // Get the email typed in the email input
        const email = gravatarEmailInput.value.trim();

        // If email is empty, warn the user
        if (email === "") {
            alert("Please enter your email first.");
            return;
        }

        // Create hash from the email
        const hash = await sha256(email);

        // Create the Gravatar image URL
        // s=120 controls image size
        // d=identicon gives a generated avatar if the email has no Gravatar
        // r=pg keeps the image rating safe
        const gravatarUrl = "https://www.gravatar.com/avatar/" + hash + "?s=120&d=identicon&r=pg";

        // Save the URL in the hidden input so PHP can store it
        gravatarUrlInput.value = gravatarUrl;

        // Show the preview image
        gravatarPreview.src = gravatarUrl;
        gravatarPreview.style.display = "block";
    });
}
</script>

<!-- Save confirmation popup
     It starts hidden and appears when the user clicks Save Changes -->
<div id="savePopup" style="
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
        <h2 style="color:#bfa67a; margin-top:0;">Save Changes?</h2>

        <!-- Popup message -->
        <p style="font-size:16px; margin:20px 0;">
            Are you sure you want to save your profile changes?
        </p>

        <!-- Popup buttons -->
        <div style="display:flex; justify-content:center; gap:15px;">
            <button id="cancelSave" class="btn">Cancel</button>
            <button id="confirmSave" class="btn">Save</button>
        </div>
    </div>

</div>

<script>
// Get the edit profile form and popup elements
const editForm = document.querySelector("form");
const savePopup = document.getElementById("savePopup");
const cancelSave = document.getElementById("cancelSave");
const confirmSave = document.getElementById("confirmSave");

// This variable controls whether the form is allowed to submit
let allowSubmit = false;

// When the user submits the form, show the confirmation popup first
editForm.addEventListener("submit", function(event) {

    // If allowSubmit is false, stop the form and show the popup
    if (!allowSubmit) {
        event.preventDefault();
        savePopup.style.display = "flex";
    }
});

// If the user clicks Cancel, close the popup and do not submit
cancelSave.addEventListener("click", function() {
    savePopup.style.display = "none";
});

// If the user clicks Save, allow the form to submit
confirmSave.addEventListener("click", function() {
    allowSubmit = true;
    editForm.submit();
});

// Close the popup when the user clicks the dark background outside the popup box
savePopup.addEventListener("click", function(event) {
    if (event.target === savePopup) {
        savePopup.style.display = "none";
    }
});
</script>

<!-- Include the logout confirmation popup file -->
<?php include "logout_popup.php"; ?>

</body>
</html>