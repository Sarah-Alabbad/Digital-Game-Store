<?php
/* Add Game Page Developed by Sarah Alabbad */

session_start();

/* Database connection */
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

/* Popup variables */
$show_popup = false;
$popup_title = "";
$popup_text = "";

/* Add game process */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST["title"];
    $genre = $_POST["genre"];
    $price = $_POST["price"];
    $discount_percent = $_POST["discount_percent"];
    $description = $_POST["description"];

    /* Discount is optional */
    if ($discount_percent == "") {
        $discount_percent = 0;
    }

    $api_image = $_POST["api_image"];
    $image = $api_image;

    /* Upload image if admin selected a file */
    if (!empty($_FILES["image"]["name"])) {

        $folder = "Images/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $image = $folder . $file_name;

        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }

    /* Empty fields validation */
    if (empty($title) || empty($genre) || empty($price) || empty($description) || empty($image)) {

        $show_popup = true;
        $popup_title = "Missing Information";
        $popup_text = "Please fill in all game fields and choose an image.";

    } else {

        /* Insert game into database */
        $stmt = $conn->prepare("INSERT INTO games (title, genre, price, discount_percent, description, image) VALUES (?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssddss", $title, $genre, $price, $discount_percent, $description, $image);

        if ($stmt->execute()) {

            $show_popup = true;
            $popup_title = "Game Added";
            $popup_text = "The game was added successfully.";

        } else {

            $show_popup = true;
            $popup_title = "Error";
            $popup_text = "Something went wrong while adding the game.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>Add Game | Digital Game Store</title>

<link rel="stylesheet" href="Style/style.css">

</head>

<body>

<nav>

    <a href="home.php">Home</a>
    <a href="profile.php">Profile</a>

    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin") { ?>
        <a href="admin.php">Admin</a>
    <?php } ?>

    <a href="cart.php">Cart</a>
    <a href="support.php">Support</a>
    <a href="FAQ.html">FAQ</a>
    <a href="Terms & Conditions.html">Terms</a>
    <a href="about_us.php">About Us</a>
    <a href="Join_Team.php">Join Team</a>
    <a href="logout.php" class="logout-link">Logout</a>

</nav>

<div class="container">

<div class="card" style="max-width:600px; margin:40px auto;">

<h1>Add Game</h1>

<form method="POST" action="" enctype="multipart/form-data" novalidate>

    <label>Game Title</label>
    <input type="text" name="title" placeholder="Enter game title">

    <button type="button" class="btn" onclick="fetchGameInfo()" style="margin:10px 0 20px;">
        Fetch Game Info
    </button>

    <label>Genre</label>

    <select name="genre">

        <option value="">Select Genre</option>
        <option value="Action">Action</option>
        <option value="Action Adventure">Action Adventure</option>
        <option value="Adventure">Adventure</option>
        <option value="Sports">Sports</option>
        <option value="Racing">Racing</option>
        <option value="Strategy">Strategy</option>
        <option value="Shooter">Shooter</option>
        <option value="Sandbox">Sandbox</option>

    </select>

    <label>Price</label>
    <input type="number" step="0.01" name="price" placeholder="Enter price">

    <label>Discount Percentage Optional</label>
    <input type="number" step="1" name="discount_percent" placeholder="Leave empty if no discount">

    <label>Game Image</label>
    <input type="file" name="image" accept="image/*">

    <input type="hidden" name="api_image" id="apiImage">

    <p id="imageInfo" style="font-size:13px; color:#bfa67a;">
        Choose an image from your device, or use Fetch Game Info to load an API image.
    </p>

    <label>Description</label>
    <textarea name="description" rows="5" placeholder="Enter game description"></textarea>

    <button type="submit" class="btn" style="display:block; margin:25px auto 0;">
        Add Game
    </button>

</form>

</div>

</div>

<footer style="width:100%; text-align:center; padding:15px; background-color:#24222f; border-top:2px solid #4a475e; color:#d1d1e0; font-size:14px;">

    © 2026 Digital Game Store | All Rights Reserved

</footer>

<div id="addGamePopup" style="
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
        width:360px;
        text-align:center;
        box-shadow:0 0 20px rgba(0,0,0,0.5);
    ">

        <h2 id="popupTitle" style="color:#bfa67a; margin-top:0;">Message</h2>

        <p id="popupText" style="font-size:16px; margin:20px 0;">
            Message here.
        </p>

        <button id="closeAddGamePopup" class="btn">Okay</button>

    </div>

</div>

<script>

/* Form elements */
const addGameForm = document.querySelector("form");
const titleInput = document.querySelector('input[name="title"]');
const genreInput = document.querySelector('select[name="genre"]');
const priceInput = document.querySelector('input[name="price"]');
const discountInput = document.querySelector('input[name="discount_percent"]');
const imageInput = document.querySelector('input[name="image"]');
const apiImageInput = document.getElementById("apiImage");
const imageInfo = document.getElementById("imageInfo");
const descriptionInput = document.querySelector('textarea[name="description"]');

/* Popup elements */
const addGamePopup = document.getElementById("addGamePopup");
const popupTitle = document.getElementById("popupTitle");
const popupText = document.getElementById("popupText");
const closeAddGamePopup = document.getElementById("closeAddGamePopup");

/* Show popup function */
function showAddGamePopup(title, text) {

    popupTitle.textContent = title;
    popupText.textContent = text;
    addGamePopup.style.display = "flex";
}

/* Form validation */
addGameForm.addEventListener("submit", function(event) {

    if (
        titleInput.value.trim() === "" ||
        genreInput.value.trim() === "" ||
        priceInput.value.trim() === "" ||
        descriptionInput.value.trim() === "" ||
        (imageInput.value.trim() === "" && apiImageInput.value.trim() === "")
    ) {

        event.preventDefault();

        showAddGamePopup(
            "Missing Information",
            "Please fill in all game fields and choose an image."
        );
    }
});

/* Close popup button */
closeAddGamePopup.addEventListener("click", function() {

    addGamePopup.style.display = "none";
});

/* Close popup when clicking outside */
addGamePopup.addEventListener("click", function(event) {

    if (event.target === addGamePopup) {
        addGamePopup.style.display = "none";
    }
});

/* Fetch game information */
async function fetchGameInfo() {

    const title = titleInput.value.toLowerCase().trim();

    const localGames = {

        "zelda": {
            genre: "Adventure",
            price: "160",
            discount_percent: "0",
            image: "Images/zelda.jpg",
            description: "Explore a vast world full of puzzles and secrets."
        },

        "fifa": {
            genre: "Sports",
            price: "200",
            discount_percent: "0",
            image: "Images/football.jpg",
            description: "Play football matches and tournaments."
        },

        "resident evil": {
            genre: "Action",
            price: "140",
            discount_percent: "0",
            image: "Images/resident.jpg",
            description: "Fight enemies and survive dangerous missions."
        }
    };

    if (title === "") {

        showAddGamePopup(
            "Missing Title",
            "Enter a game title first."
        );

        return;
    }

    try {

        const response = await fetch(
            "https://www.cheapshark.com/api/1.0/games?title=" +
            encodeURIComponent(title)
        );

        const data = await response.json();

        if (data.length > 0) {

            const game = data[0];

            priceInput.value = game.cheapest;
            discountInput.value = "";
            apiImageInput.value = game.thumb;
            imageInfo.textContent = "API image loaded: " + game.thumb;
            descriptionInput.value = "Fetched from CheapShark API.";

            showAddGamePopup(
                "Game Info Loaded",
                "Price and image were loaded from the API."
            );

            return;
        }

    } catch (error) {

        console.log("API failed.");
    }

    if (localGames[title]) {

        genreInput.value = localGames[title].genre;
        priceInput.value = localGames[title].price;
        discountInput.value = "";
        apiImageInput.value = localGames[title].image;
        imageInfo.textContent = "Local image loaded: " + localGames[title].image;
        descriptionInput.value = localGames[title].description;

        showAddGamePopup(
            "Game Info Loaded",
            "Game information was loaded from local data."
        );

    } else {

        showAddGamePopup(
            "Game Not Found",
            "No information was found for this game."
        );
    }
}

/* PHP popup message */
<?php
if ($show_popup == true) {

    echo "showAddGamePopup(" .
         json_encode($popup_title) .
         ", " .
         json_encode($popup_text) .
         ");";
}
?>

</script>

<?php include "logout_popup.php"; ?>

</body>
</html>