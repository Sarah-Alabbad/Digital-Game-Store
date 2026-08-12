<?php
/* Admin Dashboard Page - Developed by Sarah Alabbad */

session_start();
include "db_connect.php";

/* User login check */
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

/* Admin access check */
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: home.php");
    exit();
}

$message = "";

/* Update game */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_game"])) {

    $game_id = $_POST["game_id"];
    $title = $_POST["title"];
    $genre = $_POST["genre"];
    $price = $_POST["price"];
    $discount_percent = $_POST["discount_percent"];
    $description = $_POST["description"];

    /* Discount is optional */
    if ($discount_percent == "") {
        $discount_percent = 0;
    }

    /* Keep old image by default */
    $old_image = $_POST["old_image"];
    $image = $old_image;

    /* Upload new image only if admin selects one */
    if (!empty($_FILES["image"]["name"])) {

        $folder = "Images/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $image = $folder . $file_name;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image)) {
            $message = "Image upload failed.";
        }
    }

    /* Update database only if image upload did not fail */
    if ($message == "") {

        $update_sql = "UPDATE games 
                       SET title = '$title',
                           genre = '$genre',
                           price = '$price',
                           discount_percent = '$discount_percent',
                           description = '$description',
                           image = '$image'
                       WHERE game_id = '$game_id'";

        if (mysqli_query($conn, $update_sql)) {
            $message = "Game updated successfully.";
        } else {
            $message = "Error updating game: " . mysqli_error($conn);
        }
    }
}

/* Delete game */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_game"])) {

    $game_id = $_POST["game_id"];

    /* Delete related data first to avoid database relationship problems */
    mysqli_query($conn, "DELETE FROM cart_items WHERE game_id = '$game_id'");
    mysqli_query($conn, "DELETE FROM user_library WHERE game_id = '$game_id'");
    mysqli_query($conn, "DELETE FROM reviews WHERE game_id = '$game_id'");
    mysqli_query($conn, "DELETE FROM owned_games WHERE game_id = '$game_id'");

    /* Delete game from games table */
    $delete_sql = "DELETE FROM games WHERE game_id = '$game_id'";

    if (mysqli_query($conn, $delete_sql)) {
        $message = "Game deleted successfully.";
    } else {
        $message = "Error deleting game: " . mysqli_error($conn);
    }
}

/* Get all games */
$sql = "SELECT * FROM games ORDER BY game_id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | Digital Game Store</title>
<link rel="stylesheet" href="Style/style.css">
</head>

<body>

<nav>
    <a href="home.php">Home</a>
    <a href="profile.php">Profile</a>
    <a href="admin.php">Admin</a>
    <a href="cart.php">Cart</a>
    <a href="support.php">Support</a>
    <a href="FAQ.html">FAQ</a>
    <a href="Terms & Conditions.html">Terms</a>
    <a href="about_us.php">About Us</a>
    <a href="Join_Team.php">Join Team</a>
    <a href="logout.php" class="logout-link">Logout</a>
</nav>

<div class="container">

<h1>Admin Dashboard</h1>

<div class="card" style="text-align:center;">
    <h2>Game Management</h2>

    <p>
        Manage the games available in the Digital Game Store.
    </p>

    <a href="add_product.php" class="btn">Add New Game</a>
</div>

<?php if ($message != "") { ?>
    <div class="card">
        <p style="text-align:center; color:#bfa67a; font-weight:bold;">
            <?php echo $message; ?>
        </p>
    </div>
<?php } ?>

<div class="card">
    <h2>All Games</h2>

    <?php if (mysqli_num_rows($result) > 0) { ?>

        <?php while ($game = mysqli_fetch_assoc($result)) { ?>

            <!-- Game edit form -->
            <form method="POST" action="admin.php" enctype="multipart/form-data" style="
                border:1px solid #4a475e;
                padding:20px;
                margin:20px 0;
                border-radius:10px;
                background-color:#24222f;
            ">

                <input type="hidden" name="game_id" value="<?php echo $game['game_id']; ?>">
                <input type="hidden" name="old_image" value="<?php echo $game['image']; ?>">

                <div style="
                    display:grid;
                    grid-template-columns:160px 1fr;
                    gap:20px;
                    align-items:start;
                ">

                    <!-- Current game image -->
                    <div style="text-align:center;">
                        <img src="<?php echo $game['image']; ?>" 
                             alt="<?php echo $game['title']; ?>"
                             style="width:140px; height:160px; object-fit:cover; border:2px solid #4a475e; border-radius:8px;">

                        <p>ID: <?php echo $game["game_id"]; ?></p>
                    </div>

                    <!-- Game information inputs -->
                    <div>

                        <label>Game Title</label>
                        <input type="text" name="title" value="<?php echo $game['title']; ?>" required>

                        <label>Genre</label>
                        <input type="text" name="genre" value="<?php echo $game['genre']; ?>" required>

                        <label>Price</label>
                        <input type="number" step="0.01" name="price" value="<?php echo $game['price']; ?>" required>

                        <label>Discount Percentage Optional</label>
                        <input type="number" step="1" name="discount_percent" value="<?php echo $game['discount_percent']; ?>" placeholder="Leave empty if no discount">

                        <label>Game Image</label>
                        <input type="file" name="image" accept="image/*">

                        <p style="font-size:13px; color:#bfa67a;">
                            Current image: <?php echo $game['image']; ?>
                        </p>

                        <label>Description</label>
                        <textarea name="description" rows="4" required><?php echo $game['description']; ?></textarea>

                        <div style="display:flex; gap:15px; margin-top:20px; flex-wrap:wrap;">

                            <button type="submit" name="update_game" class="btn">
                                Save Changes
                            </button>

                            <button type="submit" name="delete_game" class="btn"
                                    onclick="return confirm('Are you sure you want to delete this game?');">
                                Delete Game
                            </button>

                        </div>

                    </div>

                </div>

            </form>

        <?php } ?>

    <?php } else { ?>

        <p style="text-align:center;">No games found.</p>

    <?php } ?>

</div>

</div>

<footer style="position:fixed; bottom:0; width:100%; text-align:center; padding:15px; background-color:#24222f; border-top:2px solid #4a475e; color:#d1d1e0; font-size:14px;">
    © 2026 Digital Game Store | All Rights Reserved
</footer>

<?php include "logout_popup.php"; ?>

</body>
</html>