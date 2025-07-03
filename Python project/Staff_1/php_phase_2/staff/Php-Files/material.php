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
    $mname = $_POST['materialName'];
    $mqty = $_POST['physicalQuantity'];
    $mrqty = $_POST['reservedQuantity'];
    $munit = $_POST['unit'];
    $mreorderqty = $_POST['reorderLevel'];

    if ($mrqty > $mqty) {
        echo "<script>alert('Reserved quantity cannot be greater than physical quantity!');</script>";
    } else {
        $sql = "INSERT INTO material (mname, mqty, mrqty, munit, mreorderqty)
                VALUES ('$mname', '$mqty', '$mrqty', '$munit', '$mreorderqty')";
        mysqli_query($conn, $sql);
        echo "<script>alert('Material added successfully!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Material Entry Form</title>
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
    <div class="main">
        <h2>Material Entry Form</h2>
        <form method="POST">
            <fieldset>
                <legend>Entry Form</legend>

                <label>Material Name:</label>
                <input type="text" name="materialName" required><br />

                <label>Physical Quantity:</label>
                <input type="number" name="physicalQuantity" min="1" required><br />

                <label>Reserved Quantity:</label>
                <input type="number" name="reservedQuantity" min="0" required><br />

                <label>Unit:</label>
                <input type="text" name="unit" required><br />

                <label>Re-Order Level:</label>
                <input type="number" name="reorderLevel" min="0" required><br />
            </fieldset>

            <button type="submit">Add Material</button>
        </form>
    </div>
HTML;
    ?>
</div>
</body>
</html>
