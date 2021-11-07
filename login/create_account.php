<!DOCTYPE html>

<html>
	<head>
		<title>RecipeDex Create Account</title>
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<?php include '../include/include.php' ?>
	</head>

	<body>
		<div style="width:400px; margin:auto;">
			<button type="button" name="gohome" class="btn btn-light pull-right" onclick="location.href='login.php'">Back</button>
			<h3 class="text-center">Create Account</h3>
			<form action="create_account.php" method="post">
				<div class="form-group">
					<label>Username</label>
					<input class="form-control" name="username" placeholder="Username" autocomplete="off">
				</div>
				<div class="form-group">
					<label>Password</label>
					<input class="form-control" name="password" placeholder="Password" autocomplete="off">
				</div>
				<div class="col text-center">
					<button type="submit" name="submit" class="btn btn-primary">Create Account</button>
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
		if (usernameExists($username, $link)) {
			print("Username $username is already taken");
		} else {
			$stmt = mysqli_prepare($link, "INSERT INTO accounts VALUES (?, ?)");
			mysqli_stmt_bind_param($stmt, "ss", $_POST['username'], $_POST['password']);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			print("Account for $username created");
		}
	
	}
}

function usernameExists($username, $link) {
	$sql = "SELECT * FROM accounts WHERE username = '$username'";
	$result = mysqli_query($link, $sql);
	return mysqli_num_rows($result) != 0;
}

?>
 		 </div>
	</body>

</html>




