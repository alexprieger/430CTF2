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
echo "username is: " . $reg_username ." ";

//to prevent from mysqli injection  
$username = stripcslashes($reg_username);  
$username = mysqli_real_escape_string($conn, $username);  

// retrieving salt
$retrieve_salt_sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
echo " done with salt sql ";
try {
	$salt_result = $conn->query($retrieve_salt_sql);
} catch (mysqli_sql_exception $e) {
	die("Error logging in: " . $e->getMessage());
}
$salt_object = $salt_result->fetch_object();  
echo " salt_object done ";

if($salt_object != null) {
  $email = $username + "@usc.edu";
  $reset = random_bytes($numberOfDesiredBytes);
  echo "reset is: " . $reset ." ";
  $sql_reset = "UPDATE users SET reset = '$reset' WHERE username= '$username'";

  $reset_link = "http://3.133.129.167/reset.html?reset=$reset";
  echo "\nReset link is: " . $reset_link;
  // // $shell_cmd = "echo "Reset your password here: $reset_link" | mail -s "CTF Team 4 Reset Password" $email";
  // // exec("echo \"Reset your password here: $reset_link\" | mail -s \"CTF Team 4 Reset Password\" $email");
  // // $output = shell_exec($shell_cmd);
  // // echo $output;
  $output2 = shell_exec("bash ./mail.sh $reset_link $email");
  // // echo $output2;
  echo " Reset link sent to $email";
  header("Location: success.html");
  exit();
 } else {
         header("Location: failure.html");
   exit(); 
 }

?>
