<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "secretsanta";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST["email"];
  $password = $_POST["password"];

  // Query to check if user exists
  $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // User exists, perform login logic here
    $user_data = $result->fetch_assoc();

    // Store user data in session
    $_SESSION['id'] = $user_data['id'];
    $_SESSION['email'] = $user_data['email'];

    // Redirect to index2.php
    header("Location: index2.php");
    exit();
  } else {
    // User does not exist or invalid credentials
    echo "Invalid email or password.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <a href="index2.php" onclick="changeContent('index2.php')">Home</a>
    <a href="login.php" class="middle" onclick="changeContent('loginpage.php')">Login</a>
</nav>

<div class="content">
    <h2>Log in here</h2>
    <p>Don't have an account? <a href="register.php">Make an account</a></p>
</div>

<form method="POST" action="login.php" class="content">
    <label for="email">Email:</label>
    <input type="text" id="email" name="email" required><br><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>

    <button type="submit">Log In</button>
</form>

<script src="script.js" defer></script>

</body>
</html>
