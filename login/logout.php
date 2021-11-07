<?php
session_start();

if(!isset($_SESSION['username'])){
	header("Location: ../index.php");
}
?>

<!DOCTYPE html>

<html>
	<head>
		<title>RecipeDex Logout</title>
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<?php include '../include/include.php' ?>
	</head>

	<body>
		<div style="width:400px; margin:auto;">
			<button type="button" class="btn btn-light pull-right" onclick="location.href='account.php'">Back</button>
			<h3 class="text-center">Logout of <?php echo $_SESSION['username'];?>'s Account</h3>
			<form action="logout.php" method="post">
				<div class="col text-center">
					<button type="submit" name="submit" class="btn btn-primary">Logout</button>
				</div>
			</form>

<?php
if(isset($_POST['submit'])) {
	unset($_SESSION['username']);
	header("Location: ../index.php");
}

?>

 		 </div>
	</body>

</html>

