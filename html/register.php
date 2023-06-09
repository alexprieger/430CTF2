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
$reg_password = $_GET['pass'];

if(str_contains($reg_username, "@")) {
	die("Use your USC id (the part before @usc.edu), not your full email");
}

if($reg_password == "hello-world-i-am-a-password-so-secure") {
	die("Password may not be the example password");
}


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
			die("Password only have valid dictionary words: ". $split_pass[$i]." is not a valid word");
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

//prevent mysqli injection
$username = stripcslashes($reg_username);  
$username = mysqli_real_escape_string($conn, $username);  

// initialize a salt value for the user
$salt = random_bytes($numberOfDesiredBytes); // Binary format, needs to be treated as such in SQL with X'"..."

// hashing password
$hashed_password = md5($reg_password . $salt); // Formatted as ASCII characters of hexadecimal representation

// enter the data into the database
$sql = "INSERT INTO users (username, hashed_password, asymmetric_encrypted_password, salt)
VALUES ('$username', '$hashed_password', '" . bin2hex($encrypted_password) . "', X'" . bin2hex($salt) . "')";

// The error code returned when a query attempts to insert a value with a primary key that has already been used
// In this case, it's caused by the username having already been taken
const MYSQLI_DUPLICATE_KEY_ERRNO = 1062;

try {
	$result = $conn->query($sql);
	echo "New account created successfully";
} catch (mysqli_sql_exception $e) {
	if ($e->getCode() == MYSQLI_DUPLICATE_KEY_ERRNO) {
		echo "That username has already been taken";
	} else {
		echo "An unknown error occurred";
	}
}

// close the connection
$conn->close();

?>
