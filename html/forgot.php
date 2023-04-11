<?php
$servername = "localhost";
$username = "server";
$password = "pbN967bgWUAgdb5X3BmBxI2F";
$dbname = "mydb";
$numberOfDesiredBytes = 16;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// getting username and password from the url
$reg_username = $_GET['user'];

//to prevent from mysqli injection  
$username = stripcslashes($reg_username);  
$username = mysqli_real_escape_string($conn, $username);  

// retrieving salt
$retrieve_salt_sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
try {
	$salt_result = $conn->query($retrieve_salt_sql);
} catch (mysqli_sql_exception $e) {
	die("Error logging in: " . $e->getMessage());
}
$salt_object = $salt_result->fetch_object();  

if($salt_object != null) {
  $email = $username . "@usc.edu";
  // echo " email is: " . $email;
  $reset = random_bytes($numberOfDesiredBytes);
  $reset_hex = bin2hex($reset);
  // echo " reset is: " . $reset;

  $sql_reset = "UPDATE users SET reset = X'" . $reset_hex . "' WHERE username= '$username'";
  try {
    $result = mysqli_query($conn, $sql_reset);
  } catch (mysqli_sql_exception $e) {
    die("Error updating reset: " . $e->getMessage());
  }

  $reset_link = "3.133.129.167/reset.html?reset=$reset_hex";
  // echo "\nReset link is: " . $reset_link;

  $output = shell_exec("bash ./mail.sh $reset_link $email");

  header("Location: reset_email_success.html");
  exit();
 } else {
         header("Location: reset_email_failure.html");
   exit(); 
 }

?>
