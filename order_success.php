<?php
// ==============================================
// Developed by: Fatima Alsayed
// Order Success page with receipt and sharing system
// ==============================================

session_start();

// Connect to database
include "db_connect.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {

    // Redirect user to login page
    header("Location: login.php");
    exit();
}

// Store user information from session
$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["username"] ?? "Customer";

// Generate random order ID
$order_id = rand(1000, 9999);

/* ==============================================
   Get user's cart information
============================================== */

// SQL query to get cart data
$cart_sql = "SELECT * FROM cart WHERE user_id = '$user_id'";

// Execute query
$cart_result = mysqli_query($conn, $cart_sql);

// Default cart ID
$cart_id = 0;

// Check if user has a cart
if (mysqli_num_rows($cart_result) > 0) {

    // Fetch cart data
    $cart = mysqli_fetch_assoc($cart_result);

    // Save cart ID
    $cart_id = $cart["cart_id"];
}

/* ==============================================
   Add purchased games to user library
============================================== */

if ($cart_id != 0) {

    // Insert purchased games into user library
    $add_library_sql = "INSERT IGNORE INTO user_library (user_id, game_id)
                        SELECT '$user_id', game_id
                        FROM cart_items
                        WHERE cart_id = '$cart_id'";

    // Execute insert query
    mysqli_query($conn, $add_library_sql);
}

/* ==============================================
   Get cart items for receipt before clearing cart
============================================== */

// Array to store purchased items
$items = [];

// Variable to store total price
$total = 0;

if ($cart_id != 0) {

    // Query to get game information
    $items_sql = "SELECT games.title, games.price, games.discount_percent, games.image
                  FROM cart_items
                  JOIN games ON cart_items.game_id = games.game_id
                  WHERE cart_items.cart_id = '$cart_id'";

    // Execute query
    $items_result = mysqli_query($conn, $items_sql);

    // Loop through each item
    while ($item = mysqli_fetch_assoc($items_result)) {

        // Check if game has discount
        if ($item["discount_percent"] > 0) {

            // Calculate discounted price
            $final_price = $item["price"] - ($item["price"] * ($item["discount_percent"] / 100));

        } else {

            // Original price if no discount
            $final_price = $item["price"];
        }

        // Save final price inside array
        $item["final_price"] = $final_price;

        // Add item to items array
        $items[] = $item;

        // Add price to total
        $total = $total + $final_price;
    }
}
/* Cookie: save past purchases for returning customers */
$past_purchases = [];

foreach ($items as $purchased_item) {
    $past_purchases[] = $purchased_item["title"];
}

if (count($past_purchases) > 0) {
    setcookie("past_purchases", json_encode($past_purchases), time() + (86400 * 30), "/");
}

/* ==============================================
   Clear cart after saving receipt data
============================================== */

if ($cart_id != 0) {

    // Delete all cart items
    $clear_cart_sql = "DELETE FROM cart_items WHERE cart_id = '$cart_id'";

    // Execute delete query
    mysqli_query($conn, $clear_cart_sql);
}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<!-- Page title -->
<title>Order Success | Digital Game Store</title>

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
    <a href="Join_Team.php">Join Team</a>

    <!-- Logout link -->
    <a href="logout.php" class="logout-link">Logout</a>
</nav>

<!-- Main container -->
<div class="container">

<!-- Order summary card -->
<div class="card" id="order-summary" style="max-width:650px; margin:40px auto; text-align:center;">

    <!-- Success title -->
    <h1 style="color:#bfa67a;">Order Confirmed!</h1>

    <!-- Thank you message -->
    <p style="font-size:18px;">
        Thank you, <strong><?php echo $user_name; ?></strong>!
    </p>

    <p>Your order has been placed successfully.</p>

    <hr style="border:1px solid #4a475e; margin:25px 0;">

    <!-- Display order ID -->
    <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>

    <!-- Receipt title -->
    <h2 style="margin-top:25px;">Receipt</h2>

    <?php
    // Check if receipt contains items
    if (count($items) > 0) {

        // Loop through purchased items
        foreach ($items as $item) {
    ?>

    <!-- Item card -->
    <div style="
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:15px;
        border:1px solid #4a475e;
        padding:12px;
        margin:12px 0;
        border-radius:10px;
        text-align:left;
    ">

        <!-- Game image and info -->
        <div style="display:flex; align-items:center; gap:15px;">

            <!-- Game image -->
            <img src="<?php echo $item['image']; ?>"
                 alt="<?php echo $item['title']; ?>"
                 style="width:70px; height:85px; object-fit:cover; border:2px solid #4a475e; border-radius:8px;">

            <!-- Game title -->
            <div>
                <h3 style="margin:0;"><?php echo $item["title"]; ?></h3>
                <p style="margin:5px 0 0;">Digital Game</p>
            </div>
        </div>

        <!-- Price section -->
        <strong>

            <?php
            // Check if game has discount
            if ($item["discount_percent"] > 0) {

                // Original price
                echo "<span style='text-decoration:line-through; color:#d1d1e0;'>$" . number_format($item["price"], 2) . "</span><br>";

                // Discount percentage
                echo "<span style='color:lime;'>" . number_format($item["discount_percent"], 0) . "% Discount</span><br>";

                // Final discounted price
                echo "<span style='color:lime;'>$" . number_format($item["final_price"], 2) . "</span>";

            } else {

                // Normal price without discount
                echo "$" . number_format($item["final_price"], 2);
            }
            ?>

        </strong>
    </div>

    <?php
        }

    } else {

        // Message if no items exist
        echo "<p>No items found in this order.</p>";
    }
    ?>

    <hr style="border:1px solid #4a475e; margin:25px 0;">

    <!-- Display total price -->
    <h2>Total: $<?php echo number_format($total, 2); ?></h2>

    <!-- Action buttons -->
    <div style="display:flex; justify-content:center; gap:15px; flex-wrap:wrap; margin-top:25px;">

        <!-- Print receipt button -->
        <button onclick="window.print()" class="btn">
            Print Receipt
        </button>

        <!-- Share order button -->
        <button id="shareButton" class="btn">
            Share Order Details
        </button>

        <!-- Back to store button -->
        <a href="home.php" class="btn">
            Back to Store
        </a>

    </div>

</div>

</div>

<!-- Footer -->
<footer style="position:fixed; bottom:0; width:100%; text-align:center; padding:15px; background-color:#24222f; border-top:2px solid #4a475e; color:#d1d1e0; font-size:14px;">

    © 2026 Digital Game Store | All Rights Reserved

</footer>

<!-- JavaScript share feature -->
<script>

// Share information object
const shareData = {

    title: "Order Summary",

    text: "My Order ID is: #<?php echo $order_id; ?> | Total: $<?php echo number_format($total, 2); ?>",

    url: window.location.href
};

// Select share button
const btn = document.querySelector("#shareButton");

// Add click event
btn.addEventListener("click", async function() {

    // Check if browser supports sharing
    if (navigator.share) {

        try {

            // Open native share menu
            await navigator.share(shareData);

        } catch (err) {

            // Error or cancellation message
            console.log("Share cancelled or failed.");
        }

    } else {

        // Browser unsupported message
        alert("Sharing is not supported on this browser.");
    }
});

</script>

<!-- Logout popup -->
<?php include "logout_popup.php"; ?>

</body>
</html>
