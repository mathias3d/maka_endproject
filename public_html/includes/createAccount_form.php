<?php 
if (!function_exists('checkAndPrintMessages')) 
	require_once("../php/functions.php"); 
?>

<form class="loginForm clearfix" role="form" action="php/createAccountAction.php" accept-charset="utf-8" method="POST">
	<div class="asideHeader">
		<h2>Skapa konto</h2>
	</div>	

	<div class="asideBody">
		<section>
			<label for="user">För-, Efternamn:</label>
			<input type="text" name="user" placeholder="Fullständigt namn" maxlength="40" required> 
		</section>

		<section>
			<label for="email">E-post:</label>
			<input type="email" name="email" placeholder="E-post" maxlength="40" required> 
		</section>

		<section>
			<label for="pwd">Lösenord:</label>
			<input type="password" name="pwd" placeholder="Lösenord" maxlength="20" required> 
		</section>
		
		<div class="row">
			<div class="pull-right">
            	<button id="createSubmitBtn" type="submit">Skapa Konto</button>
			</div>

			<div>
				<a id="loginAccountBtn">Logga in</a>
			</div>
		</div>

		<?php checkAndPrintMessages(); ?>

	</div>
</form>