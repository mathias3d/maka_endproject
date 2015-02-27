<?php  	

$userId = $_SESSION["user"]["userId"];

$sql 	= "SELECT user_email
		   FROM user_accounts
		   WHERE user_id = '$userId'";

$result 	= getDbContent($sql);
$userEmail 	= $result[0]["user_email"];

?>

<article class="article-box cursor">
	<div class="row article-wrapper">
		<div class="col-sm-12 article-post">

			<div class="article-header">
				<h1>Inställningar</h1>
			</div>

			<div class="article-text">
				<form class="clearfix" action="php/settingsProfileAction.php" name="settingsProfileForm" id="settingsProfileForm" method="POST" accept-charset="utf-8">
					<hr>
					<section class="row">
						<label class="col-md-5">Ändra användarnamn: (max 40 tecken)</label>
						<div class="col-md-7">
							<input type="text" name="userName" maxlength="40" value="<?= $userName ?>">
						</div>
					</section>
					<hr>
					<section class="row">
						<label class="col-md-5">Ändra Email: (max 140 tecken)</label>
						<div class="col-md-7">
							<input type="email" name="userEmail" maxlength="140" value="<?= $userEmail ?>">
						</div>
					</section>		
					<hr>
					<div class="col-sm-12 clearfix">		                
	                	<button class="pull-right" type="submit">Spara</button>
					</div>
				</form>
			</div>

		</div><!-- end.article-post -->
	</div><!-- end.article-wrapper -->
</article><!-- end.article-box -->



<article class="article-box">
	<div class="row article-wrapper">
		<div class="col-sm-12 article-post">

			<div class="article-header">
				<h1>Avsuta Medlemskapet</h1>
			</div>

			<div class="article-text">
				<form class="clearfix" action="php/settingsProfileAction.php" name="settingsProfileForm" id="settingsProfileForm" method="POST" accept-charset="utf-8">
					<hr>
					<section class="row">
						<p>Genom att klicka på knappen nedan så avslutar du medlemskapet på sidan.</p>
					</section>						
					<hr>
					<div class="col-sm-12 clearfix">	                
	                	<a href="php/endAccountAction.php" class="button pull-right">AVSLUTA MEDLEMSKAPET</a>	            
					</div>
				</form>
			</div>

		</div><!-- end.article-post -->
	</div><!-- end.article-wrapper -->
</article><!-- end.article-box -->


<div id="posts">
</div>