<?php
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
$reg_password = $_GET['pass'];

//to prevent from mysqli injection  
$username = stripcslashes($reg_username);  
$password = stripcslashes($reg_password);  
$username = mysqli_real_escape_string($conn, $username);  
$password = mysqli_real_escape_string($conn, $password);  

// retrieving salt
$retrieve_salt_sql = "select * from bank.users where username= '$username'";
$salt_result = mysqli_query($conn, $retrieve_salt_sql);  
$salt_row = mysqli_fetch_array($salt_result, MYSQLI_ASSOC);  
$salt_count = mysqli_num_rows($salt_result);  


if($salt_count == 1) {
  $salt_value = $salt_row['salt'];
  $hashed_password = hash("sha256", $password.$salt_value); 
}

$cookie_value = random_bytes($numberOfDesiredBytes);
$sql = "UPDATE users SET cookies = X'" . bin2hex($cookie_value) . "' WHERE username = '$username' and password = '$hashed_password'";
$result = mysqli_query($conn, $sql);  

if($conn->affected_rows == 1){ // Login succeeded, cookie is being set
  setcookie("auth", $cookie_value, time() + (86400 * 30)); 
  file_put_contents('php://stderr', print_r("Login successful, cookies set to " . bin2hex($cookie_value) . "\n", TRUE));
  header("Location: success.html");
  exit();

} else{
  file_put_contents('php://stderr', print_r("Login failed: " . $conn->error . "\n", TRUE));
  echo "Login failed.";
  header("Location: failure.html");
  exit();  
}

?>
