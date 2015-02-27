<article class="article-box cursor">
	<div class="row article-wrapper">
		<div class="col-sm-12 article-post">

			<div class="article-header">
				<h1>Redigera Profil</h1>
			</div>

			<div class="article-text">
				<form class="clearfix" action="php/editProfileAction.php" name="editProfileForm" id="editProfileForm" method="POST" enctype="multipart/form-data" accept-charset="utf-8">
					<hr>
					<section class="row">
						<label class="col-md-5">Profil Text: (max 140 tecken)</label>
						<div class="col-md-7">
							<textarea name="profiltxt" form="editProfileForm" maxlength="140"><?= $usertext ?></textarea>
						</div>
					</section>
					<hr>					
					<section class="row">
						<label class="col-md-5">Profil foto: (max 1 mb. Optimal storlek 200 x 200 px)</label>
						<div class="col-md-7"><input type="file" name="profilePhoto[]"></div>
					</section>
					<hr>
					<section class="row">
						<label class="col-md-5">Profil Bakgrund: (max 1mb. Optimal storlek 200 x 2800 px)</label>
						<div class="col-md-7"><input type="file" name="profilePhoto[]"></div>
					</section>
					<hr>
					<div class="col-sm-12 clearfix">
		                <div class="pull-right">
		                	<button type="submit">Spara</button>
						</div>
					</div>
				</form>
			</div>

			<div class="article-footer">

			</div>

		</div><!-- end.article-post -->
	</div><!-- end.article-wrapper -->
</article><!-- end.article-box -->


<div id="posts">
</div>