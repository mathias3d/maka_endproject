<?php 
if (!function_exists('checkAndPrintMessages')) 
	require_once("../php/functions.php"); 
?>

<form class="loginForm clearfix" role="form" action="php/loginAction.php" accept-charset="utf-8" method="POST">
	<div class="asideHeader">
		<h2>Logga in</h2>
	</div>

	<div class="asideBody">
		<section>
			<label for="user">Användarnamn:</label>
			<input type="text" id="user" name="user" placeholder="Användarnamn eller email" maxlength="20" required>
		</section>

		<section>
			<label for="pwd">Lösenord:</label>
			<input type="password" name="pwd" placeholder="Lösenord" maxlength="20" required>
		</section>
		
		<div class="row">
            <div class="pull-right">
            	<button id="loginSubmitBtn" type="submit">Logga in</button>
			</div>

			<div>
				<a id="createAccountBtn">Registrera nytt konto</a>
			</div>
		</div>

		<?php checkAndPrintMessages(); ?>

	</div>
</form>