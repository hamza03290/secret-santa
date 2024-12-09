<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup Form</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <a href="index2.php" onclick="changeContent('index2.php')">Home</a>
    <a href="login.php" class="middle" onclick="changeContent('loginpage.php')">Login</a>
</nav>

<div class="content">
    <h2>Sign in here</h2>
    <p>Already did this last year? <a href="login.php">Log in to your account</a></p>
</div>

<form method="POST" action="register.php" class="content">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br><br>
   
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br><br>
   
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>
   
    <input type="submit" value="Sign Up">
</form>

</body>
</html>


<?php
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/vendor/autoload.php'; // Include PHPMailer library

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $email = $_POST["email"];
  $password = $_POST["password"];

  $servername = "localhost";
  $db_username = "root";
  $db_password = "";
  $dbname = "secretsanta";

  $conn = new mysqli($servername, $db_username, $db_password, $dbname);
  // END: abpxx6d04wxr

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Insert user data into the database
  $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
  // FILEPATH: /c:/xampp/htdocs/secretsanta/register.php
  // BEGIN: signup-form

  if ($conn->query($sql) === TRUE) {
    echo "User registered successfully";

    // Send email to the recipient
    $mail = new PHPMailer(true);

    try {
      //Server settings
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = '';
      $mail->Password =  '';
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;

      //Recipients
      $mail->setFrom('238288student@gmail.com', 'Secret Santa');
      $mail->addAddress($email, $username); // Add a recipient

      //Content
      $mail->isHTML(true);
      $mail->Subject = 'Registration Successful';
      $mail->Body = 'Thank you for registering!';

      $mail->send();
      echo "Email sent successfully";

      // Redirect to another page
      header("Location: sucess.php");
      exit;
    } catch (Exception $e) {
      echo "Failed to send email. Error: " . $mail->ErrorInfo;
    }
  }
  $conn->close();
}
?>

