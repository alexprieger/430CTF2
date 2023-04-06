<?php

alert("in email.php");
// the link in the email should redirect to "reset.html"
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
alert($reg_username);

$username = stripcslashes($reg_username);  

$username = mysqli_real_escape_string($conn, $username); 

// retrieving salt
$retrieve_salt_sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
try {
	$salt_result = $conn->query($retrieve_salt_sql);
} catch (mysqli_sql_exception $e) {
	die("Error finding user in: " . $e->getMessage());
}
$salt_object = $salt_result->fetch_object();  

if($salt_object != null) {
  alert("user found!");
  $salt_value = $salt_object->salt; // Binary format
  //username exists -> send email to redirect to "reset.html" with username "$username"
  $email = $username + "@usc.edu";
  $reset_link = "http://localhost:8080/reset.html?user=$username";
  exec("echo \"Reset your password here: $reset_link\" | mail -s \"CTF Team 4 Reset Password\" $email");
  // shell_exec(../mail.sh $username $reset_link);
  header("Location: success.html");
  exit();
  
}
else{
  alert("Can't find user");
}

?>