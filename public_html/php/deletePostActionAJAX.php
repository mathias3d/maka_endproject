<?php 
require_once("functions.php");

if ( checkSession() && isset($_POST["del"]) && !empty($_POST["del"]) ) 
{	
	$db 	= connectToDb();

	$deleteId 	= mysqli_real_escape_string($db, $_POST["del"]);
	$userId 	= $_SESSION["user"]["userId"];

	########## CHECK IF USER IS OWNER OF POST ###########
	$db 		= connectToDb();
	$sql 		= "SELECT * FROM user_posts WHERE user_id = '$userId' AND post_id = '$deleteId'";
	$result 	= mysqli_query($db, $sql);

	if ( !$result ) 
	{
		logError( "Delete Action Failed, ". mysqli_error($db) . " " . basename(__FILE__), dirname(__FILE__) );
		$_SESSION['messages']["text"] = "Ett fel uppstod";
		exit;
	}

	$row 		= mysqli_fetch_assoc($result)["post_id"];
	mysqli_free_result($result);

	########## "DELETE" POST ###########
	if ($row == $deleteId) 
	{
		$sql = "UPDATE user_posts SET post_active = 0 WHERE post_id = '$deleteId' ";

		if ( !mysqli_query($db, $sql) ) 
		{
			logError( "Delete Action Failed, ". mysqli_error($db) . " " . basename(__FILE__), dirname(__FILE__) );
			$_SESSION['messages']["text"] = "Ett fel uppstod";
			exit;
		}
		else
		{
			$_SESSION['messages']["text"] = "Inlägget raderades";
		}
	}
	else
	{
		//message you can not delete this post
		$_SESSION['messages']["text"] = "Du kan inte radera detta inlägget";
	}

	print json_encode(array('msg'=>$_SESSION['messages']["text"] ));

	// close connection 
	mysqli_close($db);
}