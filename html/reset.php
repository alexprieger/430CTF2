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
$user_reset = $_GET['reset'];
// echo " user reset " . $user_reset;
$decoded_reset = hex2bin($user_reset);
$reg_password = $_GET['password'];
$reg_password1 = $_GET['password1'];

if($reg_password != $reg_password1) {
	die("Passwords do not match.");
}

if($reg_password == "hello-world-i-am-a-password-so-secure") {
	die("Password may not be the example password");
}

$user_sql = "SELECT * FROM users WHERE reset = '$decoded_reset' LIMIT 1";
try {
	$user_result = $conn->query($user_sql);
} catch (mysqli_sql_exception $e) {
	die("Error logging in: " . $e->getMessage());
}
$username_object = $user_result->fetch_object();  
if ($username_object != null) {
	$username = $username_object->username;
} else {
	die("No user found with that reset code");
}
// echo " username is " . $username;

$split_pass = explode("-",$reg_password);
if(sizeof($split_pass) < 8) { // check if there are at least 8 words
	die("Password must be at least 8 words long");
}

//echo "made it pass the first check";

foreach($split_pass as $word) {
	if (!ctype_lower($word)) { // check if everything is lowercase
		die("Password must be completely lowercase");
	}
}

//echo "made it pass the second check";
// check if all words are in the dictionary
try{
	$file = fopen("dictionary.txt", "r");
	for($i=0; $i < sizeof($split_pass); $i++) {
		$found = false;
		while(!feof($file)) {
			$line = fgets($file);
			if(trim($line) == trim($split_pass[$i])) {
				//echo " " . $line . " matches " . $split_pass[$i];
				$found = true;
				rewind($file);
				break;
			}
		}
		if(!$found) {
			die("Password must only have valid dictionary words: ". $split_pass[$i]." is not a valid word");
		}
	}
	fclose($file);
} catch(Exception $e) {
	echo "Error: " . $e->getMessage();
}


if (strlen($reg_password) > 384) {
	die("Password must be 384 characters or less.");
}

openssl_public_encrypt($reg_password, $encrypted_password, openssl_pkey_get_public("file://id_rsa.pem"));

//echo "made it pass the third check";
//echo "Password is valid.";

//to prevent from mysqli injection  
// $username = stripcslashes($reg_username);  
// $username = mysqli_real_escape_string($conn, $username);  

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

$sql = "UPDATE users SET hashed_password = '$hashed_password', reset = NULL, num_password_reset = num_password_reset + 1, asymmetric_encrypted_password = '" . bin2hex($encrypted_password) . "' WHERE username = '$username'";
try {
	$result = mysqli_query($conn, $sql);
} catch (mysqli_sql_exception $e) {
	die("Error updating password: " . $e->getMessage());
}
if ($result == 1){//password successfully updated
	header("Location: reset_success.html");
  exit();
} else {//password did not update
	header("Location: reset_failure.html");
  exit();  
}

?>
