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
		
		<ul id="parametres">
			<a href="<?php $core->tpl_go_to('options'); ?>"><li>Options générales</li></a>
			<a href="<?php $core->tpl_go_to('confidentialite'); ?>"><li>Confidentialité</li></a>
			<a href="<?php $core->tpl_go_to('mentions-legales'); ?>"><li>Mentions légales</li></a>
		</ul><!--#parametres-->
		
		<ul id="gestion-compte">
			<a class="nostyle" href="<?php $core->tpl_go_to('profile', array('utilisateur' => $user->get_the_ID())); ?>"><li><?php $user->the_nickname(); ?></li></a>
			<li><?php $user->the_email(); ?></li>
			<li><?php $user->the_phone(true); ?></li>
			<a class="nostyle" href="<?php $core->tpl_go_to('logout'); ?>"><li>Déconnexion</li></a>
		</ul><!--#gestion-compte-->
	
		<nav id="applications">
			<ul><!--
			 --><a class="nostyle" href="<?php $core->tpl_go_to('contacts'); ?>"><li><span>&#xe840;</span>Contacts</li></a><!--
			 --><a class="nostyle" href="<?php $core->tpl_go_to('dossiers'); ?>"><li><span>&#xe851;</span>Dossiers</li></a><!--
			 --><a class="nostyle" href="<?php $core->tpl_go_to('carto'); ?>"><li><span>&#xe845;</span>Cartographie</li></a><!--
			 --><?php /*	<a class="nostyle" href="<?php $core->tpl_go_to('porte'); ?>"><li><span>&#xe841;</span>Porte à porte</li></a><!--
			 --><a class="nostyle" href="<?php $core->tpl_go_to('boite'); ?>"><li><span>&#xe84d;</span>Boîtage</li></a><!--
			 --><a class="nostyle" href="<?php $core->tpl_go_to('rappels'); ?>"><li><span>&#xe854;</span>Rappels</li></a><!--
			 -->*/ ?><a class="nostyle" href="<?php $core->tpl_go_to('poste'); ?>"><li><span>&#xe8ef;</span>Publipostage</li></a><!--
			 --><a class="nostyle" href="<?php $core->tpl_go_to('email'); ?>"><li><span>&#xe805;</span>Emailing</li></a><!--
			 --><a class="nostyle" href="<?php $core->tpl_go_to('sms'); ?>"><li><span>&#xe8e4;</span>SMS</li></a><!--
		 --></ul>
		</nav>
	</header><!--#top-->
	
	<main id="central" class="<?php if (isset($_GET['page'])) { echo $_GET['page']; } ?>">