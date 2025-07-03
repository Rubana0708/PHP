<?php
session_start();

$hostname = "127.0.0.1";
$database = "projectDB";
$username = "root";
$password = "";
$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sname = $_POST['username'];
    $spassword = $_POST['password'];

    $sql = "SELECT * FROM staff WHERE sname = '$sname' AND spassword = '$spassword'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $_SESSION['staff'] = $sname;
        header("Location: Main.php");
        exit;
    } else {
        echo "<script>alert('Invalid username or password');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-image: url(https://m.media-amazon.com/images/I/71mHv9f8GsL.jpg);
            background-size: 300px;
        }
    </style>
</head>
<body>
<div class="login-container">
    <form class="login-content" method="POST">
        <h2>Login</h2>

        <div class="input-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Enter your username" required>
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
        </div>

        <div class="remember-forgot">
            <label><input type="checkbox"> Remember Me</label>
            <a href="#">Forgot Password?</a>
        </div>

        <button type="submit" class="login-btn">Login</button>
    </form>
</div>
</body>
</html>
