<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>LeQG : gestion de base de données électorale</title>
	
	<!-- lien vers les feuilles de style -->
	<link rel="stylesheet" media="all" href="assets/css/main.css">
	<link rel="stylesheet" media="all" href="assets/css/fonts.css">
	<link rel="stylesheet" media="all" href="assets/css/sweet-alert.css">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:100,100italic,300,300italic,400,400italic,500,500italic,700,700italic,900,900italic&subset=latin-ext">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Fira+Sans:300,300italic,400,400italic,500,500italic,700,700italic&subset=latin-ext">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,200italic,300,300italic,400,400italic,600,600italic,700,700italic,900,900italic&subset=latin-ext">
	<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />

	<!-- lien vers les ressources Javascript / jQuery -->
	<!--[if lt IE 9]><script src="assets/js/html5shiv.min.js"></script><![endif]-->
	<script src="assets/js/jquery-2.1.1.min.js"></script>
	<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
	<script src="assets/js/sweet-alert.min.js"></script>
	<script src="assets/js/main.js"></script>
	<?php if (isset($_GET['page'])) { ?><script src="assets/js/<?php echo $_GET['page']; ?>.js"></script><?php 
 } ?>
    <?php if (isset($_GET['volet']) && $_GET['volet'] == 'template') { ?>
        <link rel="stylesheet" href="api/redactor/redactor.css" />
        <script src="api/redactor/redactor.min.js"></script>
        
        <script type="text/javascript">
            $(function() {
                $('#templateEditor').redactor({
                    focus: true
                });
            });
        </script>    
    <?php } ?>
</head>

<body class="flat">	
	<!-- Contenu concret de la page -->
	<header id="top">
		<h1><a class="nostyle" href="http://<?php echo Configuration::read('ini')['SERVER']['url']; ?>">LeQG</a></h1>
		<a class="nostyle" id="menu" href="#" title="Afficher le menu"></a>
		<a class="nostyle" id="notifications" href="#" title="Afficher les notifications"><?php $nb_taches = Evenement::taches_personnelles(); if ($nb_taches) : ?><span></span><?php 
 endif; ?></a>
		<a class="nostyle" id="rechercheRapide" href="#" title="Rechercher une fiche"></a>
		<form method="post" action="<?php Core::tpl_go_to('recherche'); ?>"><input type="search" id="searchForm" name="recherche" pattern=".{3,}" placeholder="Michel Dupont" autocomplete="off"><input type="submit" value="&#xe803;" id="searchSubmit"></form>
	</header><!--header#top-->
	
	<!-- Navigation principale -->
	<nav id="principale">
    <?php 
    if (User::auth_level() >= 5) :
        $menu = array(  'contacts' => 'Contacts',
                                'dossier' => 'Dossiers',
        /*	'carto' => 'Cartographie',*/
                                'sms' => 'SMS groupés',
                                'email' => 'Emails groupés',
                                'publi' => 'Publipostage',
                                'porte' => 'Porte-à-porte',
                                'boite' => 'Boîtage',
                                'rappels' => 'Rappels',
                                'administration' => 'Utilisateurs'  );
                    
        if (isset($_GET['page'])) {
            switch ($_GET['page']) {
            case 'contact':
                $actuel = 'contacts';
                break;
                        
            case 'campagne':
                $actuel = Configuration::read('tpl.actuel');
                break;
                        
            default:
                $actuel = $_GET['page'];
                break;
            }
        }
        
        foreach ($menu as $key => $element) : ?>
       <a href="<?php Core::tpl_go_to($key); ?>" id="lien-<?php echo $key; ?>" <?php if ($actuel == $key) { echo 'class="actif"'; 
      } ?>><?php echo $element; ?></a>
        <?php endforeach; ?>
   <a href="http://auth.leqg.info/deconnexion.php" id="lien-logout">Déconnexion</a>
    <?php else: ?>
		<a href="<?php Core::tpl_go_to('porte', array('action' => 'missions')); ?>" id="lien-porte">Porte-à-porte</a>
		<a href="<?php Core::tpl_go_to('boite', array('action' => 'missions')); ?>" id="lien-boite">Boîtage</a>
		<a href="<?php Core::tpl_go_to('rappels', array('action' => 'appel')); ?>" id="lien-rappels">Rappels</a>
		<a href="http://auth.leqg.info/deconnexion.php" id="lien-logout">Déconnexion</a>
    <?php endif; ?>
	</nav><!--nav#principale-->
	
	<main id="central" class="flat">
