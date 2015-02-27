<?php
require_once("functions.php");
if ( checkSession() ) 
{
	$db 	= connectToDb();

	$userId = $_SESSION["user"]["userId"];

	$sql 	= "UPDATE user_info SET user_active = 0 WHERE user_id = '$userId'";
	mysqli_query($db, $sql);

	// close connection 
	mysqli_close($db);

	logOutUser();
}
