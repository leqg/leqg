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
	 */
	
	private $data, $link;
	
	
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
		
		if (!$query->rowCount()) exit;
		
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
						$query = $this->link->prepare('INSERT INTO `porte` (`mission_id`, `rue_id`, `immeuble_id`, `contact_id`) VALUES (:mission, :rue, :immeuble, :contact)');
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
		$query = $this->link->prepare('SELECT `porte_id` FROM `porte` WHERE `mission_id` = :mission AND `porte_statut` = 3');
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
		$query = $this->link->prepare('SELECT `porte_id` FROM `porte` WHERE `mission_id` = :mission AND `porte_statut` = 4');
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
		$query = $this->link->prepare('SELECT `contact_id` FROM `porte` WHERE `mission_id` = :mission AND `porte_statut` = :statut ORDER BY `porte_date` DESC');
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
		$query = $link->query('SELECT * FROM `mission` WHERE `mission_statut` = 1 AND `mission_type` = "' . $type . '" AND (`mission_deadline` IS NULL OR `mission_deadline` >= NOW()) ORDER BY `mission_deadline` ASC');
		
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
	
}
?>