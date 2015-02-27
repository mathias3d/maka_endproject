<?php
require_once("functions.php");
if ( checkSession() ) 
{
	//connect to db
	$db 	= connectToDb();

	$userId = $_SESSION["user"]["userId"];


	$sql 	= "UPDATE user_info SET user_active = 0 WHERE user_id = '$userId'";
	// run and check query
	if ( !mysqli_query($db, $sql) ) 
	{
		logError( "sql query failed. " . mysqli_error($db) . " " . basename(__FILE__), dirname(__FILE__) );
	}

	$sql 	= "UPDATE user_posts SET post_active = 0 WHERE user_id = '$userId'";
	if ( !mysqli_query($db, $sql) ) 
	{
		logError( "sql query failed. " . mysqli_error($db) . " " . basename(__FILE__), dirname(__FILE__) );
	}

	// close connection 
	mysqli_close($db);

	logOutUser();
}
