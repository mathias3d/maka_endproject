<?php
require_once("functions.php");

if ( checkSession() && isset($_POST["like"]) && !empty($_POST["like"]) ) 
{	
	$db 	= connectToDb();

	$likeId = mysqli_real_escape_string($db, $_POST["like"]);
	$userId = $_SESSION["user"]["userId"];


	########## GET POST-ID`S USER ALREADY LIKED FROM DB ###########

    $likeIdsArr	= getUserLikes($userId);



	########## GET NUMBER OF LIKES ON POST ###########

	$numLikes 	= getThePostLikes($likeId)["count(*)"];


	########## HAVE USER ALREADY LIKED THIS POST? ###########
	if ( in_array($likeId, $likeIdsArr) ) 
	{
		//REMOVE, USER LIKE
		$numLikes 	= $numLikes -1;

		// DELETE OLD like from DB
		$sql = "DELETE FROM user_likes WHERE user_id = '{$userId}' AND liked_post_id = '{$likeId}'";

		//run and check query
		if ( !mysqli_query($db, $sql) ) 
		{
			logError( "sql query failed. " . mysqli_error($db) . " " . basename(__FILE__), dirname(__FILE__) );
		} 
	}
	else
	{
		// ADD, USER LIKE
		$numLikes 	= $numLikes +1;

		// Insert NEW like to DB
		$sql = "INSERT INTO user_likes (user_id, liked_post_id) VALUES ('$userId', '$likeId')";
		
		//run and check query
		if ( !mysqli_query($db, $sql) ) 
		{
			logError( "sql query failed. " . mysqli_error($db) . " " . basename(__FILE__), dirname(__FILE__) );
		} 
	}

	// close connection 
	mysqli_close($db);

	print $numLikes;
}