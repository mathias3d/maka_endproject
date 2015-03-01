<?php 
require_once("db_connect.php");



/* #################################################
   #  FUNCTIONS TO BE SORTED
   ################################################# */
function uniqueId()
{
	$sql 		= "SELECT user_id FROM user_info";
	$userIdArr  = getDbContent($sql);
	$unique 	= false;

	// reconstruct the array
	foreach ($userIdArr as $value) 
	{
		foreach ($value as $v) 
		{
			$IdArr[] = $v;
		}
	}

	while ( $unique == false ) 
	{
		$newId = substr( str_shuffle( hash("sha512", microtime() ) ), 0, 20 );

		if ( in_array($newId, $IdArr) == false ) 
		{
			$unique = true;
			return $newId;
		}
	}
}


function logError($error, $dir)
{
    $error = date('Y-m-d H:i:s') . " " . $error . "\r\n"; 
    file_put_contents($dir."/log/errorLog.txt", $error, FILE_APPEND);
}



function countNews()
{
	$db 	= connectToDb();
	//måste joina in user info så att jag kan sätta user_active = 1
	$result = mysqli_query($db, "SELECT count(*) FROM user_posts WHERE post_reply = 0 AND post_active = 1");

	$row 	= mysqli_fetch_row($result);

	mysqli_close($db);
	return $row[0];
}




function countPostComments($postId)
{
	$db 	= connectToDb();

	$postId =(int)$postId;
	//måste joina in user info så att jag kan sätta user_active = 1
	$result = mysqli_query($db, "SELECT count(*) FROM user_posts WHERE post_reply = '$postId' AND post_active = 1");

	$row 	= mysqli_fetch_row($result);

	mysqli_close($db);
	return $row[0];
}



function encrypt($input)
{
	$salt = "2a9lVJyGhFVu7xTtlACA9Clp8Y595A";
	return  trim( 
		    	base64_encode(
		    		mcrypt_encrypt( MCRYPT_BLOWFISH, $salt, $input, MCRYPT_MODE_ECB, 
		    			mcrypt_create_iv(	
		    				mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB), MCRYPT_RAND
		    			)
		    		)
		    	) 
		    );
}

function isMyComment($postId)
{
	$db 	= connectToDb();

	$myId 	= $_SESSION["user"]["userId"];
	$postId =(int)$postId;

	$result = mysqli_query($db, "SELECT count(*) FROM user_posts WHERE post_reply = '$postId' AND user_id = '$myId' AND post_active = 1");

	$row 	= mysqli_fetch_row($result);

	mysqli_close($db);
	return $row[0];
}



/* #################################################
   # DATABASE FUNCTIONS
   ################################################# */

// returns a connection to db
function connectToDb() 
{
	$connect = mysqli_connect(HOST_NAME, USER_NAME, PASSWORD, DB_NAME);
	mysqli_set_charset($connect,"utf8");

	/* check connection */
	if ( mysqli_connect_errno() ) 
	{
	    logError("Connect failed:" . " " . mysqli_connect_error(), dirname(__FILE__), dirname(__FILE__) );
	    exit;
	}
	else
	{
		return $connect;
	}
}


// returns array with all rows from input sql
function getDbContent($sql) 
{
	$db 	= connectToDb();
	$array 	= [];

	if ( !$result = mysqli_query($db, $sql) ) 
	{ 
		logError("mysqli_query fail:" . " " . mysqli_error($db), dirname(__FILE__), dirname(__FILE__) );
    }
    else
    {
    	while ( @$results = mysqli_fetch_assoc($result) ) 
		{
			$array[] = $results;
		}

    }
	
	@mysqli_free_result($result);
	mysqli_close($db);
	return $array;
}

function getUserInfo($userId) 
{
	$db 	= connectToDb();
	$userId = mysqli_real_escape_string($db,$userId);
	$row 	= [];
	$result = mysqli_query($db, "SELECT * FROM user_info WHERE user_id = '$userId' AND user_active = 1");

	$row 	= mysqli_fetch_assoc($result);

	mysqli_close($db);
	return $row;
}


//kontrollera denna funktionens sql... det är den gillade användaren som måste vara active
function getUserLikes($userId) 
{
	$db 		= connectToDb();
	$userId 	= mysqli_real_escape_string($db,$userId);
	$likeIdsArr = [];
	$result 	= mysqli_query($db, "SELECT liked_post_id 
									 FROM user_likes 
									 INNER JOIN user_info 
									 ON user_likes.user_id = user_info.user_id 
									 INNER JOIN user_posts
									 ON user_likes.liked_post_id = user_posts.post_id
									 WHERE user_likes.user_id = '$userId' AND user_info.user_active = 1 AND user_posts.post_active = 1");

	while ($results = mysqli_fetch_assoc($result)) 
	{
		$likeIdsArr[] = $results["liked_post_id"];
	}
	
	mysqli_free_result($result);
	mysqli_close($db);
	return $likeIdsArr;
}

// fixa så att om en person som gillat inlägget inte är active längre så skall denna minska i antal
function getThePostLikes($postId) 
{
	$postId 	= (int)$postId;
	$db 		= connectToDb();
	$result 	= mysqli_query($db, "SELECT count(*) FROM user_likes WHERE liked_post_id = {$postId}");
	
	$row = mysqli_fetch_assoc($result);

	mysqli_free_result($result);
	mysqli_close($db);
	return $row;
}


// get the id´s of the users that "USER" is following
function getUserFollowIds($userId) 
{
	
	$db 		= connectToDb();
	$userId 	= mysqli_real_escape_string($db,$userId);
	$idsArr 	= [];
	$result 	= mysqli_query($db, "SELECT follow_user_id
									 FROM user_follows
									 INNER JOIN user_info
									 ON user_follows.follow_user_id = user_info.user_id                   
									 WHERE user_follows.user_id = '$userId' AND user_info.user_active = 1");

	while ($results = mysqli_fetch_assoc($result)) 
	{
		$idsArr[] = $results["follow_user_id"];
	}

	mysqli_free_result($result);
	mysqli_close($db);
	return $idsArr;
}

// Get the id´s of users that follows "USER"
function getFollowersIds($userId) 
{
	$db 		= connectToDb();
	$userId 	= mysqli_real_escape_string($db,$userId);
	$idsArr 	= [];
	$result 	= mysqli_query($db, "SELECT user_follows.user_id 
									 FROM user_follows 
									 INNER JOIN user_info
									 ON user_follows.user_id = user_info.user_id
									 WHERE follow_user_id = '$userId' AND user_info.user_active = 1");
	
	while ( $results = mysqli_fetch_assoc($result) ) 
	{
		$idsArr[] = $results["user_id"];
	}

	mysqli_free_result($result);
	mysqli_close($db);
	return $idsArr;
}


function getNrUserPosts($userId) 
{
	$db 		= connectToDb();
	$userId 	= mysqli_real_escape_string($db,$userId);
	$result 	= mysqli_query($db, "SELECT post_id FROM user_posts WHERE user_id = '$userId' AND user_posts.post_reply = 0 AND user_posts.post_active = 1");

	$numberOfRows = mysqli_num_rows($result);

	mysqli_free_result($result);
	mysqli_close($db);
	return $numberOfRows;
}


/* #################################################
   # SESSION / COOCKIE / VALIDATION 
   ################################################# */
//return true or false if user is logged in
function checkSession()
{
	@session_start();

	if ( isset($_SESSION["user"]["loggedIn"], $_SESSION["user"]["userId"]) )
	{
		if ($_SESSION["user"]["loggedIn"] == true) 
		{
			return true;
		}
	}
	else
	{
		return false;
	}

}

function logOutUser()
{
	@session_start();

	session_unset(); 
	session_destroy();

	// set lifetime of cookie to 0 to remove the coockie file
	//setcookie("loggedIn", "", 0);
	//setcookie("user", "", 0);
	
	// Start empty session
	session_start();

	// set message and return user
	$_SESSION['messages'] = array( "text" => "Du är nu utloggad") ;

	header('Location: ../index.php');
	exit;
}

function validatePassword($password)
{
	$isValid  = false;
	// check lenght 
	if (strlen($password) >= 8)
	{
	    $hasNumber = false;
	    $hasLetter = false;

	    for($i = 0; $i < strlen($password); $i++)
	    {
	        if ( is_numeric($password[$i]) )
	        {
	            $hasNumber = true;
	        }
	        else
	        {
	            $hasLetter = true;
	        }
	    }

	    if ($hasNumber && $hasLetter)
	        $isValid = true;
	}

	if ( $isValid )
	{
		return true;
	}
	else
	{
		return false;
	}
	    
}

function validateEmail($email)
{
	// get all users
	$sql = "SELECT user_email FROM user_accounts";
	$usersArray = getDbContent($sql);

	// check if email already exists
	$uniqueEmail = true;
	foreach ($usersArray as $row) 
	{	
		if ( $email == $row["user_email"] ) 
		{
			$uniqueEmail = false;
		}
	}
	// if email exists 
	if ( $uniqueEmail == false) 
	{
		return false;
	}
	else
	{
		return true;
	}
}
function validateName($name)
{
	// get all users
	$sql = "SELECT user_name FROM user_info";
	$usersArray = getDbContent($sql);

	// check if name already exists
	$uniqueName = true;
	foreach ($usersArray as $row) 
	{	
		if ( $name == $row["user_name"] ) 
		{
			$uniqueName = false;
		}
	}
	// if name exists 
	if ( $uniqueName == false) 
	{
		return false;
	}
	else
	{
		return true;
	}
}


/* #################################################
   # OTHER FUNCTIONS
   ################################################# */

function viewTime($dateTimeStr)
{	
	$timeStr 	= strtotime($dateTimeStr);

	// TRUE(1) if $dateTimeStr is inside timespan time(NOW) - (60min)
	$oneHour 	= ( time()-(60*60) ) < $timeStr;
	$oneDay 	= ( time()-(60*60*24) ) < $timeStr;

	if( $oneHour ) 
	{
		//print "Print MINUTES ";
		return round(abs(time() - $timeStr) / 60) . " min. sedan.";
	}
	elseif( $oneDay ) 
	{
		//print "PRINT HOURS ";
		return round(abs(time() - $timeStr) / 60 / 60 ) . " tim. sedan.";
	}
	else
	{
		//print "PRINT DATE ";
		return "den " . date("d/m - Y", $timeStr);
	}
}


function makeUniqueUserName($fullName, $email)
{
	$db  		= connectToDb();
	$sql 		= "SELECT user_name FROM user_info";

	##### rearange the $email #####
	// take first part of email ( before @ )
	$index = strpos($email, "@");
	$email = substr($email, 0, $index);
	// add @ at the begining of name
	$email = "@".$email;

	##### rearange the $fullName #####
	// remove whitespaces
	$fullName  = preg_replace("/\s+/", "", $fullName);
	// add @ at the begining of name
	$fullName  = "@".$fullName;

	##### make an array with existing user_names #####
	// get array of the db user_name´s
	$usersArray = getDbContent($sql);

	// reconstruct the $usersArray
	foreach ($usersArray as $user) 
	{
		$usersArray[] = $user["user_name"];
	}

	#### make the name unique ####

	// if $email is in array (not unique) 
	if ( in_array($email, $usersArray) ) 
	{
		$unique = false;
	}
	else
	{
		$unique = true;
		return $email;
	}

	// try to use email as unique name
	while ( $unique == false ) 
	{
		//remove one letter at the end of the $email
		$email = substr($email, 0, -1);

		if ( $email == "@" || $email == "" || $email == " ") 
		{
			break;
		}
		if ( in_array($email, $usersArray) == false ) 
		{
			$unique = true;
			$uniqueName = $email;
		}
	}

	// if $fullName is in array (not unique) 
	if ( in_array($fullName, $usersArray) ) 
	{
		$unique = false;
	}
	else
	{
		$unique = true;
		return $fullName;
	}

	// try to use fullName as unique name
	$name = $fullName;
	while ( $unique == false ) 
	{
		$name = substr($name, 0, -1);

		if ( $name == "@" || $name == "" || $name == " ") 
		{
			break;
		}
		if ( in_array($name, $usersArray) == false ) 
		{
			$unique = true;
			$uniqueName = $name;
		}
	}

	// try to use fullName plus some random number as unique name
	while ( $unique == false ) 
	{
		$fullName = $fullName . mt_rand(10,99);

		if ( in_array($fullName, $usersArray) == false ) 
		{
			$unique = true;
			$uniqueName = $fullName;
		}
	}

	//when it gets unique return it
	return $uniqueName;
}

function repopInput($field)
{
	if ( isset($_SESSION["reinput"][0][$field]) )
	{

		$input = $_SESSION["reinput"][0][$field];

		unset($_SESSION["reinput"][0][$field]);
	}
	else
	{
		$input = "";
	}
	return $input;
}

function checkAndPrintMessages()
{
	//check if there are any messages to view 
	if ( isset($_SESSION['messages']["text"]) && !empty($_SESSION["messages"]["text"]) ) 
	{
		print '<div class="message">';
		print	'<div class="messageBox">';
			
			// view messages and them empty them.
		    print $_SESSION['messages']["text"];
		    
		    unset($_SESSION['messages']); 
			
		print '</div>';
		print '<img class="pull-left img-res" src="img/design/chat-bubble.png">';
		print '<img class="img-res bee" src="img/design/hiveLogo.png">';
	    print '</div>';
	} 
}

// observe you should require this function: require_once( getRequiredPage($allowedPages) );
function getRequiredPage($allowedPages, $standardPage)
{
	// include different pages based on GET-links (.subnav a href)
	if ( isset($_GET["page"]) ) 
	{	
		$page 		  = $_GET["page"];

		//if someone edits the GET-link
		if ( ! in_array($page, $allowedPages) ) 
		{
			//this needs to be fixed on profilepage (header already sent), but is working on startpage?
			// reload page without the GET
			header("Location: ../profilePage.php");
			exit;
		}
		
		return "includes/$page.php"; 
	}
	else
	{
		// Show this as standard (if no .subnav a href is clicked)
		return $standardPage; 
	}

}

function reArrayFiles($filePost) 
{
    $fileArray 	= array();
    $fileCount  = count($filePost['name']);
    $fileKeys 	= array_keys($filePost);

    for ($i = 0; $i < $fileCount; $i++) 
    {
        foreach ($fileKeys as $key) {
            $fileArray[$i][$key] = $filePost[$key][$i];
        }
    }
    return $fileArray;
}


/* #################################################
   # UPLOAD FUNCTIONS
   ################################################# */
function validateAndUploadImg($imgFile, $userFolder, $oldUserImg)
{
	//set variable
	$uploadOk = 1;

	//image setup 
	$dir            = "../img/users/$userFolder/";
	$target_dir     = $dir . basename( $imgFile["name"]);

	$allowedTypes   = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
	$allowedExt		= array("png", "jpeg", "jpg", "gif");
	$file_type      = exif_imagetype($imgFile["tmp_name"]);

	$file_size      = $imgFile["size"];

	$file_name      = $imgFile['name'];
	$actual_name    = pathinfo($file_name,PATHINFO_FILENAME);
	$original_name  = $actual_name;
	$file_ext       = pathinfo($file_name, PATHINFO_EXTENSION);

	$makeMessage	= "";

	$specialChars   = preg_match( ' /[\'\/~`\!@#\$%\^&\*\(\)\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/ ' , $actual_name);
	
	// Tells whether the file was uploaded via HTTP POST
	if ( is_uploaded_file($imgFile['tmp_name']) ) 
	{
		//check for special characters
	    if ( $specialChars == 1 )
	    {
	        $makeMessage .= "Filnamnet innehåller otilllåtna bokstäver. ";
	        $uploadOk = 0;
	    }
	    //Check file name lenght
	    if ( strlen($actual_name) > 30 ) 
	    {
	        $makeMessage .= "Filnamnet är för långt, max 30 tecken. ";
	        $uploadOk = 0;
	    }
	    // Check file size (1mb max)
	    if ( $file_size > 1000000 ) 
	    {
	        $makeMessage .= "Din bild var för stor, max storlek är 1mb. ";
	        $uploadOk = 0;
	    }
	    // Only some filetypes are allowed 
	    if ( !in_array($file_type, $allowedTypes) || !in_array($file_ext, $allowedExt) ) 
	    {
	        $makeMessage .= "Bara .gif .jpeg och .png bilder får laddas upp. ";
	        $uploadOk = 0;
	    }
	    // Check if $uploadOk is set to 0 by an error
	    if ( $uploadOk == 0 ) 
	    {
	        $makeMessage .= "Din fil laddades inte upp. ";
	        $makeImage   = $oldUserImg;
	    }
		else// if everything is ok, try to upload file
		{
	        // Check if file already exists (rewrite name)
	    	if ( file_exists($dir . $file_name) ) 
	    	{
	            $i = 1;
	            while( file_exists($dir . $actual_name . "." . $file_ext) )
	            {           
	                $actual_name = (string)$original_name . "(" . $i . ")";
	                $file_name   = $actual_name . "." . $file_ext;
	                $i++;
	            }
	            $New_file_name = $file_name;
	            move_uploaded_file($imgFile["tmp_name"], $dir . $New_file_name);
	            chmod($dir . $New_file_name, 0644);
	            $makeMessage .= "Filens namn fanns redan, nytt namn: " . $New_file_name . " har laddats upp. ";
	            $makeImage   = $New_file_name ;
		        
	        } // if file do not already exists
	        elseif ( move_uploaded_file($imgFile["tmp_name"], $dir . $file_name) ) 
	        {
	            // Read and write for owner, read for everybody else
	            chmod($dir . $file_name, 0644);
	            $makeMessage .= "Filen ". $file_name . " har laddats upp. ";
	            $makeImage   = $file_name;
	        
	        }
	        else // if something went wrong
	        {
	            $makeMessage .= "Det blev något fel när din bild försökte laddas upp ";
	            $makeImage   = $oldUserImg;
	            $uploadOk 	 = 0;
	        }
    	}
	}
	return $returnArray = ["makeMessage" => $makeMessage, "makeImage" => $makeImage];
}


/* #################################################
   # REPLACE FUNCTIONS
   ################################################# */
function embedYoutube($text)
{
	$embed = "";
	$regex = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
	// try to find a youtube link 
	if( preg_match($regex, $text, $match) ) 
	{
    	$video_id = $match[1];
    	$embed 	  = '<iframe src="//www.youtube.com/embed/'.$video_id.'" frameborder="0" allowfullscreen></iframe>';
	}	
	//remove "all" youtube links				
	$text = preg_replace('%(https?://|www\.)*(youtube|youtu\.be)[a-z\.0-9/?=&]+%i', '', $text);
	//remove all tags
	$text = strip_tags($text);
	return $text . "<br>" . $embed;
}


function tagsToLinks($text) 
{
    $pattern = '/#\w+/u';

    return preg_replace_callback($pattern, 
    	function ($matches) 
	    {
	    	$link[0] = str_replace("#", "%23",  $matches[0]);
	        return "<a href=php/searchAction.php?searchBox={$link[0]}>{$matches[0]}</a>";
	    }, $text);
}


function tagsToDb($id, $text) 
{
	$db 	= connectToDb();
	$text 	= mysqli_real_escape_string($db,$text);
	$id 	= (int)$id;

	// find all #tags
    $pattern = '/#\w+/u';
	preg_match_all($pattern, $text, $matches);

	for ($i=0; $i < count($matches[0]); $i++)
	{
		$tag = $matches[0][$i];

		$result = mysqli_query($db, "SELECT tag_id FROM post_tags WHERE tag = '$tag' ");
		$tagId 	= mysqli_fetch_assoc($result)["tag_id"];

		mysqli_free_result($result);

		//if #tag already existed
		if ( !empty($tagId) ) 
		{
			// insert the tagId and the postId in the DB "tag_connect"
			 mysqli_query($db, "INSERT INTO tag_connect (tag_id, post_id) VALUES ('$tagId', '$id')");
		}
		else
		{
			// Create the new tag in DB 
			mysqli_query($db, "INSERT INTO post_tags (tag) VALUES ('$tag')");

			// get the tag id
			$newTagId = mysqli_insert_id($db);

			// insert the tagId and the postId in the DB "tag_connect"
			 mysqli_query($db, "INSERT INTO tag_connect (tag_id, post_id) VALUES ('$newTagId', '$id')");
		}
	}

	// close db connection
	mysqli_close($db);
}


function namesToLinks($text) 
{
    $pattern = '/@\w+/u';

    return preg_replace_callback($pattern, 
    	function ($matches) 
	    {
	        return "<a href=php/searchAction.php?searchBox={$matches[0]}>{$matches[0]}</a>";
	    }, $text);
}

/* #################################################
   # SEARCH FUNCTIONS
   ################################################# */
/* 
NOT IN USE ANYMORE
function findAllTags($input)
{	
	// find # and any word after it
	$pattern = '/#\w+/u';

	// create array with matching $pattern
	preg_match_all($pattern, $input, $matches);

	//return array with the tags (including #)
	return $matches[0];
}
*/

// function that checks for @"text" 
function isUserName($input)
{	
	// find @ possition
	$index = strpos($input, "@");
	// if no @ return false
	if ( $index === false) {
		return false;
	}
	// if @ is found, return it (including @)
	return substr($input, $index);
}

// function that checks for #"text" 
function isTag($input)
{	
	// find # possition
	$index = strpos($input, "#");
	// if no # return false
	if ( $index === false) {
		return false;
	}
	// if # is found, return it (including #)
	return substr($input, $index);
}


function getSearchResults($search)
{
	$array 	= [];

	// connect to db
	$db 	= connectToDb();
	//$search = mysqli_real_escape_string($db,$search);

	// sql query, match the search words and order by relevance 
	$sql 	=  "SELECT *, MATCH (post_txt) AGAINST ('{$search}' IN BOOLEAN MODE) AS relevance 
				FROM user_posts
				LEFT JOIN user_info
			    ON user_posts.user_id = user_info.user_id
				WHERE MATCH (post_txt) AGAINST ('{$search}' IN BOOLEAN MODE) AND user_posts.post_reply = 0 AND user_posts.post_active = 1 AND user_info.user_active = 1
				ORDER BY relevance DESC LIMIT 100";

	// if the search was on a user, change query (returns only user info, no posts)
	$name = isUserName($search);
	// remove @ att begining or sql will fail
	$name = substr($name, 1);
	if ( $name !== false ) 
	{
		$sql = "SELECT user_id, user_name,  
				MATCH (user_name) AGAINST ('{$name}' IN BOOLEAN MODE) AS relevance 
				FROM user_info
				WHERE MATCH (user_name) AGAINST ('{$name}' IN BOOLEAN MODE)  AND user_info.user_active = 1
				ORDER BY relevance DESC LIMIT 100";
	}

	// if the search was on a tag, change query
	$tag = isTag($search);
	if ( $tag !== false ) 
	{
		$sql = "SELECT * FROM tag_connect
				INNER JOIN post_tags  ON post_tags.tag_id 	 = tag_connect.tag_id
				INNER JOIN user_posts ON tag_connect.post_id = user_posts.post_id
				INNER JOIN user_info  ON user_posts.user_id  = user_info.user_id
				WHERE post_tags.tag = '{$tag}' AND user_posts.post_reply = 0 AND user_posts.post_active = 1 AND user_info.user_active = 1
				ORDER BY user_posts.post_date DESC LIMIT 100";
	}
	
	// get the result
	$result = mysqli_query($db, $sql);

	// create an array with result rows
	while ( $results = mysqli_fetch_assoc($result) ) 
	{
		$array[] = $results;
	}

	//close connection
	mysqli_close($db);

	if ($name !==false) {
		$array["isName"] = true;
	}
	else
	{
		$array["isName"] = false;
	}

	return $array;
}


