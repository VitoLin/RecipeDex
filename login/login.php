<!DOCTYPE html>

<html>
	<head>
		<title>RecipeDex Login</title>
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<?php include '../include/include.php' ?>
	</head>

	<body>
		<div style="width:400px; margin:auto;">
			<button type="button" name="gohome" class="btn btn-light pull-right" onclick="location.href='../index.php'">Home</button>
			<h3 class="text-center">Login</h3>
			<form action="login.php" method="post">
				<div class="form-group">
					<label>Username</label>
					<input class="form-control" name="username" placeholder="Username">
				</div>
				<div class="form-group">
					<label>Password</label>
					<input type="password" class="form-control" name="password" placeholder="Password">
				</div>
				<button type="submit" name="login" class="btn btn-primary" onclick="location.href='../index.php'">Login</button>
				<button type="button" class="btn btn-link" onclick="location.href='create_account.php'">Create Account</button>
				<button type="button" class="btn btn-link" onclick="location.href='forgot_password.php'">Forgot Password</button>
			</form>


<?php
session_start();
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
			print("Username or Password incorrect");
		} else {
			$_SESSION['username'] = $username;
			header("Location: ../home.php");
		}
	}
}

function accountExists($username, $password, $link) {
	$sql = "SELECT * FROM accounts WHERE username = '$username' AND password = '$password'";
	$result = mysqli_query($link, $sql);
	return mysqli_num_rows($result) != 0;
}

?>


 		 </div>
	</body>

</html>
