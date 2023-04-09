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
$reg_password = $_GET['password'];

$split_pass = explode("-",$reg_password);
if(sizeof($split_pass) < 8) { // check if there are at least 8 words
	echo "Password must be at least 8 words long";
	return;
}

//echo "made it pass the first check";

foreach($split_pass as $word) {
	if (!ctype_lower($word)) { // check if everything is lowercase
		echo "Password must be completely lowercase";
		return;
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
			echo "Password only have valid dictionary words: ". $split_pass[$i]." is not a valid word";
			return;
		}
	}
	fclose($file);
} catch(Exception $e) {
	echo "Error: " . $e->getMessage();
}


//echo "made it pass the third check";
//echo "Password is valid.";

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

$sql = "UPDATE users SET hashed_password = '$hashed_password' WHERE username = '$username'";
try {
	$result = mysqli_query($conn, $sql);
} catch (mysqli_sql_exception $e) {
	die("Error updating password: " . $e->getMessage());
}
if ($result == 1){//password successfully updated
    header("Location: success.html");
  exit();
} else {//password did not update
  header("Location: failure.html");
  exit();  
}

?>