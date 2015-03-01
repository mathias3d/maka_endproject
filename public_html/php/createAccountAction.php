<?php
require_once("functions.php");


// if user is not logged in.. Good then user can create one :)
if ( !checksession() ) 
{

	//if the data is posted && isset
	if ( $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user"], $_POST["pwd"], $_POST["email"]) )
	{	

		//if all fields are filled 
		if ( !empty($_POST["user"]) && !empty($_POST["pwd"]) && !empty($_POST["email"])) 
		{
			$fullName  	= $_POST["user"];
			$email   	= $_POST["email"];
			$password   = $_POST["pwd"];

			######################## VALIDATE INPUT ########################
			$validated = 1;

			//validate email
			if( !filter_var($email, FILTER_VALIDATE_EMAIL) )
			{
				$message .= "Epost adressen är felaktig. ";
				$validated = 0;
			}
			//validate name
			if ( !preg_match("/^[a-zA-Z -åäöÅÄÖ]*$/", $fullName) ) 
			{
				$message .= "Namnet får bara innehålla bokstäver och mellanslag. <br>";
				$validated = 0;
			}
			//validate password
			if ( !validatePassword($password) ) 
			{
				$message .= "Lösenordet är inte svårt nog. <br>";
				$validated = 0;
			}
			// check if email exists in db
			if ( !validateEmail($email) ) 
			{
				$message .= "Epost adressen är redan upptagen. <br>";
				$validated = 0;
			}
			// if any validation failed
			if ( $validated == 0 ) 
			{
				$_SESSION["messages"] = array( "text" => "$message" );
				header("Location: ../index.php");
				exit;
			}

			######################## MAKE $fullName or $email to a uniqe $username ########################
			$userName 	= makeUniqueUserName($fullName, $email);


			######################## MAKE UNIQUE ID ########################
			$userId 	= uniqueId();


			######################## CREATE USER / INSERT TO DB ########################
			// connect to db
			$db = connectToDb();

			// escape 
			$fullName	=  mysqli_real_escape_string($db, $fullName);
			$email 		=  mysqli_real_escape_string($db, $email);
			$password 	=  mysqli_real_escape_string($db, $password);

			######################## CONVERT INTO A HASCHED PASSWORD ########################
			$password 	= encrypt($password);

			// sql query
			$sql = "INSERT INTO user_info (user_full_name, user_name, user_id)
				  	VALUES ('$fullName', '$userName', '$userId')";

			// run and check sql query
			if ( !mysqli_query($db, $sql) ) 
			{
				logError( "sql query failed. " . mysqli_error($db) . " " . basename(__FILE__), dirname(__FILE__) );
				header("Location: ../index.php");
				exit;
			}


			// sql query 2
			$sql = "INSERT INTO user_accounts (user_id, user_pwd, user_email)
				  	VALUES ('$userId', '$password', '$email')";

			// run and check sql query 2
			if ( !mysqli_query($db, $sql) ) 
			{
				logError( "sql query failed. " . mysqli_error($db) . " " . basename(__FILE__), dirname(__FILE__) );
				header("Location: ../index.php");
				exit;
			}

			// close db connection
			mysqli_close($db);


			######################## CREATE FOLDER #######################
			//create and check user folder for storage of uploads //mkdir($path, 0777, true)
			$path = "../img/users/$userId";

			if ( !mkdir($path) ) 
			{
			    logError( "mkdir failed to create folder. " . basename(__FILE__), dirname(__FILE__)  );
			    header("Location: ../public_html/index.php");
				exit;
			}
			chmod($path, 0777);

		
			######################## LOGIN USER / CREATE SESSION #######################
			//create session 
			$_SESSION["user"]["loggedIn"] = true;
			$_SESSION["user"]["userId"]   = $userId;

			header("Location: ../profilePage.php");
			exit;
		}

		$message .= "Båda fälten måste fyllas i <br>";
		$_SESSION["messages"] = array( "text" => "$message" );
		header("Location: ../index.php");
		
	}
	
}
header("Location: ../index.php");	
