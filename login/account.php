<?php
session_start();

if(!isset($_SESSION['username'])) {
	header("Location: ../index.php");
}
?>


<!DOCTYPE html>

<html>
	<head>
		<title>RecipeDex Account</title>
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<?php include '../include/include.php' ?>
	</head>

	<body>
		<div style="width:400px; margin:auto;">
			<button type="button" name="gohome" class="btn btn-light pull-right" onclick="location.href='../home.php'">Back</button>
			<h3 class="text-center"><?php echo $_SESSION['username'];?>'s Account</h3>
			<form action="account.php" method="post">
				<div class="col text-center">
					<button type="button" class="btn btn-primary" onclick="location.href='view_favorites.php'">View Favorites</button><br><br>
					<button type="button" class="btn btn-primary" onclick="location.href='recommended.php'">Recommended Recipes</button><br><br>
					<button type="button" class="btn btn-primary" onclick="location.href='delete_account.php'">Delete Account</button><br><br>
					<button type="button" class="btn btn-primary" onclick="location.href='logout.php'">Logout</button>
				</div>
			</form>

 		 </div>
	</body>

</html>




