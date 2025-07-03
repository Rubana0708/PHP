<?php
$hostname = "127.0.0.1";
$database = "projectDB";
$username = "root";
$password = "";
$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = $_POST['ProductName'];
    $productDescription = $_POST['productDescription'];
    $singleProductCost = $_POST['singleProductCost'];
    $materialIDs = $_POST['materialID'];
    $materialQuantities = $_POST['materialQuantity'];

    // Upload image to 'product' folder (not stored in DB)
    $productImage = $_FILES['productImage']['name'];
    $uploadPath = "product/" . basename($productImage);
    move_uploaded_file($_FILES['productImage']['tmp_name'], $uploadPath);

    // Insert into product table
    $insertProduct = "INSERT INTO product (pname, pdesc, pcost)
                      VALUES ('$productName', '$productDescription', '$singleProductCost')";
    mysqli_query($conn, $insertProduct);
    $productID = mysqli_insert_id($conn); // Get new pid

    // Insert materials into prodmat
    for ($i = 0; $i < count($materialIDs); $i++) {
        $mid = $materialIDs[$i];
        $qty = $materialQuantities[$i];
        $insertMaterial = "INSERT INTO prodmat (pid, mid, pmqty)
                           VALUES ('$productID', '$mid', '$qty')";
        mysqli_query($conn, $insertMaterial);
    }

    echo "<script>alert('Product added successfully! Image saved to /product folder.');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>STAFF</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header class="header">
    <h1>Smile and Sunshine Staff Management
        <img src="https://odoocdn.com/openerp_website/static/src/img/icons/blue_rocket.svg"
             width="80px" alt="rocket" loading="lazy"/>
    </h1>
    <input type="text" id="searchBar" placeholder="Search...">
</header>

<div class="container">
    <aside class="sidebar">
        <h2>Staff Menu</h2>
        <ul>
            <li><a href="product-ADD.php">Add Product</a></li>
            <li><a href="material.php">Add Material</a></li>
            <li><a href="update.php">Update Order</a></li>
            <li><a href="report.php">Generate Report</a></li>
            <li><a href="delete.php">Delete Product</a></li>
        </ul>
    </aside>

    <?php
    echo <<<HTML
    <form class="main1" method="POST" enctype="multipart/form-data">
    <h2>Insert Item's Information</h2>

    <fieldset>
        <legend>Product Information</legend>
        <label>Product Name:</label>
        <input type="text" name="ProductName" required><br />

        <label>Product Description:</label>
        <textarea name="productDescription" required></textarea><br />

        <label>Product Image:</label>
        <input type="file" name="productImage" accept="image/*" required><br />

        <label>Single Product Cost:</label>
        <input type="number" step="0.01" name="singleProductCost" required>
    </fieldset>

    <h3>Material Details</h3>
    <fieldset>
        <legend>Material Information</legend>
        <div id="materials">
            <div>
                <label>Material ID:</label>
                <input type="number" name="materialID[]" required><br />

                <label>Material Quantity:</label>
                <input type="number" name="materialQuantity[]" required>
            </div>
        </div>
    </fieldset>

    <div>
        <button type="button" onclick="addMaterial()">Add More Material</button>
    </div>

    <button type="submit">Submit</button>
    </form>
    HTML;
    ?>

</div>

<script>
    function addMaterial() {
        const container = document.getElementById("materials");
        const div = document.createElement("div");
        div.innerHTML = `
            <label>Material ID:</label>
            <input type="number" name="materialID[]" required><br />
            <label>Material Quantity:</label>
            <input type="number" name="materialQuantity[]" required><br />
        `;
        container.appendChild(div);
    }
</script>
</body>
</html>
