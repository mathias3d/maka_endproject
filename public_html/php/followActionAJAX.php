<?php
require_once("functions.php");

if ( checkSession() == true && isset($_POST["follow"]) && !empty($_POST["follow"]) ) 
{   
	// connect to db
	$db		  = connectToDb();

	$followId = mysqli_real_escape_string($db, $_POST["follow"]);
	$userId   = $_SESSION["user"]["userId"];


	########## GET USERIDS that USER IS FOLLOWING, FROM DB ###########
    $followIdsArr = getUserFollowIds($userId);


    ########## GET NUMBER OF FOLLOWERS that profile owner has  ###########
	$sql 		  = "SELECT count(*) AS count
					 FROM user_follows
					 WHERE follow_user_id = '$followId'
					 GROUP BY follow_user_id";

	$result 	  = mysqli_query($db, $sql);

	//
	// check error here and log them
	//

	$numFollowers = (int)mysqli_fetch_assoc($result)["count"];

	mysqli_free_result($result);

    ########## IS USER ALREADY FOLLOWING THIS USER ? ###########
 	if ( in_array($followId, $followIdsArr) ) 
	{
		// REMOVE ID from DB
		$sql = "DELETE FROM user_follows WHERE user_id = '{$userId}' AND follow_user_id = '{$followId}'";

		//run and check query
		if ( !mysqli_query($db, $sql) ) 
		{
			logError( "sql query failed. " . mysqli_error($db) . " " . basename(__FILE__), dirname(__FILE__) );
		} 

		// -1 follower 
		$numFollowers = $numFollowers -1;
		$msg = " Följ";
	}
	else
	{	
		// Insert NEW ID to DB
		$sql = "INSERT INTO user_follows (user_id, follow_user_id) VALUES ('$userId', '$followId')";
		
		//run and check query
		if ( !mysqli_query($db, $sql) ) 
		{
			logError( "sql query failed. " . mysqli_error($db) . " " . basename(__FILE__), dirname(__FILE__) );
		} 

		// +1 follower 
		$numFollowers = $numFollowers +1;
		$msg = " Sluta följa";
	}

	$response = array(
		'follow' 	   => $msg,
		'numfollowers' => $numFollowers
	);

	print json_encode($response, JSON_FORCE_OBJECT);


	// close connection 
	mysqli_close($db);
}