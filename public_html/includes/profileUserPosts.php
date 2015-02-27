<?php 
if (!function_exists('checkSession')) 
{
	require_once("../php/functions.php"); 
}

if ( checkSession() ) 
{

	if ( isset($_GET["userId"]) && !empty($_GET["userId"]) ) 
	{
		// GET from the link to the profile
		$user  = $_GET["userId"];
	}
	elseif ( isset($_POST["userId"]) && !empty($_POST["userId"]) ) 
	{
		// POST from ajax when scrolling pagination
		$user  = $_POST["userId"];
	}
	else
	{
		// SESSION when it is my profile
		$user  = $_SESSION["user"]["userId"];
	}

	// get the ids of the USERS the ProfileOwner is following (and wrap them with ' ' for the sql)
	$followIdsArr = ( array_map( function($value) { return "'" . $value . "'"; }, getUserFollowIds($user)) );

	// IF user is not following anybody ELSE string for sql IN query
	if ( empty($followIdsArr) ) 
	{
		$IdsStr = "= '" . $user . "'";
	}
	else
	{
		$IdsStr = "IN ('" . $user . "' , " . implode(" , ", $followIdsArr) . ")";
	}


	############ CHANGE SQL IF LINKS ARE CLICKED ############
	if ( isset($_GET["show"]) && !empty($_GET["show"] && $_GET["show"] != "undefined") ) 
	{
		$show 	= $_GET["show"];
	}
	else
	{
		$show 	= "";
	}

	switch ($show) 
	{
		case 'follow':
			// the ones user is following (if any)
			$followIds = implode(",", $followIdsArr);
			if ( !empty($followIds) ) 
			{
				$sql = "SELECT * FROM user_info
					   	WHERE user_id IN ($followIds) AND user_info.user_active = 1
					   	ORDER BY user_name ASC";
			}
			else
			{
				// do this to avoid error in "IN" sql in $followIds is empty
				$sql = "SELECT * FROM user_info	WHERE user_id = 'nothing to show here' ";
			}

			break;

		case 'post':
			// get the profile owners own posts
			$sql = "SELECT * FROM user_info									
				   	INNER JOIN user_posts
				   	ON user_posts.user_id = user_info.user_id
				   	WHERE user_info.user_id = '$user' AND user_posts.post_reply = 0 AND user_posts.post_active = 1
				   	ORDER BY user_posts.post_date DESC";
			break;

		case 'like':
			// get the posts that the profile owner likes
			$sql = "SELECT * FROM user_likes
					INNER JOIN user_posts
					ON user_likes.liked_post_id = user_posts.post_id
					INNER JOIN user_info
					ON user_posts.user_id = user_info.user_id
					WHERE user_likes.user_id = '$user' AND user_posts.post_reply = 0 AND user_posts.post_active = 1
					ORDER BY user_posts.post_date DESC";
			break;
		
		default:
			// get the profile owners posts and the ones user is following (if any)
			$sql = "SELECT * FROM user_posts										
					INNER JOIN user_info
					ON user_posts.user_id = user_info.user_id
					WHERE user_posts.user_id $IdsStr AND user_posts.post_reply = 0 AND user_posts.post_active = 1 
					ORDER BY user_posts.post_date DESC";
			break;
	}


	/* #################################################
	   # PAGINATION
	   ################################################# */

	$view   = 10;	// Antal att visa per sida
	$start  = 1;	// Börja på sidan 1 som standard

	if ( isset($_POST['pageination']) ) 
	{
	    $start = (int)$_POST['pageination'];
	}

	$limit      = " LIMIT ". ($start - 1) * $view .", ". 10;
	$sql        =  $sql.$limit;


	$articles   = getDbContent( $sql );

	//print the $articles
	if( count($articles) > 0 )
	{
	    if ( $show == "follow" ) 
		{
			//print user profiles
			require_once("postsForeachProfiles.php");
		}
		else
		{
			// "print" the $articles
			require_once("postsForeachFrame.php");
		}
	}
	else
	{
		//message to javascript that stops pagination
	    print "&#32;";
	}

}
