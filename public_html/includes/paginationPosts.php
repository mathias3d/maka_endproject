<?php 
if (!function_exists('countNews')) 
{
	require_once("../php/functions.php"); 
}

// get different db content based on GET-links
if (isset($_GET["post"]) && !empty($_GET["post"]) ) 
{
	$post      = $_GET["post"];
    $minusH  = date("Y-m-d H:i:s", time()-(60*60*24) );
}
else
{
    $post      = "";
}

switch($post)
{	
	// latest posts
    case "newPosts":
        $sql = "SELECT *
                FROM user_posts
                LEFT JOIN user_info
                ON user_posts.user_id = user_info.user_id
                WHERE user_info.user_active = 1 AND user_posts.post_reply = 0 AND user_posts.post_active = 1 AND user_posts.post_date > '$minusH'
                ORDER BY user_posts.post_date DESC";       
    break;
    
	// posts whit most likes
    case "topPosts":
        $sql = "SELECT * FROM post_likes 
                INNER JOIN user_posts
                ON post_likes.liked_post_id = user_posts.post_id
                INNER JOIN user_info
                ON user_posts.user_id = user_info.user_id
                WHERE user_info.user_active = 1 AND user_posts.post_reply = 0 AND user_posts.post_active = 1 AND user_posts.post_date > '$minusH'
                ORDER BY count_likes DESC";
    break;

    default:
		$sql = "SELECT *
                FROM user_posts
                LEFT JOIN user_info
                ON user_posts.user_id = user_info.user_id
                WHERE user_info.user_active = 1 AND user_posts.post_reply = 0 AND user_posts.post_active = 1
                ORDER BY user_posts.post_date DESC";
	break;
}


/* #################################################
   # PAGINATION
   ################################################# */

$rows   = countNews();          // Antal POSTS i databasen 
$view   = 10;                   // Antal att visa per sida
$pages  = ceil($rows / $view);  // Antal sidor vi kan visa totalt
$start  = 1;                    // Börja på sidan 1 som standard

if ( isset($_POST['pageination']) ) 
{
    $start = (int)$_POST['pageination'];
}

$limit      = " LIMIT ". ($start - 1) * $view .", ". 10;
$sql        = $sql.$limit;


$articles   = getDbContent( $sql );

//print the $articles
if( count($articles) > 0 )
{
    require_once("postsForeachFrame.php");
}
else
{
    //message to javascript that stops pagination
    print "&#32;";
}
