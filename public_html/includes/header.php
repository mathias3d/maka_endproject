<header>
    <nav class="navbar" role="navigation">
      	<div class="container">
	        <div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
	          	<a class="navbar-brand" href="index.php"><img src="img/design/meny-logo.png"></a>
	        </div>
	        <div id="navbar" class="collapse navbar-collapse">
				<ul class="nav">
					<li>
						<a href="index.php">STARTSIDAN</a>
					</li>
		
					<li class="logo">
						<a href="index.php"><img src="img/design/meny-logo.png"></a>
					</li>
					<?php if ( checkSession() === false ): ?>
					<li>
						<a href="index.php?page=createAccountPage">SKAPA KONTO</a>
					</li>
					<?php endif ?>
					<?php if ( checkSession() ): ?>
					<li class="dropdown">
						<a href="profilePage.php"> PROFILSIDA</a>
					</li>
					<?php endif ?>
				</ul>
				<?php if ( checkSession() ): ?>
				<form class="searchForm" action="../php/searchAction.php" method="GET" autocomplete="off">
					<input id="searchBox" type="text" name="searchBox" maxlength="60" placeholder="Sök text, #tag eller @namn" required>
					<button tabindex="-1" type="submit">Sök</button>
					<div id="results"></div>
				</form>
				<?php endif ?>
				

	        </div><!--/.nav-collapse -->
      	</div>
    </nav>
</header>