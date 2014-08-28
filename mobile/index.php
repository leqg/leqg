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

	// On essaye de savoir s'il existe une page demandée
	if (isset($_GET['page'])) :
		
		// On commence par mettre en place la page de déconnexion
		if ($_GET['page'] == 'deconnexion') :
		
			// On appelle le script de déconnexion et on renvoit vers la page de login
			if ($user->deconnexion()) :
				
				// On retourne vers la page de login
				$core->tpl_go_to('login', array(), true);
				
			endif;
			
			
		// S'il s'agit d'une recherche
		elseif ($_GET['page'] == 'recherche') :
		
			$core->tpl_header();
			$core->tpl_load('recherche');
			$core->tpl_footer();
		
			
		// S'il s'agit du module contact
		elseif ($_GET['page'] == 'contacts') :
			
			// On regarde s'il s'agit d'une page particulière, sinon on met l'accueil du module
			if (isset($_GET['fiche'])) :
			
				// On charge l'entête
				$core->tpl_header();
				
				// On ouvre la fiche contact
				
				// On charge le template contact
				$core->tpl_load('fiche');
				
				// On charge le footer
				$core->tpl_footer();
			
			// Si aucune page précise n'a été demandée, on affiche l'accueil du module
			else :
			
				// On charge les éléments du template
				$core->tpl_header();
				$core->tpl_load('contacts');
				$core->tpl_footer();
			
			endif;
			
		
		// S'il ne s'agit d'aucune de ces pages, on redirige vers les services
		else :
		
			$core->tpl_header();
			$core->tpl_load('services');
			$core->tpl_footer();
		
		endif;
		
	
	// Si on ne détecte pas de page demandée, on charge l'accueil
	else :
	
		$core->tpl_header();
		$core->tpl_load('services');
		$core->tpl_footer();
	
	endif;
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