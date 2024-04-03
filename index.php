<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Website Title</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <nav class="navbar">
        <a href="index2.php" onclick="changeContent('index2.php')">Home</a>
        <a href="login.php" class="middle" onclick="changeContent('loginpage.php')">Login</a>
    </nav>

    <div class="content">
        <h2 id='contentTitle'>Secret Santa Generator</h2>
        <p id='contentText'>Enter Names</p>
    </div>

    <div class="content"> 
        <form id="questionForm" method="POST" action="register.php">
            <label for="name">Your Name:</label>
            <input type="text" id="name" name="name" required>

            <div id="optionsDiv">
                <input type="radio" id="optionB" name="option" value="B">
                <label for="optionB">I am just organizing</label>

                <input type="radio" id="optionC" name="option" value="C">
                <label for="optionC">I am organizing and participating in the exchange</label>
            </div>

            <input type="number" id="budget" name="budget" placeholder="Enter your budget" required>

            <div id="nameFields">
                <input type="text" id="name1" name="name[]" placeholder="Name 1" required>
            </div>

            <button type="button" onclick="addInputField()">Add more names</button>

            <input type="hidden" id="numusers" name="numusers" value="">
            <input type="hidden" id="users" name="users" value="">

            <button type="button" onclick="prepareForm()">Submit</button>
        </form>
    </div>

    <script src="script.js" defer></script>
    <script>
        var nameCounter = 1;

        function addInputField() {
            var nameFields = document.getElementById('nameFields');
            var newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.id = 'name' + (++nameCounter);
            newInput.name = 'name[]';
            newInput.placeholder = 'Name ' + nameCounter;
            nameFields.appendChild(newInput);
        }

        function prepareForm() {
            var users = document.querySelectorAll('input[name="name[]"]');
            var numUsers = users.length;
            document.getElementById('numusers').value = numUsers;

            var usersArray = [];
            users.forEach(function (user) {
                usersArray.push(user.value);
            });
            document.getElementById('users').value = JSON.stringify(usersArray);

            document.getElementById('questionForm').submit();
        }
    </script>
</body>

</html>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required keys exist in the $_POST array
    if (isset($_POST['numusers'], $_POST['users'])) {
        $numUsers = $_POST['numusers'];
        $usersJson = $_POST['users'];

        // Decode JSON string to an array
        $users = json_decode($usersJson, true);

        if ($users === null) {
            // Handle JSON decoding error if needed
            die('Error decoding JSON');
        }

        // Shuffle the users
        shuffle($users);

        // Pair the shuffled users together
        $pairs = [];
        for ($i = 0; $i < $numUsers; $i++) {
            $pairs[] = [$users[$i], $users[($i + 1) % $numUsers]];
        }

        // Convert the pairs into a string
        $pairsString = implode(', ', array_map(function ($pair) {
            return $pair[0] . ' - ' . $pair[1];
        }, $pairs));

        // Retrieve the user's email from the database based on their login information
        $loggedInUserEmail = getEmailFromDatabase($_SESSION['username']); // Replace with your actual function

        // Send an email to the logged-in user
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = ''; // Replace with your actual email address
            $mail->Password = ''; // Replace with your actual email password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('your_email@gmail.com', 'Mailer');
            $mail->addAddress($loggedInUserEmail, 'User'); // Set the user's email as the recipient

            $mail->isHTML(true);
            $mail->Subject = 'Secret Santa';
            $mail->Body = 'The paired list of users is: ' . $pairsString;

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Failed to send email. Error: " . $mail->ErrorInfo;
        }
    } else {
        // Handle the case where required keys are not set
        die('Error: Required keys are not set');
    }
}

// Function to retrieve the user's email from the database
function getEmailFromDatabase($username)
{
    // Replace these values with your actual database credentials
    $host = 'your_database_host';
    $dbUser = 'your_database_username';
    $dbPassword = 'your_database_password';
    $dbName = 'your_database_name';

    // Establish a connection to the database
    $conn = new mysqli($host, $dbUser, $dbPassword, $dbName);

    // Check the connection
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Fetch the user's email from the database
    $query = "SELECT email FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    // Close the database connection
    $conn->close();

    return $email;
}
?>
