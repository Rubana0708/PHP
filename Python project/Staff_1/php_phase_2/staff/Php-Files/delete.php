<?php
$hostname = "127.0.0.1";
$database = "projectDB";
$username = "root";
$password = "";
$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle deletion request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_pid'])) {
    $delete_pid = $_POST['delete_pid'];

    // Check again in backend: no orders
    $checkOrders = "SELECT COUNT(*) AS count FROM orders WHERE pid = '$delete_pid'";
    $result = mysqli_query($conn, $checkOrders);
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] == 0) {
        // Delete from prodmat first due to FK
        mysqli_query($conn, "DELETE FROM prodmat WHERE pid = '$delete_pid'");
        mysqli_query($conn, "DELETE FROM product WHERE pid = '$delete_pid'");
        echo "<script>alert('Product deleted successfully.');</script>";
    } else {
        echo "<script>alert('Cannot delete! Product has existing orders.');</script>";
    }
}

// Fetch product list
$productList = [];
$sql = "SELECT p.pid, p.pname, 
           (SELECT COUNT(*) FROM orders o WHERE o.pid = p.pid) AS orderCount 
        FROM product p";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $productList[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Product</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header class="header">
    <h1>Smile and Sunshine Staff Management
        <img src="https://odoocdn.com/openerp_website/static/src/img/icons/blue_rocket.svg"
             width="80px" alt="rocket" loading="lazy" />
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
    <main class="content2">
        <h2>Delete Product</h2>
        <table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Order Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
HTML;

    foreach ($productList as $product) {
        $pid = $product['pid'];
        $pname = htmlspecialchars($product['pname']);
        $hasOrders = $product['orderCount'] > 0;

        if ($hasOrders) {
            echo <<<ROW
        <tr>
            <td>$pid</td>
            <td>$pname</td>
            <td>❌ Has existing orders</td>
            <td><button class="delete-btn disabled" onclick="alert('Cannot delete: existing orders')">Delete</button></td>
        </tr>
ROW;
        } else {
            echo <<<ROW
        <tr>
            <td>$pid</td>
            <td>$pname</td>
            <td>✅ No orders</td>
            <td>
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                    <input type="hidden" name="delete_pid" value="$pid">
                    <button class="delete-btn" type="submit">Delete</button>
                </form>
            </td>
        </tr>
ROW;
        }
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
