<?php
$hostname = "127.0.0.1";
$database = "projectDB";
$username = "root";
$password = "";
$conn = mysqli_connect($hostname, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle update form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $oid = $_POST['oid'];
    $oqty = $_POST['oqty'];
    $odeliverdate = $_POST['odeliverdate'];
    $ostatus = $_POST['ostatus'];

    // Update orders table
    $sql1 = "UPDATE orders SET oqty='$oqty', odeliverdate='$odeliverdate', ostatus='$ostatus' WHERE oid='$oid'";
    mysqli_query($conn, $sql1);

    // Update material reserved quantity
    $mid = $_POST['mid'];
    $usedQty = $_POST['usedQty'];
    $sql2 = "UPDATE material SET mrqty = '$usedQty' WHERE mid='$mid'";
    mysqli_query($conn, $sql2);

    echo "<script>alert('Order and material updated.');</script>";
}

// Fetch all orders with required joins
$sql = "
SELECT o.oid, o.odate, o.oqty, o.ocost, o.odeliverdate, o.ostatus,
       c.cname, c.ctel, c.caddr,
       p.pid, p.pname, p.pcost,
       m.mid, m.mname, m.mqty, m.mrqty, m.munit, m.mreorderqty,
       pm.pmqty
FROM orders o
JOIN customer c ON o.cid = c.cid
JOIN product p ON o.pid = p.pid
JOIN prodmat pm ON p.pid = pm.pid
JOIN material m ON pm.mid = m.mid
";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Order Records</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header class="header">
    <h1>Smile and Sunshine Staff Management
        <img src="https://odoocdn.com/openerp_website/static/src/img/icons/blue_rocket.svg" width="80px" alt="rocket" loading="lazy"/>
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
<main class="content">
    <h2>Manage Orders</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Product ID</th>
                    <th>Product Image</th>
                    <th>Order Quantity</th>
                    <th>Total Amount(\$)</th>
                    <th>Customer Name</th>
                    <th>Contact Number</th>
                    <th>Delivery Address</th>
                    <th>Delivery Date</th>
                    <th>Order Status</th>
                    <th>Material Name</th>
                    <th>Material Used</th>
                    <th>Physical Qty</th>
                    <th>Available Qty</th>
                    <th>Unit</th>
                    <th>Low Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
HTML;

    while ($row = mysqli_fetch_assoc($result)) {
        $oid = $row['oid'];
        $odate = $row['odate'];
        $pid = $row['pid'];
        $oqty = $row['oqty'];
        $cost = $row['pcost'] * $oqty;
        $cname = $row['cname'];
        $ctel = $row['ctel'];
        $caddr = $row['caddr'];
        $deliver = $row['odeliverdate'] ?? "";
        $status = $row['ostatus'];
        $img = "product/{$pid}.jpg";

        $mid = $row['mid'];
        $mname = $row['mname'];
        $mqty = $row['mqty'];
        $mrqty = $row['mrqty'];
        $munit = $row['munit'];
        $reorder = $row['mreorderqty'];
        $usedQty = $row['pmqty'] * $oqty;
        $available = $mqty - $mrqty;
        $lowStock = ($available < $reorder) ? "❌" : "✅";

        echo <<<ROW
    <tr>
        <form method="POST">
        <td>$oid<input type="hidden" name="oid" value="$oid"></td>
        <td>$odate</td>
        <td>$pid</td>
        <td><img src="$img" width="100" height="100"></td>
        <td><input type="number" name="oqty" value="$oqty" min="1"></td>
        <td>\$$cost</td>
        <td>$cname</td>
        <td>$ctel</td>
        <td>$caddr</td>
        <td><input type="date" name="odeliverdate" value="$deliver"></td>
        <td>
            <select name="ostatus">
                <option value="1" {$selected = ($status == 1) ? "selected" : ""}>Pending</option>
                <option value="2" {$selected = ($status == 2) ? "selected" : ""}>Accepted</option>
                <option value="3" {$selected = ($status == 3) ? "selected" : ""}>Rejected</option>
            </select>
        </td>
        <td>$mname</td>
        <td><input type="number" name="usedQty" value="$usedQty" min="0">
            <input type="hidden" name="mid" value="$mid">
        </td>
        <td>$mqty</td>
        <td>$available</td>
        <td>$munit</td>
        <td>$lowStock</td>
        <td><button type="submit" class="update-btn">Update</button></td>
        </form>
    </tr>
ROW;
    }

    echo <<<HTML
            </tbody>
        </table>
    </div>
</main>
HTML;
    ?>
</div>
</body>
</html>
