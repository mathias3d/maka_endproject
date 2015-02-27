<?php 
if (!function_exists('checkAndPrintMessages')) 
	require_once("php/functions.php"); 
?>

<form class="loginForm clearfix" role="form" action="php/loginAction.php" accept-charset="utf-8" method="POST">
	<div class="asideHeader">
		<h2>Logga in</h2>
	</div>

	<div class="asideBody">
		<section>
			<div>Användarnamn:</div>
			<div><input type="text" name="user" placeholder="Användarnamn eller email" maxlength="140" required value=""></div>
		</section>

		<section>
			<div>Lösenord:</div>
			<div><input type="password" name="pwd" placeholder="Lösenord" maxlength="140" required></div>
		</section>
		
		<div class="row">
            <div class="pull-right">
            	<button type="submit">Logga in</button>
			</div>

			<div>
				<a id="createAccountBtn">Registrera nytt konto</a>
			</div>
		</div>

		<?php checkAndPrintMessages(); ?>

	</div>
</form>