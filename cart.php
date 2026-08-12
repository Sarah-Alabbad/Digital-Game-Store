<!-- Cart page - Developed by Zahra AL-mari -->

<?php

// Start the session so we can check which user is logged in
session_start();

// Connect this page to the database
include "db_connect.php";

/* Check login */

// Check if the user is logged in
// If not, send them back to the login page
if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();
}

// Store the logged-in user's ID in a variable
$user_id = $_SESSION["user_id"];

/* Get user cart */

// Get the cart that belongs to the logged-in user
$cart_sql = "SELECT * FROM cart WHERE user_id = '$user_id'";
$cart_result = mysqli_query($conn, $cart_sql);

// If no cart exists for this user, stop the page and show a message
if (mysqli_num_rows($cart_result) == 0) {

    die("Cart not found.");
}

// Get the cart row from the database result
$cart = mysqli_fetch_assoc($cart_result);

// Store the cart ID so we can use it to get cart items
$cart_id = $cart["cart_id"];

/* Remove game from cart */

// Check if the URL has a remove value
// Example: cart.php?remove=3
if (isset($_GET["remove"])) {

    // Store the cart item ID that should be removed
    $remove_id = $_GET["remove"];

    // Delete the selected item from the cart_items table
    $delete_sql = "DELETE FROM cart_items 
                   WHERE cart_item_id = '$remove_id'";

    mysqli_query($conn, $delete_sql);

    // Reload the cart page after removing the item
    // This prevents the remove action from repeating if the user refreshes
    header("Location: cart.php");
    exit();
}

/* Get cart items */

// Get all games inside this user's cart
// The JOIN is used because cart_items has the game_id,
// while the games table has the title, price, discount, and image
$items_sql = "SELECT 
              cart_items.cart_item_id,
              games.title,
              games.price,
              games.discount_percent,
              games.image
              FROM cart_items
              JOIN games ON cart_items.game_id = games.game_id
              WHERE cart_items.cart_id = '$cart_id'";

// Run the query that gets cart items
$items_result = mysqli_query($conn, $items_sql);

/* Total price */

// Start the total price from 0
$total = 0;
?>

<!DOCTYPE html>
<html>
<head>

<!-- Set the character encoding for the page -->
<meta charset="UTF-8">

<!-- Page title shown in the browser tab -->
<title>My Cart | Digital Game Store</title>

<!-- Link the external CSS file -->
<link rel="stylesheet" href="Style/style.css">

</head>

<body>

<!-- Navigation Bar -->
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

<!-- Page heading -->
<h1 style="text-align:center;">My Cart</h1>

<!-- Main layout
     Left side shows cart items, right side shows order summary -->
<div style="
display:grid;
grid-template-columns:2fr 1fr;
gap:30px;
align-items:flex-start;
">

<!-- Cart Items -->
<div class="card">

<?php
/* Loop through all cart items */

// Go through each item found in the cart
while ($item = mysqli_fetch_assoc($items_result)) {

    /* Roaa discount system */

    // Check if the game has a discount
    if ($item["discount_percent"] > 0) {

        // Calculate price after discount
        $final_price = $item["price"] - ($item["price"] * ($item["discount_percent"] / 100));

    } else {

        // If there is no discount, use the original game price
        $final_price = $item["price"];
    }

    /* Add to total */

    // Add this game's final price to the total cart price
    $total = $total + $final_price;
?>

<!-- One cart item box -->
<div class="cart-item" style="
display:flex;
align-items:center;
justify-content:space-between;
gap:20px;
padding:18px;
margin-bottom:15px;
border:2px solid #4a475e;
background-color:#24222f;
">

<!-- Left Side -->
<div style="display:flex; align-items:center; gap:18px;">

<!-- Game image -->
<img src="<?php print $item['image']; ?>" 
     alt="<?php print $item['title']; ?> Cover"
     style="
     width:90px;
     height:110px;
     object-fit:cover;
     border:2px solid #4a475e;
     ">

<div>

<!-- Game title -->
<h3 style="margin:0 0 12px 0; font-size:18px;">
<?php print $item["title"]; ?>
</h3>

<!-- Remove Link -->
<!-- Sends the cart_item_id in the URL so PHP can remove this item -->
<a href="cart.php?remove=<?php print $item['cart_item_id']; ?>"
   style="
   color:#bfa67a;
   text-decoration:none;
   font-size:14px;
   transition:0.2s;
   "
   onmouseover="this.style.color='#ff4d4d'"
   onmouseout="this.style.color='#bfa67a'">

Remove

</a>

</div>
</div>


<!-- Right Side -->
<!-- This side shows the price or discount information -->
<div style="
font-size:18px;
font-weight:bold;
">

<?php
/* Show discounted price */

// If the game has a discount, show old price, discount percentage, and final price
if ($item["discount_percent"] > 0) {

    print "<span style='text-decoration:line-through; color:#d1d1e0;'>$" .
    number_format($item["price"], 2) .
    "</span><br>";

    print "<span style='color:lime;'>" .
    number_format($item["discount_percent"], 0) .
    "% Discount</span><br>";

    print "<span style='color:lime;'>$" .
    number_format($final_price, 2) .
    "</span>";

} else {

    // If there is no discount, show only the normal final price
    print "$" . number_format($final_price, 2);
}
?>

</div>

</div>

<?php
}
?>

</div>


<!-- Order Summary -->
<div class="card" style="height:fit-content;">

<!-- Summary heading -->
<h2 style="text-align:center;">Order Summary</h2>

<!-- Subtotal row -->
<div style="
display:flex;
justify-content:space-between;
margin-top:25px;
font-size:18px;
">

<span>Subtotal</span>

<span>
$<?php print number_format($total, 2); ?>
</span>

</div>


<!-- Line divider between subtotal and the rest of the summary -->
<hr style="
margin:25px 0;
border:1px solid #4a475e;
">


<!-- SAR Conversion -->
<!-- Shows the approximate price in Saudi Riyal using 1 USD = 3.75 SAR -->
<div style="
background:#1d1b28;
padding:18px;
border:1px solid #4a475e;
text-align:center;
margin-bottom:25px;
">

<p style="color:#bfa67a;">
Approximate total in Saudi Riyal
</p>

<h2>
<?php print number_format($total * 3.75, 2); ?> SAR
</h2>

</div>


<!-- Final Total -->
<div style="
display:flex;
justify-content:space-between;
font-size:20px;
font-weight:bold;
margin-bottom:25px;
">

<span>Total</span>

<span>
$<?php print number_format($total, 2); ?>
</span>

</div>



<!-- Checkout Button -->
<!-- Sends the user to the checkout page to finish payment -->
<a href="checkout.php" class="btn" style="
width:100%;
padding:16px;
font-size:18px;
display:block;
text-align:center;
box-sizing:border-box;
text-decoration:none;
">
    Proceed to Checkout
</a>

</div>

</div>

</div>


<!-- Footer -->
<footer style="
position:fixed;
bottom:0;
width:100%;
text-align:center;
padding:15px;
background-color:#24222f;
border-top:2px solid #4a475e;
color:#d1d1e0;
font-size:14px;
">

© 2026 Digital Game Store | All Rights Reserved

</footer>

<!-- Include the logout confirmation popup file -->
<?php include "logout_popup.php"; ?>

</body>
</html>