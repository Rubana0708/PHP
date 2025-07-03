<?php
$hostname = "127.0.0.1";
$database = "projectDB";
$username = "root";
$password = "";
$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch report data
$sql = "SELECT o.oid, o.oqty, p.pid, p.pname, p.pcost
        FROM orders o
        JOIN product p ON o.pid = p.pid";
$result = mysqli_query($conn, $sql);

$reportData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['image'] = "product/" . $row['pid'] . ".jpg";
    $row['total'] = $row['oqty'] * $row['pcost'];
    $reportData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Reports</title>
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
<main class="content1">
    <h2>Order Reports</h2>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Product Name</th>
                <th>Product Image</th>
                <th>Order Quantity</th>
                <th>Price (\$)</th>
                <th>Total Sales (\$)</th>
            </tr>
        </thead>
        <tbody>
HTML;

    foreach ($reportData as $row) {
        $oid = $row['oid'];
        $pname = htmlspecialchars($row['pname']);
        $imagePath = $row['image'];
        $qty = $row['oqty'];
        $price = number_format($row['pcost'], 2);
        $total = number_format($row['total'], 2);

        echo <<<ROW
    <tr>
        <td>$oid</td>
        <td>$pname</td>
        <td><img src="$imagePath" width="100px" height="100px"></td>
        <td>$qty</td>
        <td>$price</td>
        <td class="total-sales">$total</td>
    </tr>
ROW;
    }

    echo <<<HTML
        </tbody>
    </table>
</main>
HTML;
    ?>
</div>
</body>
</html>