<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if IE 9]>    <html class="no-js ie9" lang="en"> <![endif]-->
<head>
	<meta charset="utf-8">
	<title>ClearCode Library</title>
	<meta name="description" content="The ClearCode Database and Authentication Libraries" />
	<meta name="keywords" content="ClearCode" />
	<meta name="author" content="The ClearCode Team">
	
	<link rel="shortcut icon" href="img/favicon.png?v=3" type="image/x-icon" />
	<link rel="apple-touch-icon" href="img/apple-touch-icon.png" type="image/png" />
	<link rel="apple-touch-icon-precomposed" href="img/apple-touch-icon-precomposed.png" type="image/png" />
	
	<!-- Facebook Metadata /-->
	<meta property="fb:page_id" content="" />
	<meta property="og:image" content="img/facebook.jpg" />
	<meta property="og:description" content="The ClearCode Database and Authentication Libraries"/>
	<meta property="og:title" content="ClearCode Library"/>
	<!-- Google+ Metadata /-->
	<meta itemprop="name" content="ClearCode Library">
	<meta itemprop="description" content="The ClearCode Database and Authentication Libraries">
	<meta itemprop="image" content="img/facebook.jpg">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	<link rel="stylesheet" href="css/gumby.css">
	<script src="js/libs/modernizr-2.6.2.min.js"></script>
</head>
<body>
	<div class="navcontain">
		<div class="navbar" gumby-fixed="top" id="nav1">
			<div class="row" style='position: relative'>
				<h1 class="four columns logo"><a href="#"><img class='mainlogo' src="img/gumby_mainlogo.png" gumby-retina /></a></h1>
				<div class="eight columns right">
                    <?php if($auth->logged_in()) { ?>
                        <p class='large primary btn' style='position: absolute; top: 0px; right: 20px; bottom: -13px; height: auto;'><a href="?logout=y"><i class='icon-logout'></i></a></p>
                    <?php } ?>
                </div>
			</div>
		</div>
	</div>
	
	