

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("127.0.0.1", "root", "", "game_store", 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM products ORDER BY id DESC");

function getImage($category, $image) {
    if (!empty($image)) {
        return "images/" . $image;
    }

    $category = strtolower($category);

    if ($category == "sports") {
        return "images/sports.jpg";
    } elseif ($category == "action") {
        return "images/action.jpg";
    } elseif ($category == "adventure") {
        return "images/adventure.jpg";
    } elseif ($category == "racing") {
        return "images/racing.jpg";
    } else {
        return "images/default.jpg";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #0b1020, #1f2340);
            color: white;
        }

        .navbar {
            background: #060b1a;
            padding: 15px 0;
            text-align: center;
            border-bottom: 1px solid #1f2937;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 20px;
            font-size: 16px;
        }

        h1 {
            text-align: center;
            margin: 30px 0;
        }

        .container {
            width: 90%;
            margin: auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 22px;
            padding-bottom: 40px;
        }

        .card {
            background: rgba(10, 15, 35, 0.95);
            border: 1px solid #1f2937;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            border-radius: 12px;
            overflow: hidden;
            padding: 15px;
            text-align: center;
        }

        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            background: #0f172a;
        }

        .card h2 {
            margin: 15px 0 10px;
            font-size: 22px;
        }

        .category {
            color: #cbd5e1;
            margin-bottom: 8px;
            text-transform: capitalize;
        }

        .price {
            color: #22c55e;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .description {
            color: #cbd5e1;
            font-size: 14px;
            min-height: 40px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #cbd5e1;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="products.php">Home</a>
    <a href="dashboard.php">Admin</a>
    <a href="add_product.php">Add Product</a>
</div>

<h1>Products</h1>

<div class="container">
<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

        $name = htmlspecialchars($row['name']);
        $category = htmlspecialchars($row['category']);
        $price = number_format((float)$row['price'], 2);
        $description = htmlspecialchars($row['description']);
        $image = getImage($row['category'], $row['image']);

        echo "<div class='card'>";
        echo "<img src='$image' alt='Product Image'>";
        echo "<h2>$name</h2>";
        echo "<div class='category'>Category: $category</div>";
        echo "<div class='price'>$$price</div>";
        echo "<div class='description'>$description</div>";
        echo "</div>";
    }
} else {
    echo "<p>No products found</p>";
}
?>
</div>

<div class="footer">
    © 2025 Digital Game Store | All Rights Reserved
</div>

</body>
</html>