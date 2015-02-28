<?php 
	require_once("./php/functions.php");
	checkSession();
?>
<!DOCTYPE html>
<html lang="sv">
<head>
	<meta charset="UTF-8">
	<title>B-community</title>
	<meta name="description" content="">

	<!-- Mobile Specific Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Favicon -->
	<link rel="shortcut icon" href="img/design/favicon.ico">
	<!-- font -->
	<link rel="stylesheet" href="fonts/font-awesome-4.3.0/css/font-awesome.min.css">

	<!-- Stylesheets -->
	<link rel="stylesheet" href="./css/base.css">
	<link rel="stylesheet" href="./css/main.css">

</head>
<body>

	<?php if ( checkSession() ): ?>
	<div class="wraper">
		
		<!-- Navigation -->
		<?php require_once("includes/header.php"); ?>
		<!-- Navigation END -->

		<div class="topdiv">
			<img class="startbg" src="img/design/Bheader.jpg">
		</div>
		<div class="topbar">
			<div class="col-lg-2"></div>
			<div class="col-lg-8">
				
				<div class="subnav pull-right">
					<ul>
						<?php if ( isset($_GET["page"]) && $_GET["page"] == "searchResults" ): ?> 
						<li>
							<a onclick="history.back(-1)"><p><i class="fa fa-caret-left fa-lg"></i> Tillbaka</p></a>
						</li>
						<?php endif ?>						
						<li>
							<a href="php/logout.php"><p><i class="fa fa-sign-out fa-lg"></i> Logga ut</p></a>
						</li>
					</ul>
				</div>

			</div>
			<div class="col-lg-2"></div>
		</div>

		<div class="container">
			<div class="row">

				<aside class="col-sm-2">
					<div class="asideHeader">
						<h1>Välkommen</h1>
					</div>
					<div class="asideBody">
						<ul>
							<a href="index.php?post=newPosts"><li><i class="fa fa-star-o"></i> Senaste inläggen idag</li></a>
							<a href="index.php?post=topPosts"><li><i class="fa fa-star-o"></i> Populäraste inläggen idag</li></a>
						</ul>
					</div>
				</aside>

				<section class="col-sm-8">

					<?php require("includes/createPostsForm.php"); ?>

					<div id="posts">
						<?php 
						// require diffrent pages based on GET link?page=somepage
						$allowedPages = ["paginationPosts", "searchResults"];
						$standardPage = "includes/paginationPosts.php";

						require_once( getRequiredPage( $allowedPages, $standardPage) );
						 ?>
					</div>

				</section>

				<aside class="col-sm-2">
					<div id="messages">
						<?php checkAndPrintMessages(); ?>
					</div>
				</aside>

			</div><!-- end.row -->
		</div><!-- end.container -->

	</div><!-- wraper end -->


	<div id="pop-backdrop">
		<div id="pop">
	
		</div>
	</div>

	<!-- Footer -->
	<?php require_once("includes/footer.php"); ?>
	<!-- Footer END-->

<?php else: ?>

		<div class="form">
			<?php require_once("includes/login_form.php"); ?>
		</div>

<?php endif ?>

<!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.min.js"></script>
    <script src="js/custom.js"></script>
        
</body>
</html>