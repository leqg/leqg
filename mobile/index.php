<?php

// On lance le système de statistique des temps de chargement
$loading['begin'] = microtime();

// On appelle le fichier d'inclusion du coeur du système
require_once('includes.php');

// On essaye de savoir s'il existe une page demandée
if (isset($_GET['page'])) :
	


// Si on ne détecte pas de page demandée, on charge l'accueil
else :

	$core->tpl_header();
	$core->tpl_load('services');
	$core->tpl_footer();

endif;

// Une fois les templates chargés, on met en place la purge et on calcule le temps nécessaire au chargement de la page à des fins de statistique
$loading['end'] = microtime();
$loading['time'] = $loading['end'] - $loading['begin'];
$loading['time-sql'] = number_format($loading['time'], 6, '.', '');

// On prépare la requête d'analyse du temps de chargement
$page = (isset($_GET['page'])) ? $_GET['page'] : 'index';
$query = 'INSERT INTO	`chargements` (`user_id`,
									   `chargement_page`,
									   `chargement_plateforme`,
									   `chargement_temps`)
							VALUES	  (' . $_COOKIE['leqg-user'] . ',
									   "' . $page . '",
									   "mobile",
									   "' . $loading['time-sql'] . '")';

// On exécute la requête d'enregistrement du temps de chargement
$noyau->query($query);

// On purge la page de ses connexions diverses aux bases de données ou aux API
require_once('purge.php');

?>