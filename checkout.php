<!-- Cheak out page - Developed by Zahra AL-mari -->

<?php
// Start the session so we can use session variables like user_id
session_start();

// Connect this page to the database
include "db_connect.php";

// Check if the user is logged in
// If there is no user_id in the session, send the user back to the login page
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Store the logged-in user's ID in a variable
$user_id = $_SESSION["user_id"];

/* Find user's cart */

// Select the cart that belongs to the logged-in user
$cart_sql = "SELECT * FROM cart WHERE user_id = '$user_id'";
$cart_result = mysqli_query($conn, $cart_sql);

// Default cart_id value
// It will stay 0 if the user does not have a cart
$cart_id = 0;

// If the user has a cart, get the cart information
if (mysqli_num_rows($cart_result) > 0) {
    $cart = mysqli_fetch_assoc($cart_result);

    // Save the user's cart_id so we can use it to find cart items
    $cart_id = $cart["cart_id"];
}

/* Calculate total */

// Get the price and discount for every game inside the user's cart
// The JOIN is used because cart_items stores the game_id, while games stores the price
$items_sql = "SELECT games.price, games.discount_percent
              FROM cart_items
              JOIN games ON cart_items.game_id = games.game_id
              WHERE cart_items.cart_id = '$cart_id'";

$items_result = mysqli_query($conn, $items_sql);

// Start the total price from 0
$total = 0;

// Count how many items are inside the cart
$item_count = mysqli_num_rows($items_result);

// Loop through each cart item to calculate the final total
while ($item = mysqli_fetch_assoc($items_result)) {

    // If the game has a discount, calculate the discounted price
    if ($item["discount_percent"] > 0) {
        $final_price = $item["price"] - ($item["price"] * ($item["discount_percent"] / 100));
    } else {
        // If there is no discount, use the normal game price
        $final_price = $item["price"];
    }

    // Add the final price of this game to the total amount
    $total = $total + $final_price;
}

// If the cart is empty, send the user back to the cart page
if ($item_count == 0) {
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<!-- Set the character encoding for the page -->
<meta charset="UTF-8">

<!-- Page title shown in the browser tab -->
<title>Checkout | Digital Game Store</title>

<!-- Link the external CSS file for styling -->
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

<!-- Checkout card that holds the payment form -->
<div class="card" style="max-width:500px; margin:40px auto;">

<h1>Checkout</h1>

<!-- Display the total amount the user needs to pay -->
<p style="text-align:center; font-size:18px;">
    Total Amount: <strong>$<?php echo number_format($total, 2); ?></strong>
</p>

<!-- Payment form
     The form submits to order_success.php after JavaScript validation passes -->
<form id="paymentForm" action="order_success.php" method="POST" novalidate>

    <!-- Input where the user enters the payment amount -->
    <label>Enter Payment Amount</label>
    <input type="number" id="paymentAmount" placeholder="Enter your money amount" step="1" min="0">

    <!-- Button used to submit the payment form -->
    <button type="submit" class="btn" style="display:block; margin:25px auto 0;">
        Pay Now
    </button>

</form>

<!-- Message to explain that this is only a demo payment -->
<p style="font-size:13px; text-align:center; margin-top:20px; color:#bfa67a;">
    This is a demo payment. No real money is used.
</p>

</div>

</div>

<!-- Website footer fixed at the bottom of the page -->
<footer style="position:fixed; bottom:0; width:100%; text-align:center; padding:15px; background-color:#24222f; border-top:2px solid #4a475e; color:#d1d1e0; font-size:14px;">
    © 2026 Digital Game Store | All Rights Reserved
</footer>

<!-- Payment popup box
     It starts hidden and appears when there is an error or successful payment -->
<div id="paymentPopup" style="
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

    <!-- Inner popup content box -->
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
        <!-- Popup title changes depending on the message -->
        <h2 id="paymentPopupTitle" style="color:#bfa67a; margin-top:0;">Payment Error</h2>

        <!-- Popup message text changes depending on the problem/success -->
        <p id="paymentPopupText" style="font-size:16px; margin:20px 0;">
            Message here.
        </p>

        <!-- Button to close the popup or confirm the payment -->
        <button id="closePaymentPopup" class="btn">Okay</button>
    </div>

</div>

<script>
// Get the payment form from the page
const paymentForm = document.getElementById("paymentForm");

// Get the payment amount input field
const paymentAmount = document.getElementById("paymentAmount");

// Get the popup elements from the page
const paymentPopup = document.getElementById("paymentPopup");
const paymentPopupTitle = document.getElementById("paymentPopupTitle");
const paymentPopupText = document.getElementById("paymentPopupText");
const closePaymentPopup = document.getElementById("closePaymentPopup");

// Store the PHP total amount inside JavaScript so it can be checked before submitting
const totalAmount = <?php echo $total; ?>;

// This controls whether the form is allowed to submit
// At first it is false, so JavaScript can validate the payment first
let allowSubmit = false;

// Function used to show the popup with a custom title and message
function showPaymentPopup(title, text) {
    paymentPopupTitle.textContent = title;
    paymentPopupText.textContent = text;
    paymentPopup.style.display = "flex";
}

// This runs when the user clicks Pay Now
paymentForm.addEventListener("submit", function(event) {

    // If payment was already accepted, allow the form to submit normally
    if (allowSubmit === true) {
        return;
    }

    // Stop the form from submitting immediately
    // This gives JavaScript time to check the payment amount first
    event.preventDefault();

    // Convert the entered amount from text to a number
    const paidAmount = parseFloat(paymentAmount.value);

    // Check if the user left the input empty or entered an invalid number
    if (paymentAmount.value.trim() === "" || isNaN(paidAmount)) {
        showPaymentPopup("Missing Payment", "Please enter a payment amount.");
        return;
    }

    // Check if the amount entered is less than the cart total
    if (paidAmount < totalAmount) {
        showPaymentPopup(
            "Not Enough Money",
            "Your payment is not enough. Total is $" + totalAmount.toFixed(2) + "."
        );
        return;
    }

    // If the amount is enough, show a success message
    showPaymentPopup("Payment Accepted", "Payment successful. Click Okay to confirm your order.");

    // Set this to true so the form can submit after the user clicks Okay
    allowSubmit = true;
});

// This runs when the user clicks the Okay button inside the popup
closePaymentPopup.addEventListener("click", function() {

    // If payment was accepted, submit the form to order_success.php
    if (allowSubmit === true) {
        paymentForm.submit();
    } else {
        // If there was an error, just close the popup
        paymentPopup.style.display = "none";
    }
});

// This lets the user close the popup by clicking outside the popup box
paymentPopup.addEventListener("click", function(event) {

    // Only close it when the user clicks the dark background
    // Also, only allow this if payment has not been accepted yet
    if (event.target === paymentPopup && allowSubmit === false) {
        paymentPopup.style.display = "none";
    }
});
</script>

<!-- Include the logout confirmation popup file -->
<?php include "logout_popup.php"; ?>

</body>
</html>