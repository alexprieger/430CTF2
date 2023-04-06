<?php
// the link in the email should redirect to "reset.html"
$servername = "localhost";
$username = "server";
$password = "pbN967bgWUAgdb5X3BmBxI2F";
$dbname = "bank";

file_put_contents('php://stderr', print_r("Logging in\n", TRUE));

$numberOfDesiredBytes = 16;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// getting username and password from the url
$reg_username = $_GET['user'];

$username = stripcslashes($reg_username);  

$username = mysqli_real_escape_string($conn, $username); 

$count = "select * from bank.users where username= '$username'";


if($count == 1) {
  //username exists -> send email to redirect to "reset.html"
}

?>