<?php
foreach ($articles as $article):
	$date	  = date( "d/m - Y", strtotime("$article[user_regdate]") );
	$text 	  = $article["user_text"];
	$user	  = $article["user_id"];
	$userName = $article["user_name"];
	$userImg  = "img/users/". $user . "/" . $article["user_img"];

?>

<article class="col-sm-4 userBox ">

	<div class="row userWrapper">

			<aside class="userImg">
			<?php if ( $article["user_img"] != null && !empty($article["user_img"]) ): ?>
		 		<img src="<?= $userImg ?>" alt="">
			<?php else: ?>
				<i class="fa fa-camera fa-size"></i>
			<?php endif; ?>
			</aside>

			<div class="userPost">
				<div class="userHeader">
					<h1><a href="profilePage.php?userId=<?= $user ?>"><?= $userName ?> </a></h1>
					<small>Medlem sedan <?= $date ?></small>
				</div>

				<div class="userText">
					<p><?= $text ?></p>	
				</div>

				<div class="userFooter">
				</div>
			</div>
	</div>
</article>

<?php 
endforeach;