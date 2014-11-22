<!doctype html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>LeQG : système de gestion de communauté électorale</title>
		
		<!-- Feuilles de style CSS -->
		<link href="assets/css/main.css" rel="stylesheet">
		<link href="http://fonts.googleapis.com/css?family=Roboto:100,100italic,300,300italic,400,400italic,500,500italic,700,700italic,900,900italic&subset=latin-ext" rel="stylesheet" type="text/css">
		<link href="http://fonts.googleapis.com/css?family=Fira+Sans:300,300italic,400,400italic,500,500italic,700,700italic&subset=latin-ext" rel="stylesheet" type="text/css">
		
		<!-- Scripts relatifs au service -->
		<script src="assets/js/jquery-2.1.1.min.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSkeqzB0suNWsj8fU3If9tA0spIl_xN2A&sensor=false"></script>
		<script src="assets/js/main.js"></script>
		<?php if (isset($_GET['page'])) : ?><script src="assets/js/<?php echo $_GET['page']; ?>.js"></script><?php endif; ?>
	</head>
	
	<body>
	
		<header id="barreHaute">
			<h1><a href="<?php Core::tpl_go_to(); ?>" class="nostyle">LeQG</a></h1>
			<a id="goToMenu" href="#menu">&#xe811;</a>
		</header>
		
		<nav id="menu">
			<a href="<?php Core::tpl_go_to('recherche'); ?>"><span>&#xe803;</span>Recherche</a>
			<a href="<?php Core::tpl_go_to('contacts'); ?>"><span>&#xe840;</span>Contacts</a>
			<a href="<?php Core::tpl_go_to('porte'); ?>"><span>&#xe841;</span>Porte à porte</a>
			<a href="<?php Core::tpl_go_to('boitage'); ?>"><span>&#xe84d;</span>Boîtage</a>
			<a href="<?php Core::tpl_go_to('deconnexion'); ?>" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?');"><span>&#xe85d;</span>Déconnexion</a>
		</nav>
		
		<main>