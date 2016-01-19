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
		
		<!-- Scripts relatifs au serivce -->
		<script src="assets/js/jquery-2.1.1.min.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSkeqzB0suNWsj8fU3If9tA0spIl_xN2A&sensor=false"></script>
		<script src="assets/js/main.js"></script>
		<?php if (isset($_GET['page'])) : ?><script src="assets/js/<?php echo $_GET['page']; ?>.js"></script><?php 
  endif; ?>
	</head>
	
	<body id="login">
	
		<header id="loginLogo">
			<h1>LeQG</h1>
		</header>

		<main>