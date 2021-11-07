<?php
	include 'include/include.php';

	// check session
	session_start();

	if(!isset($_SESSION['username'])) {
		header("Location: index.php");
	}

	// make sure it is ajax request
	function is_ajax_request(){
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
			$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
	}
	if(!is_ajax_request()) {exit;} 

	// connect to db
	$link = connect();

	// get id and username
	$id = isset($_POST['id']) ? $_POST['id'] : '';
	$username = $_SESSION['username'];

	// function to check db for existing favorites
	function inFavorites($id, $username, $link) {
		$sql = "SELECT * FROM favorites WHERE recipe_id = '$id' AND username = '$username'";
		$result = mysqli_query($link, $sql);
		return mysqli_num_rows($result) != 0;
	}

	// check if in or not in favorites
	if (!inFavorites($id, $_SESSION['username'], $link)){
		$stmt = mysqli_prepare($link, "INSERT INTO favorites VALUES (?,?)");
		mysqli_stmt_bind_param($stmt, "ss", $username, $id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
		echo 'true';
	} else {
		$sql = "DELETE FROM favorites WHERE username = '$username' AND recipe_id = '$id'";
		mysqli_query($link, $sql);
		echo 'false';
	}

	mysqli_close($link);

?>
