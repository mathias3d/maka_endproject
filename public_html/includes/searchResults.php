<?php 
//only logged in can search
if ( checkSession() === false ) 
{
	header("Location: index.php");
	exit;
}

// if search is not for a username
if ($_SESSION['search']["isName"] === false) 
{
	// get the returning search result array
	$articles = $_SESSION['search'];

	//remove ["isName"]
	array_pop($articles);

	//print the $articles
	require_once("postsForeachFrame.php");
}
else
{
	//send to the profile page if its a username
	 $id = $_SESSION['search'][0]["user_id"];

	header("Location: profilePage.php?userId=$id");
}