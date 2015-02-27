<?php 
require_once("functions.php");

// if user is logged in && the data is posted
if ( checkSession() && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["share"]) && !empty($_POST["share"])) 
{	
	//get user id
	$userId = $_SESSION["user"]["userId"];

	//get the shared post id
	$sharePostId = (int)$_POST["share"];



	########## CREATE A NEW POST ###########	
	$db = connectToDb();

	// sql query
	$sql = "INSERT INTO user_posts (user_id, post_share)
		  	VALUES ('$userId' , '$sharePostId')";


	// run and check sql query
	if ( !mysqli_query($db, $sql) ) 
	{
		logError( "sql query failed, ". mysqli_error($db) . " " . basename(__FILE__), dirname(__FILE__) );
		exit;
	}

	// get the latest id
	$newPostId = mysqli_insert_id($db);

	// close db connection
	mysqli_close($db);

	########## GET THE NEWLY CREATED POST ###########
	// sql query 1
	$sql = "SELECT *
		    FROM user_posts
		    LEFT JOIN user_info
		    ON user_posts.user_id = user_info.user_id
		    WHERE user_posts.post_id = '$newPostId' 
		    ";
	$userInfo = getDbContent($sql)[0];

	$sharedId = $userInfo["post_share"];
	$userName = $userInfo["user_name"];
	$fullName = ucfirst($userInfo["user_full_name"]);
	$userImg  = "img/users/". $userId .  "/" . $userInfo["user_img"];
	$date 	  = viewTime( date("Y-m-d H:i:s") );

	// sql query 2
	$sql = "SELECT *
		    FROM shared_posts
		    LEFT JOIN user_info
		    ON shared_posts.user_id = user_info.user_id
		    WHERE user_info.user_active = 1 AND shared_posts.ps = '$sharedId' 
		    ";		
	$sharedPostArr = getDbContent($sql)[0];

	// go to orginal-post link maybe?
	$s_postId   = $sharedPostArr["post_id"];

	$s_userId 	= $sharedPostArr["user_id"];
	$s_userName = $sharedPostArr["user_name"];
	$s_fullName = ucfirst($sharedPostArr["user_full_name"]);
	$s_userImg 	= "img/users/". $s_userId .  "/" . $sharedPostArr["user_img"];
	$s_text 	= $sharedPostArr["post_txt"];
	$s_img	  	= "img/users/". $s_userId . "/" . $sharedPostArr["post_img"];
	$s_date		= viewTime( $sharedPostArr["post_date"] );

	?>

	<article class="article-box">
		<div class="row article-wrapper">

			<aside class="col-lg-1">
			<?php if ( isset($userImg) && !empty($userImg) ): ?>
				<div class="articleUserImg">
					<a href="profilePage.php?userId=<?= $userId ?>">
		 				<img src="<?= $userImg ?>" alt="">
		 			</a>
		 		</div>
			<?php endif ?>
			</aside>

			<div class="col-lg-11 article-post">
				<div class="article-header">
					<h1><?= $fullName ?><a href="profilePage.php?userId=<?= $userId ?>"><small><?= $userName ?></small></a><small class="pull-right">Postad <?= $date; ?></small></h1>
				</div>

				<div class="article-text">

					<!-- share -->
					<article class="article-box">
						<div class="row article-wrapper">

							<aside class="col-lg-1">
							<?php if ( isset($s_userImg) && !empty($s_userImg) ): ?>
								<div class="articleUserImg">
									<a href="profilePage.php?userId=<?= $s_userId ?>">
						 				<img src="<?= $s_userImg ?>" alt="">
						 			</a>
						 		</div>
							<?php endif ?>
							</aside>

							<div class="col-lg-11 article-post">
								<div class="article-header">
									<h1><?= $s_fullName ?><a href="profilePage.php?userId=<?= $s_userId ?>"><small><?= $s_userName ?></small></a><small class="pull-right">Postad <?= $s_date; ?></small></h1>
								</div>

								<div class="article-text">
									<p><?= $s_text ?></p>	
									
									<?php if (isset($s_img) && !empty($s_img) ) { ?>
									<div class="article-img">
								 		<img class="pop" src="<?= $s_img ?>" alt="">
								 	</div>
									<?php } ?>
								</div>

							</div>
						</div>
					</article>
					<!-- end share -->

				</div>

			</div>
		</div>
	</article>

<?php

}