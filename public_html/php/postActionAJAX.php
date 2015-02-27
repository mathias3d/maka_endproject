<?php 
require_once("functions.php");

// if user is logged in && the data is posted
if ( checkSession() && $_SERVER["REQUEST_METHOD"] == "POST" ) 
{	
	//get user info
	$userId = $_SESSION["user"]["userId"];

	//only need PostID if it is a comment
	$postId = "";

	// need $oldUserImg for the validateAndUploadImg function
	$oldUserImg 	= "";

	########## POST TEXT or COMMENT ###########
	if ( isset($_POST["post_txt"]) && !empty($_POST["post_txt"]) )  
	{
		$text 	= strip_tags($_POST["post_txt"]);
	}
	elseif ( isset($_POST["comment"]) && !empty($_POST["comment"]) )
	{
		$text   = strip_tags($_POST["comment"]);
		$postId = $_POST["post_id"];
	}
	else
	{
		$text = null;
	}

	//if lenght of message is to large.
	if ( strlen($text) > 140 ) 
	{
		$_SESSION["messages"] = array( "text" => "Inlägget innehåller för många tecken " );
		/*header('Location: ../public_html/index.php');*/
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
		$makeMessage    = $returnedImgMsg["makeMessage"];

		$_SESSION["messages"] = array( "text" => $makeMessage );
	}
	else 
	{
		// if image isent set or not posted
		$makeImage   = null;
	}

	
	if ( $makeImage == null && $text == null )
	{
		$_SESSION["messages"] = array( "text" => "Inget postades. " );
		exit;
	}

	########## TAGS ###########
	// make youtube links to embedded videos and remove html and php tags
	$text = embedYoutube($text);

	//make the text-tags to links
	$text = tagsToLinks($text);

	// make text-names to links
	$text = namesToLinks($text);


	########## SEND INFO TO DB ###########
	// connect to db
	$db = connectToDb();

	// escape
	$postId     = mysqli_real_escape_string($db, $postId);
	$user_id 	= mysqli_real_escape_string($db, $userId);
	$dbtext		= mysqli_real_escape_string($db, $text);
	$makeImage  = mysqli_real_escape_string($db, $makeImage);

	if ( isset($_POST["makePost"]) ) 
	{
		// sql query
		$sql = "INSERT INTO user_posts (user_id, post_txt, post_img)
			  	VALUES ('$userId', '$dbtext', '$makeImage')";
	}

	if ( isset($_POST["makeComment"]) )
	{
		//sql query
		$sql = "INSERT INTO user_posts (user_id, post_txt, post_img, post_reply)
			  	VALUES ('$userId', '$dbtext', '$makeImage', '$postId')";
	}
	
	// run and check sql query
	if ( !mysqli_query($db, $sql) ) 
	{
		logError( "sql query failed, ". mysqli_error($db) . " " . basename(__FILE__) );
		exit;
	}

	// get the latest id
	$newPostId = mysqli_insert_id($db);

	// insert Tags to database
	tagsToDb($newPostId, $text);

	// close db connection
	mysqli_close($db);

	


//////////////////////////////////////////////////////////////////////////////////////
	$row 				= getUserInfo( $userId );
	$row["post_txt"] 	= $text;
	$row["post_img"] 	= $makeImage;
	$row["post_date"] 	= date("Y-m-d H:i:s");
	$row["post_id"] 	= $newPostId;
	$row["post_share"] 	= 0;

	$articles[] 		= $row;
//////////////////////////////////////////////////////////////////////////////////////

	if ( isset( $_POST["makePost"]) ) 
	{
		//print the $articles
		require_once("../includes/postsForeachFrame.php");
	}
	else
	{	//print the comments 
		$c_text 	= $row["post_txt"];
		$c_img	  	= "img/users/". $userId . "/" . $row["post_img"];
		$c_date		= viewTime( $row["post_date"] );
		$c_postId   = $row["post_id"];

		$c_user 	= $row["user_id"];
		$c_userName = $row["user_name"];
		$c_fullName = ucfirst($row["user_full_name"]);
		
		$c_userImg 	= "img/users/". $userId . $row["user_img"];

		include_once("../includes/postsForeachComments.php");
	}
}