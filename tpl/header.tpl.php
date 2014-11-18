<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>LeQG : gestion de base de données électorale</title>
	
	<!-- lien vers les feuilles de style -->
	<link rel="stylesheet" media="all" href="assets/css/main.css">
	<link rel="stylesheet" media="all" href="assets/css/fonts.css">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:100,100italic,300,300italic,400,400italic,500,500italic,700,700italic,900,900italic&subset=latin-ext">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Fira+Sans:300,300italic,400,400italic,500,500italic,700,700italic&subset=latin-ext">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,200italic,300,300italic,400,400italic,600,600italic,700,700italic,900,900italic&subset=latin-ext">
	<link rel="stylesheet" href="https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.css">

	<!-- lien vers les ressources Javascript / jQuery -->
	<!--[if lt IE 9]><script src="assets/js/html5shiv.min.js"></script><![endif]-->
	<script src="assets/js/jquery-2.1.1.min.js"></script>
	<script src="https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSkeqzB0suNWsj8fU3If9tA0spIl_xN2A&sensor=false"></script>
	<script src="assets/js/main.js"></script>
	<?php if (isset($_GET['page'])) { ?><script src="assets/js/<?php echo $_GET['page']; ?>.js"></script><?php } ?>
</head>
<?php $flat = array('boite', 'porte', 'contacts', 'contact', 'dossier', 'rappels', 'recherche-thematique', 'recherche', 'carto'); ?>
<body<?php if ((isset($_GET['page']) && in_array($_GET['page'], $flat)) || empty($_GET['page'])) { ?> class="flat"<?php } ?>>	
	<!-- Contenu concret de la page -->
	<header id="top">
		<h1><a class="nostyle" href="http://<?php echo Configuration::read('ini')['SERVER']['url']; ?>">LeQG</a></h1>
		<a class="nostyle" id="menu" href="#" title="Afficher le menu"></a>
		<a class="nostyle" id="notifications" href="#" title="Afficher les notifications"><!--<span></span>--></a>
		<a class="nostyle" id="rechercheRapide" href="#" title="Rechercher une fiche"></a>
		<form method="post" action="<?php Core::tpl_go_to('recherche'); ?>"><input type="search" id="searchForm" name="recherche" pattern=".{3,}" placeholder="Michel Dupont" autocomplete="off"><input type="submit" value="&#xe803;" id="searchSubmit"></form>
	</header><!--header#top-->
	
	<!-- Navigation principale -->
	<nav id="principale">
		<?php 
			$menu = array(/*'utilisateur' => 'Mon compte',*/
							'contacts' => 'Contacts',
							'dossier' => 'Dossiers',
							'carto' => 'Cartographie',
							'sms' => 'SMS groupés',
							'email' => 'Emails groupés',
							'poste' => 'Publipostage',
							'porte' => 'Porte-à-porte',
							'boite' => 'Boîtage',
							'rappels' => 'Rappels',
							'administration' => 'Gestion');
							
			if (isset($_GET['page'])) $actuel = ($_GET['page'] == 'contact') ? 'contacts' : $_GET['page'];
							
			$inactif = array('rappels');
		
			foreach ($menu as $key => $element) : ?>
		<a href="<?php Core::tpl_go_to($key); ?>" <?php if (isset($_GET['page']) && $actuel == $key) echo 'class="actif"'; if (in_array($key, $inactif)) echo 'class="inactif"'; ?> id="lien-<?php echo $key; ?>"><?php echo $element; ?></a>
		<?php endforeach; ?>
	</nav><!--nav#principale-->
	
	<main id="central" class="<?php if (isset($_GET['page'])) { echo $_GET['page']; if (in_array($_GET['page'], $flat)) { echo ' flat'; } } else { echo 'flat'; } ?>">
