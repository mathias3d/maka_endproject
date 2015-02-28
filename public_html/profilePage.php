<?php 
require_once("./php/functions.php");

if ( checkSession() == false ) 
{
	header('Location: index.php');
	exit;
}

//check who´s profile to show
if ( isset($_GET["userId"]) && !empty($_GET["userId"]) ) 
{
	$row 		  = getUserInfo( $_GET["userId"] );
	$myProfile    = false;

	if ( $_GET["userId"] == $_SESSION["user"]["userId"] ) 
	{
		$myProfile = true;
	}
}
else
{
	$row 		  = getUserInfo( $_SESSION["user"]["userId"] );
	$myProfile 	  = true;
}

$user	  	  = $row["user_id"];
$userName 	  = $row["user_name"];
$fullName 	  = ucfirst($row["user_full_name"]);
$userImg 	  = "img/users/". $user . "/" . $row["user_img"];
$numFollowers = count(getFollowersIds($user));

$bgImg	  	  = "img/users/". $user . "/" . $row["user_bg_img"];

$usertext	  = $row["user_text"];
$regdate	  = date("d/m - Y", strtotime("$row[user_regdate]"));

?>
<!DOCTYPE html>
<html lang="sv">
<head>
	<meta charset="UTF-8">
	<title>B-community</title>
	<meta name="description" content="">

	<!-- Mobile Specific Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Favicon -->
	<link rel="shortcut icon" href="img/design/favicon.ico">
	<!-- font -->
	<link rel="stylesheet" href="fonts/font-awesome-4.3.0/css/font-awesome.min.css">

	<!-- Stylesheets -->
	<link rel="stylesheet" href="css/base.css">
	<link rel="stylesheet" href="css/main.css">

</head>
<body>
	<div class="wraper">
		
		<!-- Navigation -->
		<?php require_once("includes/header.php"); ?>
		<!-- Navigation END -->


		<div class="topdiv">
			<?php if ( $row["user_bg_img"] == null ): ?>
			<img class="bg" src="img/design/img.png">
			<?php endif ?>

			<?php if ( $row["user_bg_img"] != null ): ?>
			<img class="userbg" src="<?= $bgImg ?>">
			<?php endif ?>
		</div>

		<div class="topbar">
			<div class="col-lg-2">
				
			</div>
			<div class="col-lg-8">
				<div class="pull-left">
					<h1><?= $fullName ."´s Profilsida" ?></h1>
				</div>
				<div class="subnav pull-right">
					<ul>
						<?php if ( $myProfile == true ): ?>
						<li>
							<a href="profilePage.php"><p><i class="fa fa-user fa-lg"></i> Profil hem</p></a>
						</li>
						<li>
							<a href="profilePage.php?page=settingsProfile"><p><i class="fa fa-cog fa-lg"></i> Inställningar</p></a>
						</li>
						<li>
							<a href="profilePage.php?page=editProfile"><p><i class="fa fa-pencil-square-o fa-lg"></i> Redigera Profil</p></a>
						</li>

						<?php else: ?>
						<li>
							<a class="followButton" value="<?= $user ?>"><p><i class="fa fa-user-plus fa-lg"></i>

							<?php if ( in_array( $user, getUserFollowIds($_SESSION["user"]["userId"]) ) ): ?>
								<span>Sluta följa</span>
							<?php else: ?>
								<span> Följ </span>
							<?php endif ?>

							</p></a>
						</li>
						<li>
							<a onclick="history.back(-1)"><p><i class="fa fa-caret-left fa-lg"></i> Tillbaka</p></a>
						</li>
						<?php endif ?>

						<li>
							<a href="../php/logout.php"><p><i class="fa fa-sign-out fa-lg"></i> Logga ut</p></a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-lg-2">
				
			</div>
		</div>

		<div class="container">
			<div class="row">

				<aside class="col-sm-2">

					<div class="userphoto">
						<?php if ( $row["user_img"] == null ): ?>
						<img src="img/design/photo.png">
						<?php endif ?>

						<?php if ( $row["user_img"] != null ): ?>
						<img src="<?= $userImg ?>">
						<?php endif ?>
					</div>

					<div class="userinfo">
						<p>
							<b><?= $fullName ?></b>
							<br>
							<?= $userName ?>
							<br>
						</p>
						
						<p><?= $usertext ?></p>

						<p><i class="fa fa-calendar fa-lg"></i> Gick med <?= $regdate ?></p>
					</div>
					
				</aside>

				<section class="col-sm-8 stuff">
					<?php require("includes/createPostsForm.php"); ?>
					<div id="posts">

						<?php 
						if ( $myProfile == true ) 
						{
							$allowedPages = ["editProfile", "settingsProfile"];
							$standardPage = "includes/profileUserPosts.php";

							require_once( getRequiredPage( $allowedPages, $standardPage) );	

							$user = $_SESSION["user"]["userId"];				
						}
						else
						{
							require_once("includes/profileUserPosts.php");

							// must have here or $user gets fucked up
							$user = $_GET["userId"];
						}
						?>
					</div>

				</section>

				<aside class="col-sm-2">

					<?php if ( !isset($_GET["page"]) ): ?>

						<div class="asideHeader">
							<h1>Info</h1>
						</div>

						<div class="asideBody">
							<?php if ( $myProfile ): ?>

								<a href="profilePage.php?show=post">
									<p id="nrPosts">Skrivit <?= getNrUserPosts($user); ?> st inlägg.</p>
								</a>

								<a href="profilePage.php?show=like">
									<p id="nrLikes">Gillar <?= count( getUserLikes($user) ); ?> st inlägg.</p>
								</a>

								<a href="profilePage.php?show=follow">
									<p>Följer <?= count( $followIdsArr ); ?> st användare.</p>
								</a>
							<?php  else: ?>

								<a href="profilePage.php?userId=<?= $user ?>&show=post">
									<p>Skrivit <?= getNrUserPosts($user); ?> st inlägg.</p>
								</a>

								<a href="profilePage.php?userId=<?= $user ?>&show=like">
									<p>Gillar <?= count( getUserLikes($user) ); ?> st inlägg.</p>
								</a>

								<a href="profilePage.php?userId=<?= $user ?>&show=follow">
									<p>Följer <?= count( $followIdsArr ); ?> st användare.</p>
								</a>

							<?php endif ?>

							<p id="followers">Har <?= $numFollowers; ?> st följare.</p>
						</div>

					<?php endif ?>
					
					<div id="messages">
						<?php checkAndPrintMessages(); ?>
					</div>

				</aside>

			</div><!-- end.row -->
		</div><!-- end.container -->

	</div><!-- wraper end -->

	<div id="pop-backdrop">
		<div id="pop">
	
		</div>
	</div>

	<!-- Footer -->
	<?php require_once("includes/footer.php"); ?>
	<!-- Footer END-->

<!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.min.js"></script>
    <script src="js/custom.js"></script>
        
</body>
</html>