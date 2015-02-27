<article class="article-comment row">
	<aside class="col-lg-1">
		<?php if ( isset($c_fileUserImg) && !empty($c_fileUserImg) ): ?>
			<div class="articleUserImg">
				<a href="profilePage.php?userId=<?= $c_user ?>">
	 				<img src="<?= $c_userImg ?>" alt="">
	 			</a>
	 		</div>
		<?php endif ?>
	</aside>
	<section class="col-lg-11">
		<h4>
			<a href="profilePage.php?userId=<?= $c_user ?>">
				<?= $c_fullName ?>
				<small>
				<?= $c_userName ?>
				</small>
			</a>
			<small class="pull-right">Postat <?= $c_date ?></small>
		</h4>
		<p>
			<?= $c_text ?> 
		</p>
	</section>	
</article>