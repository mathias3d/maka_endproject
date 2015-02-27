<?php
require_once("functions.php");
// if user is logged in && the data is posted
if ( checkSession() && $_SERVER["REQUEST_METHOD"] == "POST" ) 
{
	//get user info
	$userId			= $_SESSION["user"]["userId"];
	$row 			= getUserInfo( $userId );

	$oldUserTxt		= $row["user_text"];

	$oldBgImg		= $row["user_bg_img"];
	$oldUserImg		= $row["user_img"];

	$makeMessage  	= "";



	########## PROFILE IMAGE UPPLOAD ###########
	//if profilePhoto isset and contains data
	if ( isset($_FILES["profilePhoto"]["name"][0]) && $_FILES["profilePhoto"]["size"][0] > 0) 
	{
		//rearange the $_FILES array
		$imgFile 	= reArrayFiles($_FILES["profilePhoto"]);
		$imgFile 	= $imgFile[0];
		
		//validate and try to upload img else return message array
		$returnedImgMsg = validateAndUploadImg($imgFile, $userId, $oldUserImg);

		//set variables for returned array
		$makeImage 		= $returnedImgMsg["makeImage"];
		$makeMessage   .= $returnedImgMsg["makeMessage"];
	}
	else 
	{
		// if profilePhoto isent set or not posted
		$makeImage   = $oldUserImg;
	}

	########## PROFILE BACKGROUND IMAGE UPPLOAD ###########
	//if profileBg isset and contains data
	if ( isset($_FILES["profilePhoto"]["name"][1]) && $_FILES["profilePhoto"]["size"][1] > 0) 
	{	
		//rearange the $_FILES array
		$imgFile 	= reArrayFiles($_FILES["profilePhoto"]);
		$imgFile 	= $imgFile[1];

		//validate and try to upload img else return message array
		$returnedImgMsg = validateAndUploadImg($imgFile, $userId, $oldBgImg);

		//set variables for returned array
		$makeBgImage 	= $returnedImgMsg["makeImage"];
		$makeMessage   .= $returnedImgMsg["makeMessage"];
	}
	else 
	{
		// if profileBg isent set or not posted
		$makeBgImage   = $oldBgImg;
	}

	########## PROFILE TEXT ###########

	//if profiltxt issset and is not empty
	if ( isset($_POST["profiltxt"]) && !empty($_POST["profiltxt"]) ) 
	{
		$profiltxt = nl2br( strip_tags($_POST["profiltxt"]) );

		//if lenght of message is to large.
		if ( strlen($profiltxt) > 140 ) 
		{
			$makeMessage .= "Texten var för lång, inget inlägg skapades. (max 140 tecken)";
			$profiltxt = $oldUserTxt;
		}
	}
	else
	{
		$profiltxt = $oldUserTxt;
	}

	########## SEND INFO TO DB ###########

	// connect to db
	$db = connectToDb();

	// escape
	$text 		 =  mysqli_real_escape_string($db, $profiltxt);
	$makeImage 	 =  mysqli_real_escape_string($db, $makeImage);
	$makeBgImage =  mysqli_real_escape_string($db, $makeBgImage);

	// prepare sql query
	$sql = "UPDATE user_info
			SET user_text = '$profiltxt', user_img = '$makeImage', user_bg_img = '$makeBgImage'
		  	WHERE user_info.user_id = '$userId'";

	// run and check sql query
	if ( !mysqli_query($db, $sql) ) 
	{
		$makeMessage .= "Något gick fel, inget uppdaterades";
		$_SESSION["messages"] = array("text" => $makeMessage );

/*		header("Location: ../public_html/profilePage.php?page=editProfile");
*/		exit;
	}
	// else data was inserted, everything is fine :)
	// close db connection
	mysqli_close($db);

	$_SESSION["messages"] = array("text" => $makeMessage );
	header("Location: ../profilePage.php?page=editProfile");
	exit;
}