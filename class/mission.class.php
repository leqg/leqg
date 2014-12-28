<?php
/**
 * Classe de traitement des missions
 *
 * Cette classe permet de traiter l'ensemble des demandes liées aux missions,
 * aussi bien porte à porte que boîtage
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */

class Mission {
	
	/**
	 * @val     array    $data          Informations stockées sur la mission
	 * @val     object   $link          Informations d'accès à la base de données
	 * @val     string   $err           Erreurs stockées au sein de la classe
	 * @val     string   $err_msg       Message de la dernière erreur stockée
	 */
	
	private $data, $link;
	public  $err, $err_msg;
	
	
	/**
	 * Constructeur de la classe Mission permettant de charger les informations
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @param   string   $id            Idenfitiant de la mission
	 * @result  void
	 */
	
	public function __construct( $id ) {
		// On récupère la connexion à la base de données
		$this->link = Configuration::read('db.link');
		
		// On cherche à récupérer les informations liées à cette mission
		$query = $this->link->prepare('SELECT *, MD5(`mission_id`) AS `mission_hash` FROM `mission` WHERE MD5(`mission_id`) = :mission');
		$query->bindParam(':mission', $id);
		$query->execute();
		
		if (!$query->rowCount()) {
    		$this->err = true;
    		$this->err_msg = 'Mission inexistante';
        }
		
		// On récupère les informations
		$mission = $query->fetch(PDO::FETCH_ASSOC);
		
		// On stocke les informations dans la classe
		$this->data = $mission;
	}
	
	
	/**
	 * Récupère une information sur la mission
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @param   string   $information   Information demandée
	 * @result  mixed                   Valeur de l'information demandée
	 */
	
	public function get( $information ) {
		return $this->data[ $information ];
	}
	
	
	/**
	 * Modifie une information sur la mission
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @param   string   $information   Information à modifier
	 * @param   string   $value         Valeur à enregistrer
	 * @result  bool                    Réussite ou non de l'opération
	 */
	
	public function set($information, $value) {
    	$mission = $this->get('mission_id');
		$query = $this->link->prepare('UPDATE `mission` SET `' . $information . '` = :valeur WHERE `mission_id` = :mission');
		$query->bindParam(':valeur', $value);
		$query->bindParam(':mission', $mission);
		
		// On retourne un résultat selon la réussite ou non de l'opération
		if ($query->execute()) {
    		return true;
		}
		else {
    		return false;
		}
	}
	
	
	/**
     * Récupère les statistiques sur les militants inscrits à la mission
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
     * 
     * @result  array                   Statistiques disponibles
     */
    
    public function statistiques_militant() {
        // On récupère les variables
        $mission = $this->get('mission_id');
        
        // On récupère la liste des militants inscrits à la mission quelque soit leur statut
        $query = $this->link->prepare('SELECT * FROM `inscriptions` WHERE `mission_id` = :mission');
        $query->bindParam(':mission', $mission, PDO::PARAM_INT);
        $query->execute();
        
        // On vérifie qu'il existe des militants
        if ($query->rowCount()) {
            // On récupère la liste de ces militants
            $militants = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // On prépare le tableau des statistiques
            $statut = array(
                -1 => 'refus',
                 0 => 'invitation',
                 1 => 'inscrit'
            );
            
            // On lance une boucle de calcul du statut des militants
            $stats = array(
                'refus' => 0,
                'invitation' => 0,
                'inscrit' => 0
            );
            
            foreach ($militants as $militant) {
                $stats[$statut[$militant['inscription_statut']]]++;
            }
            
            // On cherche la liste de tous les reportings pour récupérer les statistiques par militants
            $query = $this->link->prepare('SELECT `' . $this->get('mission_type') . '_militant` AS `militant` FROM `' . $this->get('mission_type') . '` WHERE `mission_id` = :mission');
            $query->bindParam(':mission', $mission, PDO::PARAM_INT);
            $query->execute();
            
            // On regarde s'il existe du reporting
            if ($query->rowCount()) {
                // On enregistre le nombre de reportings
                $stats['reporting'] = $query->rowCount();
                
                // On fait une boucle de tous les reportings pour compter l'activité par utilisateur
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $reporting) {
                    // On incrémente le nombre de reportings du militant
                    $stats['militants'][$reporting['militant']]++;
                }
				
				// On tri le tableau des statistiques selon leur participation
                asort($stats['militants']);
                
                // On récupère le militant le plus actif
                $militants_actifs = array_keys($stats['militants']);
                $stats['actif'] = $militants_actifs[0];
            }
            
            // Sinon, on ne retourne aucun reporting
            else {
                $stats['reporting'] = 0;
            }
            
            // On retourne les informations récupérées
            return $stats;
        }
        
        // Sinon on ne retourne aucune statistiques
        else {
            return false;
        }
    }
    
    
    /**
     * Récupération des membres inscrits à la mission selon leur statut
     *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
     * 
     * @param   string      $statut     Statut demandé
     * @result  array                   Liste des membres inscrits
     */
    
    public function liste_inscrits($statut = null) {
        // On récupère la liste des inscrits pour le statut demandé
        $mission = $this->get('mission_id');
        if (is_null($statut)) {
            $query = $this->link->prepare('SELECT `user_id` FROM `inscriptions` WHERE `mission_id` = :mission');
            $query->bindParam(':mission', $mission, PDO::PARAM_INT);
            $query->execute();
            
            // On vérifie qu'il existe des inscrits
            if ($query->rowCount()) {
                // On retourne le tableau
                $users = $query->fetchAll(PDO::FETCH_ASSOC);
                $militants = array();
                
                foreach ($users as $user) $militants[] = $user['user_id'];
                
                return $militants;
            }
            
            // Sinon, on retourne un false
            else {
                return false;
            }
        }
        else {
            $query = $this->link->prepare('SELECT `user_id` FROM `inscriptions` WHERE `inscription_statut` = :statut AND `mission_id` = :mission');
            $query->bindParam(':statut', $statut);
            $query->bindParam(':mission', $mission, PDO::PARAM_INT);
            $query->execute();
            
            // On vérifie qu'il existe des inscrits
            if ($query->rowCount()) {
                // On retourne le tableau
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }
            
            // Sinon, on retourne un false
            else {
                return false;
            }
        }
    }
    
    
    /**
     * Invitation d'un nouveau membre
     *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
     * 
     * @param   string      $user       Utilisateur invité
     * @result  bool                    Résultat
     */
    
    public function invitation($user) {
        $mission = $this->get('mission_id');
        $query = $this->link->prepare('INSERT INTO `inscriptions` (`mission_id`, `user_id`) VALUES (:mission, :user)');
        $query->bindParam(':mission', $mission);
        $query->bindParam(':user', $user);
        
        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    
    /**
     * Réponse à une invitation
     *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
     * 
     * @param   int         $reponse    Réponse à l'invitation
     * @param   string      $user       Utilisateur concerné
     * @result  bool                    Résultat
     */
    
    public function reponse($reponse, $user) {
        $mission = $this->get('mission_id');
        $query = $this->link->prepare('UPDATE `inscriptions` SET `inscription_statut` = :reponse WHERE MD5(`user_id`) = :user AND `mission_id` = :mission');
        $query->bindParam(':reponse', $reponse);
        $query->bindParam(':mission', $mission);
        $query->bindParam(':user', $user);
        
        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
	
	
	/**
	 * Récupère les statistiques sur les éléments à parcourir
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
     * 
     * @result  array                   Statistiques disponibles
     */
    
    public function statistiques_parcours() {
        // On récupère l'identifiant de la mission
        $mission = $this->get('mission_id');
        
        // On récupère tous les items à visiter dans la rue
        $query = $this->link->prepare('SELECT * FROM `items` WHERE `mission_id` = :mission');
        $query->bindParam(':mission', $mission);
        $query->execute();
        
        if ($query->rowCount()) {
            $items = $query->fetchAll(PDO::FETCH_ASSOC);
            $stats = array(
                'attente' => 0,
                'absent' => 0,
                'ouvert' => 0,
                'procuration' => 0,
                'contact' => 0,
                'npai' => 0
            );
            
            foreach ($items as $item) {
                // On regarde si ce n'est pas encore fait
                if ($item['item_statut'] == 0) {
                    $stats['attente']++;
                }
                
                else {
                    if ($item['item_statut'] ==  1) $stats['absent']++;
                    if ($item['item_statut'] ==  2) $stats['ouvert']++;
                    if ($item['item_statut'] ==  3) $stats['procuration']++;
                    if ($item['item_statut'] ==  4) $stats['contact']++;
                    if ($item['item_statut'] == -1) $stats['npai']++;
                }
            }
            
            // On calcule la réalisation
            $stats['total'] = array_sum($stats);
            $stats['fait'] = $stats['total'] - $stats['attente'];
            $stats['proportion'] = ceil($stats['fait'] * 100 / $stats['total']);
            
            return $stats;
        }
        
        else {
            return false;
        }
    }
    
	
	
	/**
	 * Récupère la liste des rues à parcourir dans la mission
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
     * 
     * @result  array                   Liste des rues à parcourir
     */
    
    public function rues() {
        $mission = $this->get('mission_id');
        
        // On effectue une récupération de toutes les rues de la mission
        $query = $this->link->prepare('SELECT DISTINCT `rue_id` FROM `items` WHERE `mission_id` = :mission');
        $query->bindParam(':mission', $mission);
        $query->execute();
        
        // On vérifie s'il existe des rues à parcourir
        if ($query->rowCount()) {
            $rues = $query->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($rues as $key => $rue) {
                $query = $this->link->prepare('SELECT * FROM `rues` WHERE `rue_id` = :rue');
                $query->bindParam(':rue', $rue['rue_id']);
                $query->execute();
                $street = $query->fetch(PDO::FETCH_ASSOC);
                
                $rues[$key] = $street;
            }
            
            // On tri le tableau selon rue_nom
            Core::triMultidimentionnel($rues, 'rue_nom');
            
            return $rues;
        }
        
        // Sinon, on ne retourne rien
        else {
            return false;
        }
    }
    
	
	
	/**
	 * Récupère les statistiques de réalisation de la rue
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
     * 
     * @param   int          $id        Identifiant de la rue
     * @result  array                   Liste des rues à parcourir
     */
    
    public function statistique_rue($id) {
        // On récupère l'identifiant de la mission
        $mission = $this->get('mission_id');
        
        // On récupère tous les items à visiter dans la rue
        $query = $this->link->prepare('SELECT * FROM `items` WHERE `mission_id` = :mission AND `rue_id` = :rue');
        $query->bindParam(':mission', $mission);
        $query->bindParam(':rue', $id);
        $query->execute();
        
        if ($query->rowCount()) {
            $items = $query->fetchAll(PDO::FETCH_ASSOC);
            $stats = array(
                'fait' => 0,
                'attente' => 0
            );
            
            foreach ($items as $item) {
                // On regarde si ce n'est pas encore fait
                if ($item['item_statut'] == 0) {
                    $stats['attente']++;
                }
                
                else {
                    $stats['fait']++;
                }
            }
            
            // On calcule la réalisation
            $stats['total'] = array_sum($stats);
            $stats['proportion'] = ceil($stats['fait'] / $stats['total']);
            
            return $stats;
        }
        
        else {
            return false;
        }
    }
    
	
	/**
	 * Ajoute une rue à couvrir à la mission
	 *
	 * @author	Damien Senger	<mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	int		$rue		Identifiant de la rue à ajouter
	 * @param	int		$mission	Identifiant de la mission dans laquelle ajouter la rue
	 * @return	bool
	 */
	 
	public function ajoutRue( $rue ) {
		// On effectue une recherche de tous les immeubles contenus dans la rue demandée
		$query = $this->link->prepare('SELECT `immeuble_id` FROM `immeubles` WHERE `rue_id` = :rue');
		$query->bindParam(':rue', $rue, PDO::PARAM_INT);
		$query->execute();
		
		// S'il y a des immeubles
		if ($query->rowCount()) {
			// On récupère la liste des identifiants
			$immeubles = $query->fetchAll(PDO::FETCH_NUM);
			
			// Si la mission est un porte à porte, on cherche les électeurs concernés
			if ($this->data['mission_type'] == 'porte') {
				// Pour chaque immeuble, on recherche tous les électeurs
				foreach ($immeubles as $immeuble) {
					$query = $this->link->prepare('SELECT `contact_id` FROM `contacts` WHERE `immeuble_id` = :immeuble');
					$query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
					$query->execute();
					$contacts = $query->fetchAll(PDO::FETCH_NUM);
					
					// Pour chaque électeur, on créé une porte à frapper
					foreach ($contacts as $contact) {
						$query = $this->link->prepare('INSERT INTO `items` (`mission_id`, `rue_id`, `immeuble_id`, `contact_id`) VALUES (:mission, :rue, :immeuble, :contact)');
						$query->bindParam(':mission', $this->data['mission_id'], PDO::PARAM_INT);
						$query->bindParam(':rue', $rue, PDO::PARAM_INT);
						$query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
						$query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
						$query->execute();
					}
				}
			}
			
			// Si la mission est un boîtage, on enregistre les immeubles
			else {
				foreach ($immeubles as $immeuble) {
					$query = $this->link->prepare('INSERT INTO `boitage` (`mission_id`, `rue_id`, `immeuble_id`) VALUES (:mission, :rue, :immeuble)');
					$query->bindParam(':mission', $this->data['mission_id'], PDO::PARAM_INT);
					$query->bindParam(':rue', $rue, PDO::PARAM_INT);
					$query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
					$query->execute();
				}
			}	
		}
		
		// S'il n'y a pas d'immeubles
		else {
			return false;
		}
	}
	
	
	/**
	 * Cette méthode permet de reporter une action dans une mission
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param   int     $electeur       Identifiant de l'électeur concerné par le reporting (MD5)
	 * @param   int     $statut         Statut du reporting
	 * @result  void
	 */
	
	public function reporting( $electeur , $statut ) {
		// On récupère les informations sur la mission
		$userId = User::ID();
		$mission = $this->data;
		$type = $mission['mission_type'];
		
		// On prépare et on exécute déjà l'enregistrement du reporting de la porte
		if ($type == 'porte') {
			$query = $this->link->prepare('UPDATE `porte` SET `porte_statut` = :statut, `porte_date` = NOW(), `porte_militant` = :militant WHERE `mission_id` = :mission AND MD5(`contact_id`) = :element');
		} elseif ($type == 'boitage') {
			$query = $this->link->prepare('UPDATE `boitage` SET `boitage_statut` = :statut, `boitage_date` = NOW(), `boitage_militant` = :militant WHERE `mission_id` = :mission AND MD5(`immeuble_id`) = :element');
		}
		$query->bindParam(':statut', $statut);
		$query->bindParam(':militant', $userId, PDO::PARAM_INT);
		$query->bindParam(':mission', $mission['mission_id']);
		$query->bindParam(':element', $electeur);
		$query->execute();
		
		// S'il s'agit un porte à porte, on ajoute un événement pour le contact
		if ($type == 'porte') {
			// On recherche l'identifiant en clair du contact vu
			$query = $this->link->prepare('SELECT `contact_id` FROM `contacts` WHERE MD5(`contact_id`) = :contact');
			$query->bindParam(':contact', $electeur);
			$query->execute();
			$contact = $query->fetch(PDO::FETCH_NUM);
			
			// On prépare l'objet de l'historique
			$type_historique = array(
				'porte'   => 'Porte à porte',
				'boitage' => 'Boîtage'
			);
			
			$event_historique = array(
				 1 => 'Électeur absent',
				 2 => 'Électeur rencontré',
				 3 => 'Demande de procuration',
				 4 => 'Électeur à contacter',
			    -1 => 'Adresse incorrecte'
			);
			
			$objet_historique = $type_historique[$type] . ' – ' . $event_historique[$statut];
			
			// On rajoute une entrée d'historique pour le contact en question
			$query = $this->link->prepare('INSERT INTO `historique` (`contact_id`, `compte_id`, `historique_type`, `historique_date`, `historique_objet`, `campagne_id`) VALUES (:contact, :compte, "porte", NOW(), :objet, :campagne)');
			$query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
			$query->bindParam(':compte', $userId, PDO::PARAM_INT);
			$query->bindParam(':objet', $objet_historique);
			$query->bindParam(':campagne', $mission['mission_id'], PDO::PARAM_INT);
			$query->execute();
		}
		
		// s'il s'agit d'un boîtage et que l'immeuble a été fait, on fait un élément d'historique pour tous les habitants électeurs déclarés dans l'immeuble concerné
		elseif ($type == 'boitage' && $statut == 2) {
    		// On cherche tous les contacts qui habitent ou sont déclarés électoralement dans l'immeuble en question pour créer un élément d'historique
    		$query = $this->link->prepare('SELECT `contact_id` FROM `contacts` WHERE MD5(`immeuble_id`) = :immeuble OR MD5(`adresse_id`) = :immeuble');
    		$query->bindParam(':immeuble', $electeur);
    		$query->execute();
    		$contacts = $query->fetchAll(PDO::FETCH_NUM);
    		
    		// On fait la boucle de tous ces contacts pour leur ajouter l'élément d'historique
    		foreach ($contacts as $contact) {
	    		$query = $this->link->prepare('INSERT INTO `historique` (`contact_id`, `compte_id`, `historique_type`, `historique_date`, `historique_objet`, `campagne_id`) VALUES (:contact, :compte, "boite", NOW(), :mission, :campagne)');
	    		$query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
	    		$query->bindParam(':compte', $userId, PDO::PARAM_INT);
	    		$query->bindParam(':mission', $this->data['mission_nom']);
				$query->bindParam(':campagne', $mission['mission_id'], PDO::PARAM_INT);
	    		$query->execute();
    		}
		}
	}
	
	
	/**
	 * Calcul du nombre de procurations demandées, uniquement pour le porte à porte
	 * 
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @return  int    Nombre de procurations demandées
	 */
	
	public function nombre_procurations() {
		// On calcule le nombre de procurations demandées
		$query = $this->link->prepare('SELECT `item_id` FROM `items` WHERE `mission_id` = :mission AND `item_statut` = 3');
		$query->bindParam(':mission', $this->data['mission_id'], PDO::PARAM_INT);
		$query->execute();
		
		// On récupère le nombre demandé
		return $query->rowCount();
	}
	
	
	/**
	 * Calcul du nombre de procurations demandées, uniquement pour le porte à porte
	 * 
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @return  int    Nombre de procurations demandées
	 */
	
	public function nombre_recontacts() {
		// On calcule le nombre de recontacs demandés
		$query = $this->link->prepare('SELECT `item_id` FROM `items` WHERE `mission_id` = :mission AND `item_statut` = 4');
		$query->bindParam(':mission', $this->data['mission_id'], PDO::PARAM_INT);
		$query->execute();
		
		// On récupère le nombre demandé
		return $query->rowCount();
	}
	
	
	/**
	 * Liste les contacts selon le statut demandé pour la mission ouverte (pour le porte à porte)
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @param   int    $statut    Statut demandé pour la recherche
	 * @result  array             Identifiants des contacts concernés
	 */
	
	public function liste_contacts( $statut ) {
		// On réalise le tri
		$query = $this->link->prepare('SELECT `contact_id` FROM `items` WHERE `mission_id` = :mission AND `item_statut` = :statut ORDER BY `item_reporting_date` DESC');
		$query->bindParam(':mission', $this->data['mission_id'], PDO::PARAM_INT);
		$query->bindParam(':statut', $statut, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne le tableau des identifiants
		return $query->fetchAll(PDO::FETCH_NUM);
	}
	
	
	/**
	 * Calcule les contacts selon le statut demandé pour la mission ouverte (pour le porte à porte)
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @param   int    $statut    Statut demandé pour la recherche
	 * @result  array             Identifiants des contacts concernés
	 */
	
	public function nombre_contacts( $statut ) {
		// On réalise le tri
		$query = $this->link->prepare('SELECT `contact_id` FROM `porte` WHERE `mission_id` = :mission AND `porte_statut` = :statut ORDER BY `porte_date` DESC');
		$query->bindParam(':mission', $this->data['mission_id'], PDO::PARAM_INT);
		$query->bindParam(':statut', $statut, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne le tableau des identifiants
		return $query->rowCount();
	}
	
	
	/**
	 * Calcule les immeubles selon le statut demandé pour la mission ouverte (pour le boitage)
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @param   int    $statut    Statut demandé pour la recherche
	 * @result  array             Identifiants des contacts concernés
	 */
	
	public function nombre_immeubles( $statut ) {
		// On réalise le tri
		$query = $this->link->prepare('SELECT `boitage_id` FROM `boitage` WHERE `mission_id` = :mission AND `boitage_statut` = :statut ORDER BY `boitage_date` DESC');
		$query->bindParam(':mission', $this->data['mission_id'], PDO::PARAM_INT);
		$query->bindParam(':statut', $statut, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne le tableau des identifiants
		return $query->rowCount();
	}
	
	
	/**
     * Rend public une mission
	 * 
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @return  void
	 */
	
	public function ouvrir() {
    	// On enregistre l'ouverture
    	if ($this->set('mission_statut', 1)) {
        	return true;
    	} else {
        	return false;
    	}
	}
	
	
	/**
     * Rend privée une mission
	 * 
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @return  void
	 */
	
	public function fermer() {
    	// On enregistre l'ouverture
    	if ($this->set('mission_statut', 0)) {
        	return true;
    	} else {
        	return false;
    	}
	}
	
	
	/**
	 * Cloture une mission ouverte
	 * 
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @return  void
	 */
	
	public function cloture() {
		// On prépare la modification pour enregistrer la fermeture de la mission
		$query = $this->link->prepare('UPDATE `mission` SET `mission_statut` = 0 WHERE `mission_id` = :mission');
		$query->bindParam(':mission', $this->data['mission_id'], PDO::PARAM_INT);
		
		// On effectue la modification
		$query->execute();
	}
	
	
	/**
	 * Cette méthode permet d'obtenir un tableau des missions disponibles actuellement
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	0.1
	 * 
	 * @param   string  $type   Type de mission demandée (porte ou boitage)
	 * @return	int		        Tableau des missions disponibles
	 */
	 
	public static function missions($type) {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de récupération des missions
		$query = $link->query('SELECT * FROM `mission` WHERE `mission_type` = "' . $type . '" ORDER BY `mission_deadline` ASC');
		
		// On retourne la liste des missions s'il en existe
		if ($query->rowCount()) {
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		else {
			return 0;
		}
	}
	
	
	/**
	 * Cette méthode permet de créer une nouvelle mission de porte-à-porte
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param   string  $type       Type de mission demandée (porte ou boitage)
	 * @param	array	$infos		Tableau contenant l'ensemble des informations postées par l'utilisateur
	 * @return	int					Identifiant SQL de la nouvelle mission créée
	 */
	 
	public static function creation($type, array $infos ) {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		$userId = User::ID();
		
		// On retraite la date entrée
		if (!empty($infos['date'])) {
			$date = explode('/', $infos['date']);
			krsort($date);
			$date = implode('-', $date);
		} else {
			$date = null;
		}
	
		// On exécute la requête d'insertion dans la base de données
		$query = $link->prepare('INSERT INTO `mission` (`createur_id`, `responsable_id`, `mission_deadline`, `mission_nom`, `mission_type`) VALUES (:cookie, :responsable, :deadline, :nom, :type)');
		$query->bindParam(':cookie', $userId, PDO::PARAM_INT);
		$query->bindParam(':responsable', $infos['responsable'], PDO::PARAM_INT);
		$query->bindParam(':deadline', $date);
		$query->bindParam(':nom', $infos['nom']);
		$query->bindParam(':type', $type);
		$query->execute();
		
		// On affiche l'identifiant de la nouvelle mission
		return $link->lastInsertId();
	}
	
	
	/**
	 * Récupère les invitations d'un membre
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param   string      $type       Type de missions demandées
	 * @param   string      $id         ID du membre
	 * @return	array                   Invitations
	 */

    public static function invitations($type, $user) {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		
        // On récupère les invitations liées à l'utilisateur
        $query = $link->prepare('SELECT * FROM `inscriptions` WHERE `user_id` = :user AND `inscription_statut` = 0');
        $query->bindParam(':user', $user);
        $query->execute();
        
        // On regarde s'il existe des invitations
        if ($query->rowCount()) {
            $missions = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // On vérifie que les missions sont bien du bon type et ouvertes
            $missions_ouvertes = array();
            foreach ($missions as $mission) {
                $query = $link->prepare('SELECT * FROM `mission` WHERE `mission_type` = :type AND `mission_statut` = 1 AND `mission_id` = :mission');
                $query->bindParam(':type', $type);
                $query->bindParam(':mission', $mission['mission_id']);
                $query->execute();
                
                if ($query->rowCount()) {
                    $infos = $query->fetch(PDO::FETCH_ASSOC);
                    $missions_ouvertes[] = $infos['mission_id'];
                }
            }
            
            return $missions_ouvertes;
        } else {
            return false;
        }
    }
	
	
	/**
	 * Récupère les missions ouvertes d'un membre
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param   string      $type       Type de missions demandées
	 * @param   string      $id         ID du membre
	 * @return	array                   Missions ouvertes
	 */

    public static function missions_ouvertes($type, $user) {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		
        // On récupère les invitations liées à l'utilisateur
        $query = $link->prepare('SELECT * FROM `inscriptions` WHERE `user_id` = :user AND `inscription_statut` = 1');
        $query->bindParam(':user', $user);
        $query->execute();
        
        // On regarde s'il existe des invitations
        if ($query->rowCount()) {
            $missions = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // On vérifie que les missions sont bien du bon type et ouvertes
            $missions_ouvertes = array();
            foreach ($missions as $mission) {
                $query = $link->prepare('SELECT * FROM `mission` WHERE `mission_type` = :type AND `mission_statut` = 1 AND `mission_id` = :mission');
                $query->bindParam(':type', $type);
                $query->bindParam(':mission', $mission['mission_id']);
                $query->execute();
                
                if ($query->rowCount()) {
                    $infos = $query->fetch(PDO::FETCH_ASSOC);
                    $missions_ouvertes[] = $infos['mission_id'];
                }
            }
            
            return $missions_ouvertes;
        } else {
            return false;
        }
    }
	
}
?>