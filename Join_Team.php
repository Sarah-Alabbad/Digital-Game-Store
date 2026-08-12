<?php
// ==============================================
// Developed by: Fatima Alsayed
// Join Team page with API application system
// ==============================================

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Connect to database
include "db_connect.php";

/* ==============================================
   API Request Handling
============================================== */

// Check if request method is POST and API parameter exists
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['api'])) {

    // Return response as JSON format
    header('Content-Type: application/json');

    // Read JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate received data
    if (!$data) {

        // Stop execution and return error message
        die(json_encode([
            "status" => "error",
            "message" => "No data received."
        ]));
    }

    // Secure user input before inserting into database
    $name = mysqli_real_escape_string($conn, $data['name']);
    $phone = mysqli_real_escape_string($conn, $data['phone']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $cv = mysqli_real_escape_string($conn, $data['cv']);

    // SQL query to insert application data
    $sql = "INSERT INTO applications (full_name, phone, email, cv_link)
            VALUES ('$name', '$phone', '$email', '$cv')";

    // Execute query
    if (mysqli_query($conn, $sql)) {

        // Success response
        echo json_encode([
            "status" => "success"
        ]);

    } else {

        // Error response
        echo json_encode([
            "status" => "error",
            "message" => "SQL Error: " . mysqli_error($conn)
        ]);
    }

    // Stop script after API response
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Page title -->
<title>Join Our Team | Digital Game Store</title>

<!-- External CSS file -->
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
    <a href="about_us.php">About Us</a>

    <!-- Logout button -->
    <a href="logout.php" class="logout-link">Logout</a>
</nav>

<!-- Main container -->
<div class="container">

    <!-- Main page title -->
    <h1>Join Our Team</h1>

    <!-- Card section -->
    <div class="card" style="max-width:750px; margin:40px auto;">

        <!-- Career section title -->
        <h2>Career Opportunities</h2>

        <!-- Introduction text -->
        <p style="text-align:center;">
            We are always excited to welcome talented individuals. Fill the form below!
        </p>

        <!-- Join button -->
        <div style="text-align:center; margin:25px 0;">

            <button id="joinBtn" class="btn" onclick="showForm()">
                Join Now
            </button>

        </div>

        <!-- Hidden application form -->
        <form id="applicationForm" style="display:none; margin-top:30px;">

            <!-- Full name input -->
            <label>Full Name:</label>
            <input type="text" id="name" required>

            <!-- Phone number input -->
            <label>Phone Number:</label>
            <input type="text" id="phone" required>

            <!-- Email input -->
            <label>Email Address:</label>
            <input type="email" id="email" required>

            <!-- CV link input -->
            <label>CV Link (Google Drive/Dropbox):</label>
            <input type="url" id="cv" required>

            <!-- Submit button -->
            <div style="text-align:center; margin-top:20px;">

                <button type="button" class="btn" onclick="submitApplication()">
                    Submit Application
                </button>

            </div>

            <!-- Response message -->
            <p id="responseMsg" style="text-align:center; margin-top:20px; font-weight:bold;"></p>

        </form>

    </div>

</div>

<!-- Footer -->
<footer style="position:fixed; bottom:0; width:100%; text-align:center; padding:15px; background-color:#24222f; border-top:2px solid #4a475e; color:#d1d1e0; font-size:14px;">

    © 2026 Digital Game Store | All Rights Reserved

</footer>

<script>

/* ==============================================
   Show Application Form
============================================== */

function showForm() {

    // Display form
    document.getElementById('applicationForm').style.display = 'block';

    // Hide join button
    document.getElementById('joinBtn').style.display = 'none';
}

/* ==============================================
   Submit Application Using Fetch API
============================================== */

async function submitApplication() {

    // Get user input values
    const name = document.getElementById('name').value;
    const phone = document.getElementById('phone').value;
    const email = document.getElementById('email').value;
    const cv = document.getElementById('cv').value;

    // Response message element
    const msg = document.getElementById('responseMsg');

    // Validate empty fields
    if (!name || !phone || !email || !cv) {

        msg.innerText = "Please fill in all fields!";
        msg.style.color = "#ff6b6b";

        return;
    }

    // Sending message
    msg.innerText = "Sending...";
    msg.style.color = "#bfa67a";

    try {

        // Send request to API
        const response = await fetch('Join_Team.php?api=1', {

            method: 'POST',

            headers: {
                'Content-Type': 'application/json'
            },

            // Convert data into JSON format
            body: JSON.stringify({
                name: name,
                phone: phone,
                email: email,
                cv: cv
            })
        });

        // Check if response failed
        if (!response.ok) {

            throw new Error('Network or Server Error');
        }

        // Convert response to JSON
        const result = await response.json();

        // Check success response
        if (result.status === 'success') {

            // Success message
            msg.innerText = "Application sent successfully.";

            msg.style.color = "#3e8e5b";

            // Reset form fields
            document.getElementById('applicationForm').reset();

        } else {

            // Error message
            msg.innerText = "Error: " + (result.message || "Unknown failed.");

            msg.style.color = "#ff6b6b";

            console.error(result.message);
        }

    } catch (error) {

        // Connection error message
        msg.innerText = "Connection error. Check Console.";

        msg.style.color = "#ff6b6b";

        console.error('Fetch error:', error);
    }
}

</script>

<!-- Include logout popup -->
<?php include "logout_popup.php"; ?>

</body>
</html>