<?php
session_start();

if(!isset($_SESSION['username'])){
	header("Location: ../index.php");
}
?>

<!DOCTYPE html>

<html>
	<head>
		<title>RecipeDex Delete Account</title>
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<?php include '../include/include.php' ?>
	</head>

	<body>
		<div style="width:400px; margin:auto;">
			<button type="button" class="btn btn-light pull-right" onclick="location.href='account.php'">Back</button>
			<h3 class="text-center">Delete Account</h3>
			<form action="delete_account.php" method="post">
				<div class="form-group">
					<label>Re-enter Username</label>
					<input class="form-control" name="username" placeholder="Username" autocomplete="off">
				</div>
				<div class="form-group">
					<label>Re-enter Password</label>
					<input type="password" class="form-control" name="password" placeholder="Password" autocomplete="off">
				</div>
				<div class="col text-center">
					<button type="submit" name="submit" class="btn btn-danger">Delete Account</button>
				</div>
			</form>

<?php
$link = connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = $_REQUEST['username'];
	$password = $_REQUEST['password'];
	if (empty($username) && !empty($password)){
		echo "Please enter a username";
	} elseif (empty($password) && !empty($username)){
		echo "Please enter a password";
	} elseif (!empty($username) && !empty($password)){
		if(!accountExists($username, $password, $link)) {
			print("Account does not exist");
		} else {
			if(deleteAccount($username, $password, $link)) {
				unset($_SESSION['username']);
				header("Location: deleted.php");
			} else {
				print("Error deleting account");
			}
		}
	}
}

function accountExists($username, $password, $link) {
	$sql = "SELECT * FROM accounts WHERE username = '$username' AND password = '$password'";
	$result = mysqli_query($link, $sql);
	return mysqli_num_rows($result) != 0;
}

function deleteAccount($username, $password, $link) {
	$sql = "DELETE FROM accounts WHERE username = '$username' AND password = '$password'";
	$status = mysqli_query($link, $sql);
	return $status;
}

?>
 		 </div>
	</body>

</html>
