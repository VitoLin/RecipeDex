<?php
session_start();

if(!isset($_SESSION['username'])){
	header("Location: ../index.php");
}
?>

<!DOCTYPE html>

<html>
	<head>
		<title>RecipeDex Recommendations</title>
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<?php include '../include/include.php' ?>
	</head>

	<body>
		<div style="width:400px; margin:auto;">
			<button type="button" class="btn btn-light pull-right" onclick="location.href='account.php'">Back</button>
			<h3 class="text-center">Not set up yet</h3>
 		 </div>
	</body>

</html>

