<div class="kvitter">
	<div class="kvitterHeader">
		<h1>Skapa inlägg</h1>
	</div>
	<form id="makePostForm" class="clearfix" action="php/postAction.php" method="POST" accept-charset="utf-8" enctype="multipart/form-data">

		<textarea id="post_txt" name="post_txt" required maxlength="140"></textarea>
		
		<input id="fileInput" class="hidden" type="file" name="image">	

		<input type="hidden" name="makePost">
			
		<div class="article-meny row">
			<ul>
				<li>
					<a id="fileButton" title="Ladda upp bild">
						<p><i class="fa fa-camera fa-lg"></i></p>
					</a>
				</li>
				<li>
					<p id="fileName"></p>
				</li>
				<li class="pull-right">
		        	<button id="makePost" type="submit" name="makePost">Posta</button>
				</li>					
			</ul>

		</div>

	</form>
</div>