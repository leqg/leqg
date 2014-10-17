<?php

// On lance le système de statistique des temps de chargement
$loading['begin'] = microtime();

// On appelle le fichier d'inclusion du cœur du système
require_once('includes.php');


// On vérifie qu'une personne est connectée au système, on affiche le script de login

if (!$user->statut_connexion() || (isset($_GET['page']) && $_GET['page'] == 'login')) {

	// On regarde si on a reçu des informations POST
	if (isset($_POST['login'], $_POST['pass'])) {
	
		// On effectue la démarche de connexion
		$user->connexion($_POST['login'], $_POST['pass']);
		
	} else {
	
		// On affiche le script de connexion
		$core->tpl_load('login');
		
	}
	
} else {

	// On commence par appeler l'index.tpl si aucune page n'est appelée
	
	if (empty($_GET['page'])) {
	
		// On charge le header du site
		$core->tpl_header();
		
		// On charge l'affichage des services de la plateforme
		$core->tpl_load('services');
		
		// On charge le footer du site
		$core->tpl_footer();
	
	}
	
	// Si une page a été appelée, on calcule et on affiche son contenu
	else {
	
		// Si la page appelée est la déconnexion
		if ($_GET['page'] == 'logout') {
			// On appelle le script de déconnexion et on renvoit vers la page de login
			if ($user->deconnexion()) {
				$core->tpl_redirection();
			}
		}
		
		// Sinon, si la page demandée est une fiche
		
		else if ($_GET['page'] == 'fiche' && isset($_GET['id'])) {
			// On ouvre dans ce cas une fiche
			$fiche->acces(addslashes($_GET['id']), true);
			
			// On charge le template de fiche
			$core->tpl_load('fiche');
			
			// On ferme la fiche ouverte
			$fiche->fermeture();
		}
		
		else if ($_GET['page'] == 'contact' && isset($_GET['contact'])) { 
			// On charge le template correspondant à la fiche contact
			$core->tpl_load('contact');
		}
		
		else if ($_GET['page'] == 'fiche' && !empty($_GET['operation'])) {
			
			// On regarde si on demande la fusion de fiche
			if ($_GET['operation'] == 'fusion') {
				// On charge le template de fusion de fiche
				$core->tpl_header();
				$core->tpl_load('fiche', 'fusion');
				$core->tpl_footer();
			} elseif ($_GET['operation'] == 'choix') {
				// On charge le template de résolution des conflits
				$core->tpl_header();
				$core->tpl_load('fiche', 'fusion-conflits');
				$core->tpl_footer();
			} else if ($_GET['operation'] == 'creation') {
				// On charge le template de création de fiches
				$core->tpl_header();
				$core->tpl_load('fiche', 'ajout');
				$core->tpl_footer();
			}
			
		}
		
		else if ($_GET['page'] == 'creation' && isset($_GET['id'])) {
			// S'il s'agit de la création d'un dossier ou d'une tâche, on charge les templates relatifs à la page demandée
			$core->tpl_header();
			$core->tpl_load('creation', addslashes($_GET['type']));
			$core->tpl_footer();
		} 
		
		else if ($_GET['page'] == 'creation' && $_GET['type'] == 'dossier' && isset($_GET['submit'])) {
			// S'il s'agit de la création d'un dossier, on l'enregistre dans la base de données, en récupérant d'abord les variables
			$titre = $_POST['titre'];
			$description = $_POST['description'];
			$id = $_POST['id'];
			
			// On enregistre ces informations dans la base de données
			$dossier = $fiche->dossier_ajout($titre, $description, $id);
			
			$core->tpl_redirection('dossier', $dossier);
		}
		
		else if ($_GET['page'] == 'dossier' && is_numeric($_GET['id'])) {
			$core->tpl_header();
			$core->tpl_load('dossier');
			$core->tpl_footer();
		}
		
		else if ($_GET['page'] == 'dossier' && $_GET['action'] == 'ajout' && isset($_GET['id'])) {
			// On récupère les ID concernés
			$id = explode('-', $_GET['id']);
			$id = array('dossier' => $id[0],
						'fiche' => $id[1]);
			
			// On cherche les fiches déjà associées au dossier sélectionné
			$query = 'SELECT dossier_nom, dossier_contacts FROM dossiers WHERE dossier_id = ' . $id['dossier'];
			$sql = $db->query($query);
			$dossier = $core->formatage_donnees($sql->fetch_assoc());
			$fiches = explode(',', $dossier['contacts']);
			
			// On ajout dans le tableau la fiche demandée
			$fiches[] = $id['fiche'];
			
			// On formate pour la base de données la liste des fiches
			$fiches = implode(',', $fiches);
			
			$query = 'UPDATE dossiers SET dossier_contacts = "' . $fiches . '" WHERE dossier_id = ' . $id['dossier'];
			$db->query($query);
			
			// On ajoute maintenant cette action à l'historique du contact
			$fiche->historique_ajout($id['fiche'], 'autre', 'Ajout au dossier ' . $dossier['nom']);
			
			$core->tpl_redirection('dossier', $id['dossier']);
		}
		
		else if ($_GET['page'] == 'dossier' && $_GET['action'] == 'suppression' && isset($_GET['id'])) {
			// On récupère les ID concernés
			$id = explode('-', $_GET['id']);
			$id = array('dossier' => $id[0],
						'fiche' => $id[1]);
			
			// On cherche les fiches déjà associées au dossier sélectionné
			$query = 'SELECT dossier_nom, dossier_contacts FROM dossiers WHERE dossier_id = ' . $id['dossier'];
			$sql = $db->query($query);
			$dossier = $core->formatage_donnees($sql->fetch_assoc());
			$fiches = explode(',', $dossier['contacts']);
			
			// On recherche la clé correspondant à la valeur pour la supprimer
			$key = array_search($id['fiche'], $fiches);
			unset($fiches[$key]);
			
			// On enregistre la nouvelle liste dans la base de données
			$fiches = implode(',', $fiches);
			$query = 'UPDATE dossiers SET dossier_contacts = "' . $fiches . '" WHERE dossier_id = ' . $id['dossier'];
			$db->query($query);
			
			// On ajoute maintenant cette action à l'historique du contact
			$fiche->historique_ajout($id['fiche'], 'autre', 'Retrait du dossier ' . $dossier['nom']);

			$core->tpl_redirection('dossier', $id['dossier']);
		}
		
		else if ($_GET['page'] == 'creation' && $_GET['type'] == 'tache' && isset($_GET['submit'])) {
			// S'il s'agit de la création d'un dossier, on l'enregistre dans la base de données, en récupérant d'abord les variables
			$contenu_tache = $_POST['tache'];
			$id = $_POST['id'];
			$destinataire = implode(',', $_POST['destinataire']);
			$deadline = $_POST['deadline']; 
			
			// On enregistre ces informations dans la base de données
			$tache_id = $tache->creation( $contenu_tache , $deadline , $id , $destinataire );
						
			// On ajoute maintenant cette action à l'historique du contact
			$fiche->historique_ajout($id, 'autre', 'Nouvelle tâche associée : ' . $contenu_tache);

			$core->tpl_redirection('fiche', $id);
		}
		
		else if ($_GET['page'] == 'tache' && $_GET['action'] == 'suppression' && isset($_GET['id'])) {
			// On récupère les identifiants
			$id = explode('-', $_GET['id']);
			
			// On défini la tâche comme terminée dans la base de données
			$db->query("UPDATE taches SET tache_terminee = 1 WHERE tache_id = " . $id[1]);
			
			// On renvoit vers la fiche
			$core->tpl_redirection('fiche', $id[0]);
		}

		else if ($_GET['page'] == 'contacts') {
			
			// On charge le header, la page d'accueil du module contact et le footer
			$core->tpl_header();
			$core->tpl_load('contacts');
			$core->tpl_footer();
			
		}
		
		else if ($_GET['page'] == 'dossiers') {
			
			// On charge les tpl header, le tpl de la page d'accueil des dossiers et le tpl footer
			$core->tpl_header();
			$core->tpl_load('dossiers');
			$core->tpl_footer();
			
		}
		
		else if ($_GET['page'] == 'recherche') {

			// On prépare les différents champs à la recherche (suppression des espaces et des caractères, remplacement par des jokers
			$recherche = $core->formatage_recherche($_POST['recherche']);

			// On fait la recherche
			$query = 'SELECT contact_id FROM contacts WHERE CONCAT_WS(" ", contact_prenoms, contact_nom, contact_nom_usage, contact_nom, contact_prenoms) LIKE "%' . $recherche . '%" ORDER BY contact_nom, contact_nom_usage, contact_prenoms ASC';

			if (!empty($query)) $sql = $db->query($query);
			
			// On regarde le nombre de résultats
			$nb = $sql->num_rows;
			
			if ($nb == 1) {
				// S'il n'y a qu'un seul résultat, on ouvre la fiche correspondante
				$row = $sql->fetch_array();
				
				$core->tpl_go_to('contact', array('contact' => md5($row[0])), true);
			} else if ($nb > 1) {
				// On load le header de la page
				$core->tpl_header();
				
				// On load les différentes fiches
				echo '<section class="liste">';
				
				// On prépare le tableau des différentes fiches
				$fiches = array();
				
				while ($row = $sql->fetch_array()) {
				
					$fiche->acces($row[0], true);
					
					// On charge le template de la fiche
					$core->tpl_load('fiche', 'liste');
					
					// On ferme la fiche ouverte
					$fiche->fermeture();
				}
				
				echo '</section>';
				
				// On charge le footer
				$core->tpl_footer();
			} else {

				$core->tpl_load('fiche', 'vide');
			}
		}
		
		else if ($_GET['page'] == 'recherche-tag') {
			
			// On regarde si le tag existe
			$query = 'SELECT * FROM tags WHERE tag_nom LIKE "' . utf8_decode($_POST['tag']) . '"';
			$sql = $db->query($query);
			
			if ($sql->num_rows >= 1) {
				$tag = $sql->fetch_array();
				$tag = $tag[0];
				
				// On initialise la liste des fiches ayant ce tag
				$fiches = array();
				
				// On fait la recherche des fiches ayant ce tag de manière grossière
				$query = 'SELECT contact_tags, contact_id FROM contacts WHERE contact_tags LIKE "%' . $tag . '%"';
				$sql = $db->query($query);
				while ($row = $sql->fetch_array()) {
					$tags = explode(',', $row[0]);
					
					if (in_array($tag, $tags)) {
						$fiches[] = $row[1];
					}
				}
				
				if (count($fiches) == 1) {
					// Si nous n'avons qu'un seul résultat, on l'ouvre
					$core->tpl_redirection('fiche', $fiches[0]);
				} else if (count($fiches) > 1) {
					// On load le header de la page
					$core->tpl_header();
					
					// On load les différentes fiches
					echo '<section class="liste">';
					
					foreach ($fiches as $row) {
						$fiche->acces($row, true);
						
						// On charge le template de la fiche
						$core->tpl_load('fiche', 'liste');
						
						// On ferme la fiche ouverte
						$fiche->fermeture();
					}
					
					echo '</section>';
					
					// On charge le footer
					$core->tpl_footer();
				} else {
					$core->tpl_load('fiche', 'vide');
				}
			} else {
				$core->tpl_load('fiche', 'vide');
			}
		}
		
		else if ($_GET['page'] == 'creerfiche' && isset($_GET['etape'])) {
			
			// On charge dans tous les cas le template d'header
			$core->tpl_header();
			
			// Si l'étape de création est l'étape de recherche des doublons, on charge le tpl associé
			if ($_GET['etape'] == 'recherche') {
				$core->tpl_load('creerfiche', 'recherche');
			}
			
			// on charge dans tous les cas le template de footer
			$core->tpl_footer();
			
		}
		
		
	// Mise en place des conditions concernant le module cartographique
		
			else if ($_GET['page'] == 'carto') {
			
				// On charge d'abord le header
				$core->tpl_header();
				
					// On regarde quel module est demandé
					if (isset($_GET['module'])) {
					
						// Si on demande le module de l'arborescence
						if ($_GET['module'] == 'arborescence') {
						
							// On charge les sous-modules demandés (branches)
							
							if (isset($_GET['branche'])) {
								
								// On appelle la branche
								$core->tpl_load('carto', 'arborescence-' . $_GET['branche']);
								
							} else {
								
								// On charge le sommaire du module
								$core->tpl_load('carto', 'arborescence');
								
							}
							
						} else if ($_GET['module'] == 'bureaux') {
						
							// On charge les templates demandés dans ce module
							
							if (isset($_GET['bureau']) && is_numeric($_GET['bureau'])) {
								
								// Si on demande un bureau de vote directement, on charge le module correspondant
								$core->tpl_load('carto', 'bureau');
								
							} else {
								
								// On charge le sommaire du module
								$core->tpl_load('carto', 'bureaux');
								
							} 
							
						} else {
						
							// On charge le module 
							$core->tpl_load('carto', $_GET['module']);
							
						}
						
						
					} else {
						
						// On charge la page d'accueil du module cartographique si aucun module spécifique n'est demandé
						$core->tpl_load('carto');
						
					}
				
				// On charge ensuite le footer
				$core->tpl_footer();
			
			}
			
	
	// Mise en place des templates liés aux modules de recherche
	
			else if ( $_GET['page'] == 'rechercher' ) {
			
				// On charge d'abord le template de header
				$core->tpl_header(); 
				
				// On regarde s'il s'agit d'une recherche par tags, de fiches ou de dossiers
				
				if ( isset($_POST['tags']) ) {
					
					// S'il s'agit d'une recherche par tags
					$core->tpl_load('recherche', 'tags');
					
				} else {
					
					
					
				}
				
				// On charge enfin le template de footer
				$core->tpl_footer();
				
			}
			
			
	// Mise en place des templates liés au module de SMS
	
			else if ( $_GET['page'] == 'sms' ) {
				
				// On charge d'abord le template de header
				$core->tpl_header(); 
				
				// On charge les templates de page selon la demande
				if ( isset($_GET['action'])) {
					
					if ( $_GET['action'] == 'nouveau' ) : $core->tpl_load('sms', 'nouveau');
					elseif ( $_GET['action'] == 'historique' ) : $core->tpl_load('sms', 'historique');
					elseif ( $_GET['action'] == 'campagne' ) : $core->tpl_load('sms', 'campagne');
					elseif ( $_GET['action'] == 'reglages' ) : $core->tpl_load('sms', 'reglages');
					else : $core->tpl_load('sms'); endif;
					
				} else {
					
					$core->tpl_load('sms');
					
				}
				
				
				// On charge enfin le template de footer
				$core->tpl_footer();
				
			}
			
			
	// Mise en place des templates liés au module d'emailing
	
			else if ( $_GET['page'] == 'email' ) {
				
				// On charge d'abord le template de header
				$core->tpl_header(); 
				
				// On charge les templates de page selon la demande
				if ( isset($_GET['action'])) {
					
					if ( $_GET['action'] == 'nouveau' ) : $core->tpl_load('email', 'nouveau');
					elseif ( $_GET['action'] == 'historique' ) : $core->tpl_load('email', 'historique');
					elseif ( $_GET['action'] == 'campagne' ) : $core->tpl_load('email', 'campagne');
					elseif ( $_GET['action'] == 'reglages' ) : $core->tpl_load('email', 'reglages');
					else : $core->tpl_load('email'); endif;
					
				} else {
					
					$core->tpl_load('email');
					
				}
				
				
				// On charge enfin le template de footer
				$core->tpl_footer();
				
			}
			
			
	// Mise en place des templates liés au module de porte à porte
	
			else if ( $_GET['page'] == 'porte' ) {
				
				// On charge les templates de page selon la demande
				if ( isset($_GET['action'])) {
					
					// On charge d'abord le template de header
					$core->tpl_header(); 
					
					if ( $_GET['action'] == 'nouveau' ) : $core->tpl_load('porte', 'nouveau');
					elseif ( $_GET['action'] == 'missions' ) : $core->tpl_load('porte', 'missions');
					elseif ( $_GET['action'] == 'mission' ) : $core->tpl_load('porte', 'mission');
					elseif ( $_GET['action'] == 'reporting' ) : $core->tpl_load('porte', 'reporting');
					elseif ( $_GET['action'] == 'reglages' ) : $core->tpl_load('porte', 'reglages');
					else : $core->tpl_load('porte'); endif;
					
					// On charge enfin le template de footer
					$core->tpl_footer();
				
				} else if ( isset($_GET['mission']) ) {
	
					$core->tpl_load('porte', 'mission');
				
				} else {
					
					$core->tpl_load('porte');
					
				}
				
			}
			
			
	// Mise en place des templates liés au module de boîtage
	
			else if ( $_GET['page'] == 'boite' ) {
				
				// On charge les templates de page selon la demande
				if ( isset($_GET['action'])) {
					
					// On charge d'abord le template de header
					$core->tpl_header(); 
					
					if ( $_GET['action'] == 'nouveau' ) : $core->tpl_load('boite', 'nouveau');
					elseif ( $_GET['action'] == 'missions' ) : $core->tpl_load('boite', 'missions');
					elseif ( $_GET['action'] == 'mission' ) : $core->tpl_load('boite', 'mission');
					elseif ( $_GET['action'] == 'reporting' ) : $core->tpl_load('boite', 'reporting');
					elseif ( $_GET['action'] == 'reglages' ) : $core->tpl_load('boite', 'reglages');
					else : $core->tpl_load('boite'); endif;
					
					// On charge enfin le template de footer
					$core->tpl_footer();
				
				} else if ( isset($_GET['mission']) ) {
	
					$core->tpl_load('boite', 'mission');
				
				} else {
					
					$core->tpl_load('boite');
					
				}
				
			}
			
			
	// Mise en place des templates liés au module de publipostage
	
			else if ( $_GET['page'] == 'poste' ) {
				
				// On charge d'abord le template de header
				$core->tpl_header(); 
				
				// On charge les templates de page selon la demande
				if ( isset($_GET['action'])) {
					
					if ( $_GET['action'] == 'nouveau' ) : $core->tpl_load('poste', 'nouveau');
					elseif ( $_GET['action'] == 'historique' ) : $core->tpl_load('poste', 'historique');
					elseif ( $_GET['action'] == 'campagne' ) : $core->tpl_load('poste', 'campagne');
					elseif ( $_GET['action'] == 'reglages' ) : $core->tpl_load('poste', 'reglages');
					else : $core->tpl_load('email'); endif;
					
				} else {
					
					$core->tpl_load('poste');
					
				}
				
				
				// On charge enfin le template de footer
				$core->tpl_footer();
				
			}
			
	
	// Mise en place des templates associés au système de gestion des utilisateurs
	
			else if ( $_GET['page'] == 'utilisateur' ) {
				
				// On charge d'abord le template de header
				$core->tpl_header();
				
				// ON regarde si une action est demandée
				if (isset($_GET['action'])) {
					
					
					
				// Sinon, on charge la fiche utilisateur pour information et modification
				} else {
					
					// Template de modification de la fiche de l'utilisateur courant
					$core->tpl_load('utilisateur'); 
					
				}
				
				// On charge enfin le template de footer
				$core->tpl_footer();
				
			}
			
			
	// Mise en place des templates associés à l'administration
	
			else if ( $_GET['page'] == 'administration' ) {
				
				// On charge d'abord le template de header
				$core->tpl_header();
				
				// On regarde si une action spécifique est demandée
				if (isset($_GET['historique'])) {
					
					// On charge le template d'historique
					$core->tpl_load('admin', 'historique');
					
					
				// Sinon, on charge la page de gestion des utilisateurs	
				} else {
					
					// Template de gestion des comptes utilisateurs
					$core->tpl_load('admin', 'users');
					
				}
				
				// On termine en chargeant le template de footer
				$core->tpl_footer();
				
			}
			
		
		else {
			// On redirige temporairement vers la page de présentation des services
			$core->tpl_header();
			$core->tpl_load('services');
			$core->tpl_footer();
		}
	}
}


// Une fois les templates chargés, on met en place la purge et on calcule le temps nécessaire au chargement de la page à des fins de statistique
$loading['end'] = microtime();
$loading['time'] = $loading['end'] - $loading['begin'];
$loading['time-sql'] = number_format($loading['time'], 6, '.', '');

// On prépare la requête d'analyse du temps de chargement
$personne_connectee = (isset($_COOKIE['leqg-user'])) ? $_COOKIE['leqg-user'] : 0;
$page = (isset($_GET['page'])) ? $_GET['page'] : 'index';
$query = 'INSERT INTO	`chargements` (`user_id`,
									   `chargement_page`,
									   `chargement_plateforme`,
									   `chargement_temps`)
							VALUES	  (' . $personne_connectee . ',
									   "' . $page . '",
									   "desktop",
									   "' . $loading['time-sql'] . '")';

// On exécute la requête d'enregistrement du temps de chargement
$noyau->query($query);


// On appelle les fichiers de purge
require_once('purge.php');

?>