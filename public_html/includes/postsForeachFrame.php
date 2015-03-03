	<?php
		checkSession();
		$sesUser  	= $_SESSION["user"]["userId"];
		$userlikes 	= getUserLikes( $sesUser );

		foreach ($articles as $article):
			$user	  	 = $article["user_id"];
			$userName 	 = $article["user_name"];
			$fullName 	 = ucfirst($article["user_full_name"]);
			$fileUserImg = $article["user_img"];
			$userImg  	 = "img/users/". $user . "/" . $fileUserImg;

			$text 	  	 = $article["post_txt"];
			$filePostImg = $article["post_img"];
			$img	  	 = "img/users/". $user . "/" . $filePostImg;
			$date	  	 = viewTime($article["post_date"]);
			$postId   	 = $article["post_id"];
			$postShared  = $article["post_share"];

			$likes 	  = getThePostLikes($postId)["count(*)"]; 
			
			//remove number of likes if less then 1
			if ($likes == null || $likes < 1) 
			{
				$likes = "";
			}
	?>

	<article class="article-box">
		<div class="row article-wrapper">

			<aside class="col-lg-1">
			<?php if ( isset($fileUserImg) && !empty($fileUserImg) ): ?>
				<div class="articleUserImg">
					<a href="profilePage.php?userId=<?= $user ?>">
		 				<img src="<?= $userImg ?>" alt="">
		 			</a>
		 		</div>
			<?php endif ?>
			</aside>

			<div class="col-lg-11 article-post">
				<div class="article-header">
					
					<h1><?= $fullName ?><a href="profilePage.php?userId=<?= $user ?>"><small><?= $userName ?></small></a><small class="pull-right">Postad <?= $date; ?></small></h1>
					
				</div>

				<div class="article-text">
					<p><?= $text ?></p>	

					<?php 
					########## VIEW SHARED POST (if any) ########### 
					//does this contain a shared message?
					if ( $postShared > 0):

						$sql = "SELECT *
							    FROM shared_posts
							    LEFT JOIN user_info
							    ON shared_posts.user_id = user_info.user_id
							    WHERE shared_posts.ps = {$postShared}
							    ";
						
						$sharedPostArr = getDbContent($sql);

						// did we find the shared message?
						if (!empty( $sharedPostArr) ):

							// is the shared message active and is the owner active?
							if ($sharedPostArr[0]["post_active"] == 1 && $sharedPostArr[0]["user_active"] == 1):
							
								$sharedPostArr = $sharedPostArr[0];
								
								$s_userId 	= $sharedPostArr["user_id"];
								$s_userName = $sharedPostArr["user_name"];
								$s_fullName = ucfirst($sharedPostArr["user_full_name"]);

								$s_fileUserImg = $sharedPostArr["user_img"];
								$s_userImg 	   = "img/users/". $s_userId .  "/" . $s_fileUserImg;
								$s_text  	   = $sharedPostArr["post_txt"];
								$s_filePostImg = $sharedPostArr["post_img"];
								$s_img	  	   = "img/users/". $s_userId . "/" . $s_filePostImg;
								$s_date		   = viewTime( $sharedPostArr["post_date"] );

								// go to orginal-post link / funktion maybe?
								$s_postId   = $sharedPostArr["post_id"];
							?>
								<article class="article-box">
									<div class="row article-wrapper">

										<aside class="col-lg-1">
										<?php if ( isset($s_fileUserImg) && !empty($s_fileUserImg) ): ?>
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
												
												<?php if (isset($s_filePostImg) && !empty($s_filePostImg) ) { ?>
												<div class="article-img">
											 		<img class="pop" src="<?= $s_img ?>" alt="">
											 	</div>
												<?php } ?>
											</div>

										</div>
									</div>
								</article>

							<?php 
								// if the user is not active 
								elseif ($sharedPostArr[0]["user_active"] == 0): 
							?>
									<article class="article-box">
										<div><i class="fa fa-exclamation-triangle"></i> Det delade inläggets ägare har sagt upp sitt konto</div>
									</article>

							<?php 
								// if the post is not active
								else: 
							?>

								<article class="article-box">
									<div><i class="fa fa-exclamation-triangle"></i> Det delade inlägget har raderats av användaren</div>
								</article>

							<?php endif ?>

						<?php endif ?>

					<?php endif ?>
					
					<?php if (isset($filePostImg) && !empty($filePostImg) ) { ?>
					<div class="article-img">
				 		<img class="pop" src="<?= $img ?>" alt="">
				 	</div>
					<?php } ?>
					
				</div>

				<div class="article-footer">
					<div class="article-meny row">
						<ul>
							<li>
								<a class="likeButton" value="<?= $postId ?>" title="Gilla">
									<p>
										<?php if ( in_array($postId, $userlikes) ): ?>
											<i class="fa fa-star liked"></i>
										<?php else: ?>
											<i class="fa fa-star"></i>
										<?php endif ?>
													
										<span id="like<?= $postId ?>" class="numLikes"><?= $likes ?></span>
									</p>
								</a>
							</li>

							<li>
								<a title="Antal Kommentarer">
									<p>
										<?php if ( isMyComment($postId) > 0 ): ?>
											<i class="fa fa-comments-o liked"></i>
										<?php else: ?>
											<i class="fa fa-comments-o"></i>
										<?php endif ?>

										<span id="noCom" class="numLikes"><?= countPostComments($postId); ?></span>
									</p>
								</a>
							</li>
							
							<li class="dropdown pull-right">
								<div class="a"><p><i class="fa fa-ellipsis-v"></i></p></div>
								<ul class="dropdown-menu" role="menu">
									
									<?php if ( $user == $sesUser ): ?>

										<li><a class="delete" value="<?= $postId ?>"><i class="fa fa-times"></i> Radera Inlägg</a></li>

									<?php else: ?>

										<?php if( $postShared == 0 ): ?>
											<li><a class="share" value="<?= $postId ?>"><i class="fa fa-retweet"></i> Dela med dig av inlägget</a></li>
										<?php endif ?>
									
										<li><a>Denhär knappen gör ingenting</a></li>

									<?php endif ?>
								</ul>
							</li>
							
						</ul>

					</div>
				</div><!-- end.article-footer -->

			</div>


			<div class="comments-wrapper">

				
				<!-- ########## POST COMMENTS ########### -->
				<div class="article-comment row">

					<form class="makeCommentForm" action="php/postAction.php" method="POST" accept-charset="utf-8">
						<input type="text" name="comment" maxlength="140" placeholder="Kommentera" required>
						<input type="hidden" name="post_id" value="<?= $postId ?>">
						<input type="hidden" name="makeComment">
			            <button class="makeComment" type="submit" name="makeComment">Posta</button>																			
					</form>

				</div>
				
				<div class="comAppendWrap<?= $postId ?>">
					<?php 
						########## VIEW COMMENTS ###########

						$sql = "SELECT *
							    FROM user_posts
							    LEFT JOIN user_info
							    ON user_posts.user_id = user_info.user_id
							    WHERE user_info.user_active = 1 AND user_posts.post_reply = {$postId}
							    ORDER BY user_posts.post_date DESC";

						$commentsArr = getDbContent($sql);
					
						foreach ($commentsArr as $comments)
						{

							$c_text 	= $comments["post_txt"];
							$c_date		= viewTime($comments["post_date"]);
							$c_postId   = $comments["post_id"];

							$c_user 	= $comments["user_id"];
							$c_userName = $comments["user_name"];
							$c_fullName = ucfirst($comments["user_full_name"]);
							
							$c_fileUserImg = $comments["user_img"];
							$c_userImg 	   = "img/users/". $user . $c_fileUserImg;

							//for future use.. or just as a reminder that they exist
							//$c_fileImg  = $comments["post_img"];
							//$c_img	  = "img/users/". $user . "/" . $c_fileImg;
						
							include("postsForeachComments.php");

						} 
						########## END COMMENTS ###########
					?>	
				</div>

			</div>

		</div>
	</article>

	<?php 
		endforeach;
	?>