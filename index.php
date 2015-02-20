<?php
// Fichier d'aiguillage des requêtes du site

// On lance le système de statistique des temps de chargement
$loading['begin'] = microtime();

// On appelle le fichier d'inclusion du cœur du système
require_once('includes.php');

// On vérifie juste qu'un cookie existe auquel cas, on redirige vers l'auth system
//if (!isset($_COOKIE['leqg'])) header('Location: http://auth.leqg.info');
//Core::debug($_GET['page'], false);

/** ON RÉALISE LE TRAITEMENT AUTOMATISÉ DE CHARGEMENT DES TEMPLATES SELON LES PAGES APPELÉES **/

// Si aucune page n'a été appelée, on affiche selon l'accréditation la page des services ou le module contacts
if (empty($_GET['page'])) {
	
	// Redirection si l'utilisateur à une accréditation inférieure au niveau 5
	if (User::auth_level() >= 5) {
		Core::tpl_go_to('contacts', true);
	}
	
	// Redirection si l'utilisateur n'est que faiblement accrédité à la présentation des services
	else {
		Core::tpl_go_to('services', true);
	}

}

// Si une page a été appelée, on calcule et on affiche son contenu
else {
    
	// Si on demande l'affichage du module contacts
	if ($_GET['page'] == 'contacts') {
		Core::tpl_load('contacts');
	}
	
	
	
	// Si on demande l'affichage d'une fiche contact
	else if ($_GET['page'] == 'contact') { 
	    if (isset($_GET['contact'])) {
		    Core::tpl_load('contact');
	    }
	    
	    elseif (isset($_GET['operation'])) {
		    // Si l'opération consiste en une création, on créé le contact ici
		    if ($_GET['operation'] == 'creation') {
			    // On va commencer par créer une nouvelle fiche et récupérer son identifiant
			    $id = Contact::creation();
			    
			    // On redirige vers la nouvelle fiche créée
			    Core::tpl_go_to('contact', array('contact' => md5($id)), true);
		    }
		    
		    // Sinon, on charge le template
		    else {
			    Core::tpl_load('contact', $_GET['operation']);
		    }
	    }
	    
	    else {
		    Core::tpl_go_to('contacts', true);
	    }
	}
	
	
	
	// Si on demande l'affichage du module dossiers	
	else if ($_GET['page'] == 'dossiers') { Core::tpl_load('dossiers'); }
	
	
	
	// Si on demande l'affichage d'un dossier
	else if ($_GET['page'] == 'dossier') {
		if (isset($_GET['dossier'])) {
			Core::tpl_load('dossier');
		}
		else {
			Core::tpl_load('dossiers');
		}
	}
	
	
	
	// Si on demande l'affichage d'une recherche d'utilisateur ou de tag
	else if ($_GET['page'] == 'recherche') { Core::tpl_load('recherche'); }
	
	else if ($_GET['page'] == 'recherche-thematique') { Core::tpl_load('recherche', 'thematique'); }
	
	
	
	// Si on demande le module cartographique
	else if ($_GET['page'] == 'carto') {
    	
		// Si un niveau d'exploration a été demandé
		if (isset($_GET['niveau'], $_GET['code'])) {
			Core::tpl_load('carto', $_GET['niveau']);
		}
		
		// Sinon, on charge la page d'accueil du module
		else {
			Core::tpl_load('carto');
		}
		
	}
	
	
	
	// Si on demande le module de campagnes
	else if ($_GET['page'] == 'campagne') {
    	    // On regarde si on demande une fiche particulière
    	    if (isset($_GET['id'])) {
        	    Core::tpl_load('campaign', 'dashboard');
    	    }
    	    
    	    // Sinon, on appelle la page principale du module
    	    else {
        	    Core::tpl_load('campaign');
    	    }
	}
	
	
	
	// Si on demande le module SMS
	else if ($_GET['page'] == 'sms') {
		// On regarde si une page en particulier est demandée
		if (isset($_GET['campagne'])) {
			Core::tpl_load('sms', 'campagne');
		}
		
		// Sinon, on appelle la page principale du module
		else {
			Core::tpl_load('sms');
		}
	}
	
	
	
	// Si on demande le module Email
	else if ($_GET['page'] == 'email') {
		// On regarde si une page en particulier est demandée
		if (isset($_GET['campagne'])) {
			Core::tpl_load('email', 'campagne');
		}
		
		// Sinon, on appelle la page principale du module
		else {
			Core::tpl_load('email');
		}
	}
	
	
	
	// Si on demande le module Publipostage
	else if ($_GET['page'] == 'publi') {
		// On regarde si une page en particulier est demandée
		if (isset($_GET['campagne'])) {
			Core::tpl_load('publi', 'campagne');
		}
		
		// Sinon, on appelle la page principale du module
		else {
			Core::tpl_load('publi');
		}
	}
	
	
	
	// Si on demande le module d'administration d'une mission (porte à porte + boitage)
	else if ($_GET['page'] == 'mission') {
    	// On charge les panneaux d'administration
    	if (isset($_GET['admin'])) {
        	Core::tpl_load('mission', $_GET['admin']);
    	}
    	
    	// On charge le template d'administration générale de la page
    	else {
        	Core::tpl_load('mission');
    	}
	}
	
	
	
	// Si on demande le module d'administration d'une mission (porte à porte + boitage)
	else if ($_GET['page'] == 'reporting') {
    	// On charge les panneaux d'administration
    	if (isset($_GET['action'])) {
        	Core::tpl_load('reporting', $_GET['action']);
    	}
    	
    	elseif (isset($_GET['rue'])) {
        	Core::tpl_load('reporting', 'rue');
    	}
    	
    	// On charge le template d'administration générale de la page
    	else {
        	Core::tpl_load('reporting');
    	}
	}
	
	
	
	// Si on demande le module de porte à porte
	else if ($_GET['page'] == 'porte') {
		// On charge les templates de page selon la demande
		if (isset($_GET['action'])) {
			Core::tpl_load('porte', $_GET['action']);
		
		} else if (isset($_GET['mission']) && !isset($_GET['rue'])) {
			Core::tpl_load('porte', 'mission');
		
		} else if (isset($_GET['reporting'])) {
			Core::tpl_load('porte', 'reporting');
		
		} else if (isset($_GET['rue']) && isset($_GET['mission'])) {
			Core::tpl_load('porte', 'reporting-rue');
		
		} else {
			Core::tpl_load('porte');
		}
	}
	
	
	
	// Si on demande le module de boîtage
	else if ($_GET['page'] == 'boite') {
		// On charge les templates de page selon la demande
		if ( isset($_GET['action'])) {

			Core::tpl_load('boite', $_GET['action']);
		
		} else if ( isset($_GET['mission']) && !isset($_GET['rue']) ) {
			Core::tpl_load('boite', 'mission');
			
		} else if ( isset($_GET['rue']) && isset($_GET['mission']) ) {
            Core::tpl_load('boite', 'reporting-rue');
		
		} else {
			Core::tpl_load('boite');
		}
	}
	
	
	
	// Redirection vers la campagne de rappels depuis les fiches contact
	else if ($_GET['page'] == 'rappel') {
		// On vérifie qu'une campagne est bien demandée
		if (isset($_GET['campagne'])) {
			// On récupère le numéro de l'argumentaire correspondant
			$query = $link->prepare('SELECT `argumentaire_id` FROM `argumentaires` WHERE MD5(`argumentaire_id`) = :id');
			$query->bindParam(':id', $_GET['campagne']);
			$query->execute();
			$data = $query->fetch(PDO::FETCH_NUM);
			
			// On redirige vers la vraie URL
			Core::tpl_go_to('rappels', array('mission' => $data[0]), true);
		}
	}
	
	
	
	// Si on demande le module de rappels téléphoniques
	else if ($_GET['page'] == 'rappels') {
		// On charge les templates de page selon la demande
		if ( isset($_GET['action']) )
		{
			    Core::tpl_load('rappels', $_GET['action']);
		}
		
		// Si c'est une page d'argumentaire, on charge l'argumentaire
		elseif ( isset($_GET['mission']) )
		{
			    Core::tpl_load('rappels', 'mission');
		}
		
		// Sinon, c'est la page d'accueil du module rappels
		else
		{
			    Core::tpl_load('rappels');
		}
	}
	
	
	
	// Si on demande le module d'administration
	else if ($_GET['page'] == 'administration') {
		// On regarde si une action spécifique est demandée
		if (isset($_GET['compte'])) {
			// On charge le template d'historique
			Core::tpl_load('admin', 'user');
		}
		
		elseif (isset($_GET['action']) && $_GET['action'] == 'nouveau') {
    		Core::tpl_load('admin', 'new');
		}
		
		elseif (isset($_GET['action']) && $_GET['action'] == 'creation') {
    		Core::tpl_load('admin', 'creation');
		}
			
		// Sinon, on charge la page de gestion des utilisateurs	
		else {
			// Template de gestion des comptes utilisateurs
			Core::tpl_load('admin', 'users');
		}
	}
	
	
	
	// Si on demande le module d'affichage des services
	else if ($_GET['page'] == 'services') {
		// Si l'utilisateur a une forte accréditation, il s'agit de l'écran d'accueil du module contacts qui est demandé
		if (User::auth_level() >= 5) {
			Core::tpl_go_to('contacts', true);
		}
		
		// Sinon on affiche effectivement le module des services
		else {
			Core::tpl_load('services');
		}
	}
	
	
	
	// On redirige automatiquement sur vers la page de présentation des services ou le module contacts en cas de module non trouvé selon l'accréditation
	else {
		if (User::auth_level() >= 5) {
			Core::tpl_go_to('contacts', true);
		}
		
		else {
			Core::tpl_go_to('services', true);
		}
	}
}


// Une fois les templates chargés, on met en place la purge et on calcule le temps nécessaire au chargement de la page à des fins de statistique
$loading['end'] = microtime();
$loading['time'] = $loading['end'] - $loading['begin'];
$loading['time-sql'] = number_format($loading['time'], 6, '.', '');

// On prépare la requête d'analyse du temps de chargement
$page = (isset($_GET['page'])) ? $_GET['page'] : '';

// On enregistre le temps de chargement de la page à des fins statistiques
$query = $core->prepare('INSERT INTO `stats` (`user`, `page`, `time`) VALUES (:compte, :page, :temps)');
$utilisateur = User::ID();
$query->bindParam(':compte', $utilisateur);
$query->bindParam(':page', $page);
$query->bindParam(':temps', $loading['time-sql']);
$query->execute();
?>
