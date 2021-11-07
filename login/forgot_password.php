<!DOCTYPE html>

<html>
	<head>
		<title>RecipeDex Forgot Password</title>
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<?php include '../include/include.php' ?>
	</head>

	<body>
		<div style="width:400px; margin:auto;">
			<button type="button" name="goback" class="btn btn-light pull-right" onclick="location.href='login.php'">Back</button>
			<h3 class="text-center">Forgot Password</h3>
			<form action="forgot_password.php" method="get">
				<div class="form-group">
					<label>Enter Your Username</label>
					<input class="form-control" name="username" placeholder="Username" autocomplete="off">
				</div>
				<div class="col text-center">
					<button type="submit" name="findpword" class="btn btn-primary">Find Password</button>
				</div>
			</form>

<?php
$link = connect();

if (isset($_GET['username'])) {
	$username = $_GET['username'];
	$sql = "SELECT password FROM accounts WHERE username = '$username'";
	$result = mysqli_query($link, $sql);
	$tuple = mysqli_fetch_array($result, MYSQLI_ASSOC);
	if ($tuple != NULL) {
		$password = $tuple['password'];
		print("The password for $username is: $password");
	} else {
		print("Username $username not found");
	}
	
}

?>
 		 </div>
	</body>

</html>




