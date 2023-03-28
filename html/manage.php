<?php
$servername = "localhost";
$username = "server";
$password = "pbN967bgWUAgdb5X3BmBxI2F";
$dbname = "bank";

file_put_contents('php://stderr', print_r("Managing\n", TRUE));

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// getting username and password from the url
$param_action = $_GET['action'];
$param_amount = $_GET['amount'];

//prevent mysqli injection
$action = stripcslashes($param_action);
$amount = stripcslashes($param_amount);
$action = mysqli_real_escape_string($conn, $action);
$amount = mysqli_real_escape_string($conn, $amount);

/* Will need to identify user's account based on included cookie somehow.
   Until the cookies are properly set up, just using a static account*/
$auth_cookie = $_COOKIE['auth'];

if(isset($auth_cookie)) {
  file_put_contents('php://stderr', print_r("Using cookie " . bin2hex($auth_cookie) . "\n", TRUE));
  $sql = "SELECT iduser FROM users WHERE cookies = X'" . bin2hex($auth_cookie) . "'";
  $result = $conn->query($sql);
}

if ($result->num_rows === 1) {
  $row = $result->fetch_assoc();
  $user_account_id = $row["iduser"];
  file_put_contents('php://stderr', print_r("Logged in as user $user_account_id\n", TRUE));
} else {
  file_put_contents('php://stderr', print_r("Not logged in\n", TRUE));
  die("Not logged in.");
}

if(strcmp($action, "deposit") === 0) {
  $sql = "UPDATE balances SET balances = balances + {$amount} WHERE users_iduser = {$user_account_id}";
  $conn->query($sql);

  file_put_contents('php://stderr', print_r("Depositing $amount\n", TRUE)); // Log the deposit
} else if(strcmp($action, "withdraw") === 0) {
  $sql = "UPDATE balances SET balances = IF(balances >= $amount, balances -  $amount, balances) WHERE users_iduser = {$user_account_id}";
  $conn->query($sql);

  file_put_contents('php://stderr', print_r("Withdrawing $amount\n", TRUE)); // Log the withdrawal
} else if(strcmp($action, "balance") === 0) {
  $sql = "SELECT balances FROM balances WHERE users_iduser = {$user_account_id}";
  $result = $conn->query($sql);

  if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    echo "balance=" . $row["balances"];
    file_put_contents('php://stderr', print_r("Balance: {$row["balances"]}\n", TRUE)); // Log the balance
  } else {
    die("Invalid user");
  }

} else if(strcmp($action, "close") === 0) {
  $sql = "DELETE FROM users WHERE iduser = {$user_account_id}";
  $conn->query($sql);
  file_put_contents('php://stderr', print_r("Logging out\n", TRUE));
} else {
  file_put_contents('php://stderr', print_r("Not a valid action: $action\n", TRUE));
  echo "No valid action.";
}

// close the connection
$conn->close();

?>
