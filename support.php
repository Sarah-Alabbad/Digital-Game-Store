<!-- Shahad Aldawsari-->

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Support | Digital Game Store</title>
<link rel="stylesheet" href="Style/style.css">
</head>

<body>

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

<div class="card">
    <h1>Support</h1>

<?php if (isset($_GET['success'])): ?>
<p style="color: green; text-align: center;">Submitted successfully! Thank you for your patience</p>
<?php endif; ?>

    <form action="process-support.php" method="post" novalidate>

        <label>Full Name</label>
        <input type="text" name="full_name" placeholder="Enter your name">

        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter your email">

        <label>Issue Type</label>
        <select name="issue_type">
            <option value="Payment Problem">Payment Problem</option>
            <option value="Game Not Working">Game Not Working</option>
            <option value="Refund Request">Refund Request</option>
            <option value="Account Issue">Account Issue</option>
            <option value="Other">Other</option>
        </select>

        <label>Message</label>
        <textarea id="messageBox" name="message" rows="5" placeholder="Describe your issue" maxlength="200"></textarea>

        <p id="charCount" style="text-align: right; font-size: 14px; color: #4f9a94;">
            0 / 200 characters
        </p>

        <button type="submit" class="btn" style="display:block; margin:20px auto 0;">
            Submit
        </button>

    </form>
</div>

<div class="card contact-links">
    <h2>Contact Us</h2>
    <p>📞 Phone: <a href="tel:0551234567">055-123-4567</a></p>
    <p>📧 Email: <a href="mailto:support@gamestore.com">support@gamestore.com</a></p>
</div>

</div>

<footer style="position:fixed; bottom:0; width:100%; text-align:center; padding:15px; background-color:#24222f; border-top:2px solid #4a475e; color:#d1d1e0; font-size:14px;">
    © 2026 Digital Game Store | All Rights Reserved
</footer>

<div id="supportPopup" style="
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
        width:380px;
        text-align:center;
        box-shadow:0 0 20px rgba(0,0,0,0.5);
    ">
        <h2 id="supportPopupTitle" style="color:#bfa67a; margin-top:0;">Support Message</h2>

        <p id="supportPopupText" style="font-size:16px; margin:20px 0;">
            Message text goes here.
        </p>

        <button id="closeSupportPopup" class="btn">Okay</button>
    </div>

</div>

<script>

     document.addEventListener("DOMContentLoaded", function() {
    
    // Grabbing our text area and counter element
    const messageBox = document.getElementById("messageBox");
    const charCount = document.getElementById("charCount");

    // Grabbing form elements so we can validate their inputs
    const supportForm = document.querySelector("form");
    const nameInput = document.querySelector('input[name="full_name"]');
    const emailInput = document.querySelector('input[name="email"]');
    const issueInput = document.querySelector('select[name="issue_type"]');

    // Grabbing all the popup components
    const supportPopup = document.getElementById("supportPopup");
    const supportPopupTitle = document.getElementById("supportPopupTitle");
    const supportPopupText = document.getElementById("supportPopupText");
    const closeSupportPopup = document.getElementById("closeSupportPopup");

    // Flag to track if the user has passed all checks and confirmed submission
    let allowSubmit = false;

    // Helper function to quickly pop open our custom modal with whatever title/text we need
    function showSupportPopup(title, text) {
        supportPopupTitle.textContent = title;
        supportPopupText.textContent = text;
        supportPopup.style.display = "flex"; // Changes from 'none' to 'flex' so the modal centers nicely
    }

    //  Live Character Counter 
    if (messageBox && charCount) {
        messageBox.addEventListener("input", function() {
            const currentLength = messageBox.value.length;
            
            // Update the text to show current count live as they type
            charCount.textContent = currentLength + " / 200 characters";

          
        });
    }

    //  Form Validation & Submission Logic
    if (supportForm) {
        supportForm.addEventListener("submit", function(event) {
            const fullName = nameInput.value.trim();
            const email = emailInput.value.trim();
            const message = messageBox.value.trim();

            // If they already passed all checks and clicked 'Okay' on the confirmation popup, let the submission go through

            if (allowSubmit === true) {
                return;
            }

            //Stop the form from submitting immediately so we can run our checks.
            event.preventDefault();

            //Check for empty fields
            if (fullName === "" || email === "" || message === "") {
                showSupportPopup("Missing Information", "Please fill in all fields before submitting.");
                return;
            }

            //make sure they entered at least a first and last name (looking for a space)
            if (!fullName.includes(" ")) {
                showSupportPopup("Full Name Required", "Please enter your first and last name.");
                nameInput.focus();
                return;
            }

            //email validation. Just making sure it looks like an email format.
            if (!email.includes("@") || !email.includes(".")) {
                showSupportPopup("Invalid Email", "Please enter a valid email address.");
                emailInput.focus();
                return;
            }

            // If they survived all the checks, show them the final confirmation popup
            showSupportPopup("Submit Ticket?", "Thank you " + fullName + ". Click Okay to submit your support ticket.");
            
            // Flip the flag to true so the next time 'submit' is triggered, it actually goes to process-support.php
            allowSubmit = true;
        });
    }

    //  Handling Popup Clicks

    closeSupportPopup.addEventListener("click", function() {
        if (allowSubmit === true) {
            // They clicked 'Okay' on the final confirmation, so send it! 
            supportForm.submit();
        } else {
            //just an error message, so just close the popup and let them fix their mistakes
            supportPopup.style.display = "none";
        }
    });

    //close the popup if they click the dark background outside the modal box
    supportPopup.addEventListener("click", function(event) {
        if (event.target === supportPopup && allowSubmit === false) {
            supportPopup.style.display = "none";
        }
    });
});
</script>

<?php include "logout_popup.php"; ?>

</body>
</html>