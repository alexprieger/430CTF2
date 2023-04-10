<?php
$servername = "localhost";
$username = "server";
$password = "pbN967bgWUAgdb5X3BmBxI2F";
$dbname = "mydb";

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
  $salt_value = $salt_object->salt; // Binary format
  $hashed_password = md5($reg_password . $salt_value);
} else {
  die("No user found with that username");
}

$sql = "SELECT * FROM users WHERE username = '$username' AND hashed_password = '$hashed_password' LIMIT 1";
try {
  $result = mysqli_query($conn, $sql);
} catch (mysqli_sql_exception $e) {
  die("Error logging in: " . $e->getMessage());
}
$user_object = $result->fetch_object();

$login_file = fopen("login_stats.txt", "r");
$successful_logins = 0;
$failed_logins = 0;
$total_logins = 0;

while (($line = fgets($login_file)) !== false)
{
  $parts = explode(":", $line);
  if (count($parts) != 2)
    continue;

  $count = intval(trim($parts[1]));
  switch (trim($parts[0]))
  {
    case "S":
      $successful_logins = $count;
      break;
    case "F":
      $failed_logins = $count;
      break;
    case "L":
      $total_logins = $count;
      break;
  }
}

if($user_object != null) { // Login succeeded
  $successful_logins++;
  header("Location: success.html");
} else { // Login failed, passwords did not match
  $failed_logins++;
  header("Location: failure.html");
}

$total_logins = $successful_logins + $failed_logins;
$updated_file_contents = "S: $successful_logins\nF: $failed_logins\nL: $total_logins\n";
file_put_contents("login_stats.txt", $updated_file_contents);

exit();
?>