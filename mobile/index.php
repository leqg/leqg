<?php

// On lance le système de statistique des temps de chargement
$loading['begin'] = microtime();

// On appelle le fichier d'inclusion du coeur du système
require_once('includes.php');


// On vérifie qu'une personne est connectée au système, on affiche le script de login

if (!$user->statut_connexion() || (isset($_GET['page']) && $_GET['page'] == 'login')) :

	// On regarde si on a reçu des informations POST
	if (isset($_POST['login'], $_POST['pass'])) :
	
		// On effectue la démarche de connexion
		$user->connexion($_POST['login'], $_POST['pass']);
		
	else :
	
		// On affiche le script de connexion
		$core->tpl_header('login');
		$core->tpl_load('login');
		$core->tpl_footer();
		
	endif;
	
else :
	
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
					
		
		// S'il s'agit de l'affichage des résultats d'une recherche
		elseif ($_GET['page'] == 'resultats') :
			
			// On prépare le contenu à rechercher
			$recherche = $core->formatage_recherche($_POST['recherche']);
			
			// On prépare la requête
			$query = 'SELECT contact_id FROM contacts WHERE CONCAT_WS(" ", contact_prenoms, contact_nom, contact_nom_usage, contact_nom, contact_prenoms) LIKE "%' . $recherche . '%" ORDER BY contact_nom, contact_nom_usage, contact_prenoms ASC';
			
			// On exécute la recherche et on enregistre les contacts trouvés dans un tableau contacts
			$sql = $db->query($query);
			
			// On regarde s'il n'y a qu'une réponse à la recherche. Si oui, on charge directement la fiche
			if ($sql->num_rows == 1) :
			
				$contact = $sql->fetch_assoc();
				$core->tpl_go_to('contacts', array('fiche' => $contact['contact_id']), true);
			
			else :
			
				// On met en place le tableau des contacts trouvés
				$contacts = array();
				while ($row = $sql->fetch_assoc()) $contacts[] = $row['contact_id'];
				
				// On charge les templates d'affichage des résultats
				$core->tpl_header();
				
					// On affiche les premières lignes d'encadrement du résultat
					echo '<h2>Résultats</h2>';
					echo '<ul class="listeEncadree">';
					
					// On lance la boucle par fiche contact trouvée
					foreach($contacts as $contact) : $fiche->acces($contact, true);
						$core->tpl_load('resultats');
					endforeach;
					
					// On affiche les dernières lignes d'encadrement du résultat
					echo '</ul>';
				
				// On charge le template du pied de page
				$core->tpl_footer();
			
			endif;
		
			
		// S'il s'agit du module contact
		elseif ($_GET['page'] == 'contacts') :
			
			// On regarde s'il s'agit d'une page particulière, sinon on met l'accueil du module
			if (isset($_GET['fiche'])) :
			
				// On charge l'entête
				$core->tpl_header();
				
				// On ouvre la fiche contact
				$fiche->acces($_GET['fiche'], true);

				// On charge le template contact
				$core->tpl_load('fiche');
				
				// On ferme la fiche ouverte
				$fiche->fermeture();
				
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
			$core->tpl_load('contacts');
			$core->tpl_footer();
		
		endif;
		
	
	// Si on ne détecte pas de page demandée, on charge l'accueil
	else :
	
		$core->tpl_header();
		$core->tpl_load('contacts');
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