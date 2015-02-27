<?php
require_once("functions.php");
// if user is logged in && the data is posted
if ( checkSession() && $_SERVER["REQUEST_METHOD"] == "POST" ) 
{
	//get user info
	$userId = $_SESSION["user"]["userId"];

	$sql 	= "SELECT user_email , user_name
			   FROM user_accounts
			   INNER JOIN user_info
			   ON user_accounts.user_id = user_info.user_id
			   WHERE user_accounts.user_id = '$userId'";

	$result 	= getDbContent($sql);

	$oldEmail 	= $result[0]["user_email"];
	$oldName 	= $result[0]["user_name"];

	$makeMessage = "";

	########## NAME ###########
	//if name is set and is not empty
	if ( isset($_POST["userName"]) && !empty($_POST["userName"] && $_POST["userName"] !== $oldName) ) 
	{
		$name = strip_tags($_POST["userName"]);

		//if lenght of name is to large.
		if ( strlen($name) > 40 ) 
		{
			$makeMessage .= "Användarnamnet var för långt. (max 40 tecken) ";
			$name = $oldName;
		}

		// remove whitespaces
		$name = preg_replace("/\s+/", "", $name);

		// check if username not starts with @, add it
		if ( isUserName($name) == false ) 
		{
			$name = "@".$name;
		}

		// check if the username already is in use
		if ( validateName($name) == false) 
		{	
			$makeMessage .= "Användarnamnet är upptaget ";
			$name = $oldName;
		}
	}
	else
	{
		$name = $oldName;
	}

	########## EMAIL ###########
	//if name is set and is not empty
	if ( isset($_POST["userEmail"]) && !empty($_POST["userEmail"]) && $_POST["userEmail"] !== $oldEmail ) 
	{
		$email = $_POST["userEmail"];

		//validate email
		if( !filter_var($email, FILTER_VALIDATE_EMAIL) )
		{
			$message .= "Epost adressen är felaktig. ";
			$email = $oldEmail;
		}

		//if lenght of email is to large.
		if ( strlen($email) > 140 ) 
		{
			$makeMessage .= "Epost adressen var för långt. (max 140 tecken) ";
			$email = $oldEmail;
		}

		// check if the email already is in use
		if ( validateEmail($email) == false) 
		{	
			$makeMessage .= "Epost adressen är upptagen ";
			$email = $oldEmail;
		}
	}
	else
	{
		$email = $oldEmail;
	}

	########## SEND INFO TO DB ###########
	//user id
	$userid = $_SESSION["user"]["userId"];

	// connect to db
	$db = connectToDb();

	// escape
	$name 		 =  mysqli_real_escape_string($db, $name);
	$email 		 =  mysqli_real_escape_string($db, $email);

	// prepare sql query
	$sql = "UPDATE user_accounts
			INNER JOIN user_info
			ON user_accounts.user_id = user_info.user_id
			SET user_info.user_name = '$name', user_accounts.user_email = '$email'
		  	WHERE user_accounts.user_id = '$userid'";


	// run and check sql query
	if ( !mysqli_query($db, $sql) ) 
	{
		$makeMessage .= "Något gick fel, prova igen.";
		$_SESSION["messages"] = array( "text" => $makeMessage );

		header("Location: ../profilePage.php?page=settingsProfile");
		exit;
	}
	// else data was inserted, everything is fine :)
	// close db connection
	mysqli_close($db);

	$_SESSION["messages"] = array("text" => $makeMessage );
	header("Location: ../profilePage.php?page=settingsProfile");
	exit;
}
else
{ 
	//if user is not logged in.. leave pls.
	header("Location: ../index.php");
}