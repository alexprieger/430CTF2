<?php
$servername = "localhost";
$username = "server";
$password = "pbN967bgWUAgdb5X3BmBxI2F";
$dbname = "bank";

$numberOfDesiredBytes = 16;
$cookies = null;

file_put_contents('php://stderr', print_r("Registering\n", TRUE));

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// getting username and password from the url
$reg_username = $_POST['user'];
$reg_password = $_POST['pass'];

//prevent mysqli injection
$username = stripcslashes($reg_username);  
$password = stripcslashes($reg_password);  
$username = mysqli_real_escape_string($conn, $username);  
$password = mysqli_real_escape_string($conn, $password);

// we can't require passwords to be strong according to assignment - desiree

// verify that the username is not already taken
$sql = "SELECT username FROM users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  file_put_contents('php://stderr', print_r("Username already taken\n", TRUE));
  echo "Username already taken";
  exit();
}

// initialize a salt value for the user
$salt = random_bytes($numberOfDesiredBytes);

// hashing password
$hashed_password = hash("sha256", $password.$salt); 

// enter the data into the database
$sql = "INSERT INTO users (username, password, salt)
VALUES ('$username', '$hashed_password', X'" . bin2hex($salt) . "')";
$result = $conn->query($sql);

if(!$result) {
  file_put_contents('php://stderr', print_r("Error creating record with statement: " . $sql . ", gave error: " . $conn->error . "\n", TRUE));
}

$last_id = $conn->insert_id;

$sql = "INSERT INTO balances (users_iduser)
VALUES ({$last_id})";
$result = $conn->query($sql);

if(!$result) {
  file_put_contents('php://stderr', print_r("Error creating record: " . $conn->error . "\n", TRUE));
  echo "Error creating record";
} else {
  file_put_contents('php://stderr', print_r("New record created successfully\n", TRUE));
  echo "New record created successfully";
}

// close the connection
$conn->close();

?>
