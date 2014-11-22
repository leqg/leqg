<?php

// On lance le système de statistique des temps de chargement
$loading['begin'] = microtime();

// On appelle le fichier d'inclusion du coeur du système
require_once('includes.php');


/** ON RÉALISE LE TRAITEMENT AUTOMATISÉ DE CHARGEMENT DES TEMPLATES SELON LES PAGES APPELÉES **/

// On essaye de savoir s'il existe une page demandée
if (isset($_GET['page'])) :
	
	// On commence par mettre en place la page de déconnexion
	if ($_GET['page'] == 'deconnexion') :
	
		// On appelle le script de déconnexion et on renvoit vers la page de login
		if ($user->deconnexion()) :
			
			// On retourne vers la page de login
			Core::tpl_go_to('login', array(), true);
			
		endif;
		
		
	// S'il s'agit d'une recherche
	elseif ($_GET['page'] == 'recherche') :
	
		Core::tpl_load('recherche');
				
	
	// S'il s'agit de l'affichage des résultats d'une recherche
	elseif ($_GET['page'] == 'resultats') :
		
		Core::tpl_load('resultats');
	
		
	// S'il s'agit du module contact
	elseif ($_GET['page'] == 'contacts') :
		
		// On regarde s'il s'agit d'une page particulière, sinon on met l'accueil du module
		if (isset($_GET['fiche'])) :
		
			Core::tpl_load('fiche');
		
		// Si aucune page précise n'a été demandée, on affiche l'accueil du module
		else :
		
			// On charge les éléments du template
			Core::tpl_load('contacts');
		
		endif;
		
	
	// S'il s'agit d'une page du module d'interactions
	elseif ($_GET['page'] == 'interaction') :
	
		// On regarde si une action particulière est demandée
		if (isset($_GET['action'])) :
		
			// Si l'action demandée concerne l'ajout d'une interaction
			if ($_GET['action'] == 'ajout') :
			
				// On charge les éléments de template
				Core::tpl_load('interaction-ajout');

			// Sinon, on redirige vers le module contact, vers une fiche si une fiche existait				
			else :
			
				if (isset($_GET['fiche'])) :
				
					Core::tpl_go_to('contacts', array('fiche' => $_GET['fiche']), true);
				
				else :
				
					Core::tpl_go_to('contacts', array(), true);
				
				endif;
			
			endif;
		
		else :
		
			// Si aucune action n'est demandée, on charge la page de lecture d'une interaction s'il existe une fiche demandée
			if (isset($_GET['interaction'])) :
			
				Core::tpl_load('interaction');
			
			// Sinon, on charge le module "contacts"	
			else :
			
				Core::tpl_go_to('contacts', array(), true);
				
			endif;
				
		endif;

	
	// S'il s'agit du module de boîtage ou de porte-à-porte 
	elseif ($_GET['page'] == 'boitage' || $_GET['page'] == 'porte') :
	
		// On regarde s'il s'agit d'une action particulière, sinon on charge le démarrage du module
			if (isset($_GET['mission']) && isset($_GET['rue'])) { Core::tpl_load($_GET['page'], 'rue'); }
			
		elseif (isset($_GET['mission']) && isset($_GET['immeuble'])) { Core::tpl_load($_GET['page'], 'immeuble'); }
		
		elseif (isset($_GET['mission'])) { Core::tpl_load($_GET['page'], 'mission'); }
			
		else { Core::tpl_load($_GET['page']); }
	
	
	// S'il ne s'agit d'aucune de ces pages, on redirige vers les services
	else :
	
		Core::tpl_header();
		Core::tpl_load($_GET['page']);
		Core::tpl_footer();
	
	endif;
	

// Si on ne détecte pas de page demandée, on charge l'accueil
else :

	Core::tpl_header();
	Core::tpl_load('services');
	Core::tpl_footer();

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
$core->query($query);

?>