<?php 
require_once("functions.php");

session_start();

if ( $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user"], $_POST["pwd"]) && !empty($_POST["pwd"]) && !empty($_POST["user"]) ) 
{
	$db 	  = connectToDb();
	
	$username = mysqli_real_escape_string( $db, $_POST["user"] );
	$password = mysqli_real_escape_string( $db, $_POST["pwd"] );

	$password = encrypt( $password );

	$sql 	= "SELECT user_info.user_full_name, user_info.user_id
			   FROM user_info 
               LEFT JOIN user_accounts
               ON user_info.user_id = user_accounts.user_id
			   WHERE user_pwd = '$password' AND (user_name = '$username' OR user_email = '$username') AND user_active = 1";

	$result = mysqli_query($db, $sql);

	if ( $result ) 
	{
	    // determine number of rows result set
	    $rowcount = mysqli_num_rows($result);
	}
	else
	{
		//logError( "sql query failed. " . basename(__FILE__), "thisIsWrong" );
	}

	if ($rowcount == 1) 
	{
		//create session 
		$row 		  = mysqli_fetch_assoc($result);
		$userId 	  = $row["user_id"];

		$_SESSION["user"]["loggedIn"] = true;
		$_SESSION["user"]["userId"]   = $userId;

		mysqli_free_result($result);
		mysqli_close($db);
	}
	else
	{
		$message = "Fel lösenord eller användarnamn <br>";
		$_SESSION["messages"] = array( "text" => "$message" );
	}
	
}
else
{
	$message = "Båda fälten måste fyllas i <br>";
	$_SESSION["messages"] = array( "text" => "$message" );
}
header("Location: ../index.php");
