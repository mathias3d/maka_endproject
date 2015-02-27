<?php
require_once("functions.php");

//only logged in can search
if ( checkSession() === false )
{
	$_SESSION['messages'] = array("text" => '<a href="index.php?page=createAccountPage"><b>Skapa ett konto</b></a> och få tillgång till massa kul.' );
	header("Location: ../index.php");
	exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["searchBox"]) && !empty($_GET["searchBox"]) ) 
{	

	$searchResultsArray = getSearchResults($_GET["searchBox"]);

	//user_name should allways be returned if something i found
	if ( !isset($searchResultsArray[0]["user_name"]) ) 
	{
		// set message and return user
		$_SESSION['messages'] = array("text" => "Jag hittade tyvärr inget åt dig." );
		header('Location: ../index.php');
		exit;
	}

	$_SESSION['search'] = $searchResultsArray;
	header('Location: ../index.php?page=searchResults');
	exit;

}
// set message and return user
$_SESSION['messages'] = array("text" => "Du glömde ju att skriva in något i sökrutan." );
header('Location: ../index.php');