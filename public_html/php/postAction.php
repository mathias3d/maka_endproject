<?php 
require_once("functions.php");

// if user is logged in && the data is posted
if ( checkSession() && $_SERVER["REQUEST_METHOD"] == "POST" ) 
{	
	//get user info
	$userId 		= $_SESSION["user"]["userId"];
	$row 			= getUserInfo( $userId );

	// need $oldUserImg for the validateAndUploadImg function
	$oldUserImg 	= "";
	

	########## POST TEXT or COMMENT ###########
	if ( isset($_POST["post_txt"]) && !empty($_POST["post_txt"]) )  
	{
		$text 	= nl2br( strip_tags($_POST["post_txt"]) );
	}
	elseif ( isset($_POST["comment"]) && !empty($_POST["comment"]) )
	{
		$text      = nl2br( strip_tags($_POST["comment"]) );
		$kvitterId = $_POST["kvitter_id"];
	}
	else
	{
		$text = null;
	}

	//if lenght of message is to large.
	if ( strlen($text) > 140 ) 
	{
		$_SESSION["messages"] = array( "text" => "Inlägget innehåller för många tecken " );
		header('Location: ../index.php');
		exit;
	}

	########## IMAGE UPPLOAD ###########
	//if image isset and contains data
	if ( isset($_FILES["image"]["name"]) && $_FILES["image"]["size"] > 0) 
	{

		$imgFile = $_FILES["image"];
		
		//validate and try to upload img else return message array
		$returnedImgMsg = validateAndUploadImg($imgFile, $userId, $oldUserImg);

		//set variables for returned array
		$makeImage 		= $returnedImgMsg["makeImage"];
		$makeMessage   .= $returnedImgMsg["makeMessage"];
	}
	else 
	{
		// if image isent set or not posted
		$makeImage   = null;
	}

	
	if ( $makeImage == null && $text == null )
	{
		$_SESSION["messages"] = array( "text" => "Inget postades. " );
		header('Location: ../index.php');
		exit;
	}
		
		
	########## SEND INFO TO DB ###########
	// connect to db
	$db = connectToDb();

	// escape
	$kvitterId  = mysqli_real_escape_string($db, $kvitterId);
	$user_id 	= mysqli_real_escape_string($db, $userId);
	$text 		= mysqli_real_escape_string($db, $text);
	$makeImage  = mysqli_real_escape_string($db, $makeImage);

	########## TAGS ###########
	// make youtube links to embedded videos and remove html and php tags
	$text = embedYoutube($text);

	//make the text-tags to links
	$text = tagsToLinks($text);

	// make text-names to links
	$text = namesToLinks($text);

	if ( isset($_POST["makePost"]) ) 
	{
		// sql query
		$sql = "INSERT INTO user_posts (user_id, post_txt, post_img)
			  	VALUES ('$userId', '$text', '$makeImage')";
	}

	if ( isset($_POST["makeComment"]) )
	{
		//sql query
		$sql = "INSERT INTO user_posts (user_id, post_txt, post_img, post_reply)
			  	VALUES ('$userId', '$text', '$makeImage', '$kvitterId')";
	}
	
	// run and check sql query
	if ( !mysqli_query($db, $sql) ) 
	{
		logError( "sql query failed, ". mysqli_error($db) . " " . basename(__FILE__), dirname(__FILE__)  );
		
		header("Location: ../index.php");
		exit;
	}


	// get the latest id
	$postId = mysqli_insert_id($db);

	// insert Tags to database
	tagsToDb($postId, $text);

	// close db connection
	mysqli_close($db);

	$_SESSION["messages"] = array( "text" => $makeMessage );
}

header('Location: ../index.php');


