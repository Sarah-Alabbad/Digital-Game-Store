<?php
/* 
    Digital Game Store
    Modified by Roaa Alhaddad
*/

session_start();
include "db_connect.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid game.");
}

$game_id = $_GET["id"];
$message = "";

/* Get game details */
$sql = "SELECT * FROM games WHERE game_id = '$game_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    die("Game not found.");
}

$game = mysqli_fetch_assoc($result);


/* Add game to cart */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION["user_id"])) {

        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION["user_id"];

    /* Find user cart */
    $cart_sql = "SELECT * FROM cart WHERE user_id = '$user_id'";
    $cart_result = mysqli_query($conn, $cart_sql);

    /* Create cart if not exists */
    if (mysqli_num_rows($cart_result) == 0) {

        $create_cart_sql = "INSERT INTO cart (user_id)
                            VALUES ('$user_id')";

        mysqli_query($conn, $create_cart_sql);

        $cart_id = mysqli_insert_id($conn);

    } else {

        $cart = mysqli_fetch_assoc($cart_result);
        $cart_id = $cart["cart_id"];
    }

    /* Check if game already exists in cart */
    $check_sql = "SELECT * FROM cart_items
                  WHERE cart_id = '$cart_id'
                  AND game_id = '$game_id'";

    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {

        $message = "This game is already in your cart.";

    } else {

        $insert_sql = "INSERT INTO cart_items (cart_id, game_id)
                       VALUES ('$cart_id', '$game_id')";

        if (mysqli_query($conn, $insert_sql)) {

            $message = "Game added to cart successfully!";

        } else {

            $message = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>
<?php print $game["title"]; ?> | Digital Game Store
</title>

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

<div class="card" style="
    display:flex;
    gap:30px;
    align-items:flex-start;
    flex-wrap:wrap;
">

    <!-- Game image -->
    <div style="flex:1; min-width:260px; text-align:center;">

        <img 
            src="<?php print $game['image']; ?>" 
            alt="<?php print $game['title']; ?>"
            style="
                width:280px;
                height:360px;
                object-fit:cover;
                border:3px solid #4a475e;
            "
        >

    </div>


    <!-- Game details -->
    <div style="flex:2; min-width:300px;">

        <h1>
            <?php print $game["title"]; ?>
        </h1>

        <p>
            <strong>Genre:</strong>
            <?php print $game["genre"]; ?>
        </p>


        <!-- Discount logic added by Roaa -->
        <?php
        $price = $game["price"];
        $discount_percent = $game["discount_percent"];

        if ($discount_percent > 0) {
            $final_price = $price - ($price * ($discount_percent / 100));
        ?>

            <p>
                <strong>Price:</strong>
                <span style="text-decoration:line-through;">
                    $<?php echo number_format($price, 2); ?>
                </span>
                <br>

                <strong>Discount:</strong>
                <span style="color:lime; font-weight:bold;">
                    <?php echo number_format($discount_percent, 0); ?>%
                </span>
                <br>

                <strong>Final Price:</strong>
                <span style="color:lime; font-weight:bold;">
                    $<?php echo number_format($final_price, 2); ?>
                </span>
            </p>

        <?php
        } else {
        ?>

            <p>
                <strong>Price:</strong>
                $<?php echo number_format($price, 2); ?>
            </p>

        <?php
        }
        ?>


        <p>
            <strong>Description:</strong>
        </p>

        <p style="line-height:1.7;">

            <?php print $game["description"]; ?>

        </p>


        <?php

        if ($message != "") {

            print "
            <p style='color:#bfa67a; font-weight:bold;'>
                $message
            </p>";
        }

        ?>


        <!-- Buttons -->
        <form method="POST" action="">

            <div style="
                display:flex;
                gap:10px;
                margin-top:20px;
            ">

                <button 
                    type="submit"
                    class="btn"
                    style="
                        width:140px;
                        text-align:center;
                    "
                >
                    Add to Cart
                </button>

                <a 
                    href="home.php"
                    class="btn"
                    style="
                        width:140px;
                        text-align:center;
                        display:inline-block;
                        box-sizing:border-box;
                    "
                >
                    Back to Store
                </a>

            </div>

        </form>

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


<?php include "logout_popup.php"; ?>

</body>
</html>