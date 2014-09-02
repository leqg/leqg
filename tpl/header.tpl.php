<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>LeQG : gestion de base de données électorale</title>
	
	<!-- lien vers les feuilles de style -->
	<link rel="stylesheet" media="all" href="assets/css/main.css">
	<link rel="stylesheet" media="all" href="assets/css/fonts.css">
	<link href='http://fonts.googleapis.com/css?family=Roboto:100,100italic,300,300italic,400,400italic,500,500italic,700,700italic,900,900italic&subset=latin-ext' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Fira+Sans:300,300italic,400,400italic,500,500italic,700,700italic&subset=latin-ext' rel='stylesheet' type='text/css'>

	<!-- lien vers les ressources Javascript / jQuery -->
	<script src="assets/js/jquery-2.1.1.min.js"></script>
	<script src="assets/js/jquery.inputmask.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSkeqzB0suNWsj8fU3If9tA0spIl_xN2A&sensor=false"></script>
	<script src="assets/js/main.js"></script>
	<?php if (isset($_GET['page'])) { ?><script src="assets/js/<?php echo $_GET['page']; ?>.js"></script><?php } ?>
</head>
<body>	
	<!-- Contenu concret de la page -->
	<header id="top">
		<h1><a class="nostyle" href="http://<?php echo $config['SERVER']['url']; ?>">LeQG</a></h1>
		<a class="nostyle" id="menu" href="#" title="Afficher le menu"></a>
		<a class="nostyle" id="notifications" href="#" title="Afficher les notifications"><span></span></a>
	</header><!--header#top-->
	
	<!-- Navigation principale -->
	<nav id="principale">
		<a href="<?php $core->tpl_go_to('utilisateur'); ?>" id="lien-utilisateur">Mon compte</a>
		<a href="<?php $core->tpl_go_to('contacts'); ?>" id="lien-contacts">Contacts</a>
		<a href="<?php $core->tpl_go_to('dossiers'); ?>" id="lien-dossiers">Dossiers</a>
		<a href="<?php $core->tpl_go_to('carto'); ?>" id="lien-carto">Cartographie</a>
		<a href="<?php $core->tpl_go_to('sms'); ?>" id="lien-sms">SMS groupés</a>
		<a href="<?php $core->tpl_go_to('email'); ?>" id="lien-email">Emails groupés</a>
		<a href="<?php $core->tpl_go_to('poste'); ?>" id="lien-poste">Publipostage</a>
		<a href="<?php $core->tpl_go_to('porte'); ?>" id="lien-porte">Porte-à-porte</a>
		<a href="<?php $core->tpl_go_to('boite'); ?>" id="lien-boite">Boîtage</a>
		<a href="<?php $core->tpl_go_to('rappels'); ?>" id="lien-rappels" class="inactif">Rappels</a>
	</nav><!--nav#principale-->
	
	<main id="central" class="<?php if (isset($_GET['page'])) { echo $_GET['page']; } ?>">