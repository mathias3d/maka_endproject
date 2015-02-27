<?php 
require_once("functions.php");
if ( checkSession() && isset($_POST["searchBox"]) && !empty($_POST["searchBox"])) 
{
	// connect to db
	$db 	= connectToDb();
	$search = mysqli_real_escape_string($db, $_POST["searchBox"]);

	############ SEARCH FOR NAME ############
	$name = isUserName($search);

	if ( $name !== false ) 
	{
		// remove @ att begining or sql will fail
		$name = substr($name, 1);

		$sql = "SELECT *
				FROM user_info
				WHERE user_name LIKE '%{$name}%' AND user_active = 1
				LIMIT 6";

		// get the result
		$result = mysqli_query($db, $sql);
	
		print "<ul>";
		while ( $results = mysqli_fetch_assoc($result) ) 
		{
			$userId	  = $results["user_id"];
			$userName = $results["user_name"];
			$fullName = ucfirst($results["user_full_name"]);
			$userImg  = $results["user_img"];
			$img  	  = "img/users/" . $userId ."/" . $userImg;

		?>

			<li>
				<a class="pull-left" href="profilePage.php?userId=<?= $userId ?>"> 
					<div class="userphoto-small pull-left">
						<?php if ($userImg): ?>
							<img src="<?= $img ?>">
						<?php else: ?>
							<img src="img/design/at.png">
						<?php endif ?>
					</div>
					<p class="pull-left"><?= $userName ?></p>
				</a>
			</li>

		<?php	
		}
		print "</ul>";
	}


	############ SEARCH FOR TAG ############
	$tag = isTag($search);

	if ( $tag !== false ) 
	{
		$sql = "SELECT tag
				FROM post_tags
				INNER JOIN tag_connect
				ON post_tags.tag_id = tag_connect.tag_id
				INNER JOIN user_posts
				ON tag_connect.post_id = user_posts.post_id
				WHERE tag LIKE '%{$tag}%' AND user_posts.post_active = 1
				LIMIT 6";

		// get the result
		$result = mysqli_query($db, $sql);	

		print "<ul>";
		while ( $results = mysqli_fetch_assoc($result) ) 
		{
			$dbtag  = $results["tag"];

			// remove # 
			$linkTag = substr($dbtag, 1);

			//add %23
			$linkTag = "%23" . $linkTag;
		?>
			<li>
				<a class="pull-left" href="php/searchAction.php?searchBox=<?= $linkTag ?>"> 
					<div class="userphoto-small pull-left">
						<img src="img/design/hashtag.png">
					</div>
					<p class="pull-left"><?= $dbtag ?></p>
				</a>
			</li>

		<?php	
		}
		print "</ul>";
	}

	//close connection
	mysqli_close($db);

}