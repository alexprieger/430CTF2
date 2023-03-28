<?php
$servername = "localhost";
$username = "server";
$password = "pbN967bgWUAgdb5X3BmBxI2F";
$dbname = "bank";

file_put_contents('php://stderr', print_r("Logging out\n", TRUE));

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$cookie_value = $_COOKIE["auth"];
if (isset($cookie_value)){
  // Delete the cookie from the server
  $sql = "UPDATE users SET cookies = NULL WHERE cookies = X'" . bin2hex($cookie_value) . "'";
  $result = mysqli_query($conn, $sql);  

  // Delete the cookie from the browser
  setcookie("auth", "", time() - (3600));
}

?>
