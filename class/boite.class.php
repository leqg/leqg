<?php
/**
 * Cette classe comprend l'ensemble des méthodes de traitement des boîtages sur le SaaS LeQG
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */

class Boite {
	
	/**
	 * @var	object  $db   Propriété contenant le lien vers la base de données de l'utilisateur
	 */
	private $link;
	

	/**
	 * Cette méthode permet la construction de la classe boîtage
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	object	$db			Lien vers la base de données de l'utilisateur
	 * @param	object	$base		Lien vers la base de données de l'utilisateur (mode PDO)
	 * @return	void
	 */
	 
	public	function __construct() {
		$this->link = Configuration::read('db.link');
	}

	
	/**
	 * Cette méthode permet de calculer le nombre de missions disponible actuellement
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @return	int		Nombre de missions disponibles
	 */
	 
	public static function nombre() {
		// On met en place le lien vers la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête
		$query = $link->query('SELECT COUNT(*) FROM `mission` WHERE `mission_statut` = 1 AND `mission_type` = "boitage" AND (`mission_deadline` IS NULL OR `mission_deadline` >= NOW())');
		$data = $query->fetch(PDO::FETCH_NUM);
		
		return $data[0];
	}
	
	
	/**
	 * Cette méthode permet d'obtenir un tableau des missions disponibles actuellement
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	0.1
	 *
	 * @return	int		Tableau des missions disponibles
	 */
	 
	public static function missions() {
		// On met en place le lien vers la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête
		$query = $link->query('SELECT * FROM `mission` WHERE `mission_statut` = 1 AND `mission_type` = "boitage" AND (`mission_deadline` IS NULL OR `mission_deadline` >= NOW())');
		
		// On retourne le résultat
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * Cette méthode permet de créer une nouvelle mission de boîtage
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	array	$infos		Tableau contenant l'ensemble des informations postées par l'utilisateur
	 * @return	int					Identifiant SQL de la nouvelle mission créée
	 */
	 
	public static function creation( array $infos ) {
		// On met en place le lien vers la base de données
		$link = Configuration::read('db.link');
		
		// On retraite la date entrée
		$date = explode('/', $infos['date']);
		ksort($date);
		$date = implode('-', $date);
	
		// On exécute la requête d'insertion dans la base de données
		$query = $link->prepare('INSERT INTO `mission` (`createur_id`, `responsable_id`, `mission_deadline`, `mission_nom`, `mission_type`) VALUES (:createur, :responsable, :deadline, :nom, "boitage")');
		$query->bindParam(':createur', User::ID(), PDO::PARAM_INT);
		$query->bindParam(':responsable', $infos['responsable'], PDO::PARAM_INT);
		$query->bindParam(':deadline', $date);
		$query->bindParam(':nom', $infos['nom']);
		$query->execute();
		
		// On retourne l'ID de la mission créée
		return $link->lastInsertId();
	}
	
	
	/**
	 * Cette méthode permet de vérifier si une mission de boîtage correspond bien à l'ID renvoyé
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string	$mission	Entrée dont la véracité est à contrôler
	 * @return	bool
	 */
	
	public static function verification( $mission ) {
		// On met en place le lien vers la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de vérification
		$query = $link->prepare('SELECT `mission_id` FROM `mission` WHERE MD5(`mission_id`) = :id AND `mission_type` = "boitage"');
		$query->bindParam(':id', $mission);
		$query->execute();
		
		// Si on trouve une entrée, c'est bon, sinon non
		if ($query->rowCount() == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * Cette méthode permet de récupérer toutes les informations concernant une mission de boîtage demandée
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string	$mission	Identifiant de la mission pour laquelle la récupération des informations est demandée
	 * @return	array				Tableau contenant l'ensemble des informations concernant la mission demandée
	 */
	
	public static function informations($mission) {
		// On met en place le lien vers la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de recherche des informations
		$query = $link->prepare('SELECT * FROM `mission` WHERE MD5( `mission_id` ) = :id');
		$query->bindParam(':id', $mission);
		$query->execute();

		// On retourne les informations sous forme d'un tableau
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * Cette méthode permet de connaître le nombre d'immeubles à réaliser dans une mission
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	int		$mission 	Identifiant de la mission
	 * @param	string	$type		La recherche doit-elle porter sur les immeubles fait (1), non-fait (0) ou tous (-1) 
	 * @result	int					Nombre d'immeubles correspondant à la recherche
	 */
	
	public static function nombreImmeubles( $mission , $type = 0 ) {
		// On met en place le lien vers la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête
		if ($type) {
			$query = $link->prepare('SELECT COUNT(*) FROM `boitage` WHERE `mission_id` = :id AND `boitage_statut > 0');
		} else {
			$query = $link->prepare('SELECT COUNT(*) FROM `boitage` WHERE `mission_id` = :id AND `boitage_statut = 0');
		}
		$query->bindParam(':id', $mission);
		$query->execute();
		$data = $query->fetch(PDO::FETCH_NUM);

		// On retourne le nombre d'immeubles trouvés
		return $data[0];
	}
	
	
	/**
	 * Cette méthode permet d'ajouter une rue entière dans une mission de boîtage
	 *
	 * @author	Damien Senger	<mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	int		$rue		Identifiant de la rue à ajouter
	 * @param	int		$mission	Identifiant de la mission dans laquelle ajouter la rue
	 * @return	bool
	 */
	 
	public static function ajoutRue( $rue , $mission ) {
		// On met en place le lien vers la base de données
		$link = Configuration::read('db.link');
		
		// On effectue une recherche de tous les immeubles contenus dans la rue
		$query = $link->prepera('SELECT `immeuble_id` FROM `immeubles WHERE `rue_id` = :id');
		$query->bindParam(':id', $rue, PDO::PARAM_INT);
		$query->execute();
		$immeubles = $query->fetchAll(PDO::FETCH_NUM);
		
		// Pour chaque immeuble, on créé une insertion dans la base de données
		foreach ($immeubles as $immeuble) {
			$query = $this->prepare('INSERT INTO `boitage` (`mission_id`, `rue_id`, `immeuble_id`) VALUES (:mission, :rue, :immeuble)');
			$query->bindParam(':mission', $mission, PDO::PARAM_INT);
			$query->bindParam(':rue', $rue, PDO::PARAM_INT);
			$query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
			$query->execute();
		}
	}
	
	
	/**
	 * Cette méthode permet d'ajouter un bureau entier dans une mission de boîtage
	 *
	 * @author	Damien Senger	<mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	int		$rue		Identifiant de la rue à ajouter
	 * @param	int		$mission	Identifiant de la mission dans laquelle ajouter la rue
	 * @return	bool
	 */
	 
	public static function ajoutBureau( $bureau , $mission ) {
		// On met en place le lien vers la base de données
		$link = Configuration::read('db.link');
		
		// On effectue une recherche de tous les immeubles contenus dans la rue
		$query = $link->prepare('SELECT `immeuble_id`, `rue_id` FROM `immeubles` WHERE `bureau_id` = :id');
		$query->bindParam(':id', $bureau, PDO::PARAM_INT);
		$query->execute();
		$immeubles = $query->fetchAll(PDO::FETCH_NUM);
		
		// Pour chaque immeuble, on créé une insertion dans la base de données
		foreach ($immeubles as $immeuble) {
			$query = $this->prepare('INSERT INTO `boitage` (`mission_id`, `rue_id`, `immeuble_id`) VALUES (:mission, :rue, :immeuble)');
			$query->bindParam(':mission', $mission, PDO::PARAM_INT);
			$query->bindParam(':rue', $immeuble[1], PDO::PARAM_INT);
			$query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
			$query->execute();
		}
	}
	
	
	/**
	 * Cette méthode permet de pouvoir obtenir une liste des immeubles à boiter par rue
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	int		$mission	Identifiant de la mission
	 * @param	int		$statut		Statut des immeubles recherchés (1 fait, 0 non fait)
	 * @return	array				Tableau contenant par rue l'ensemble des immeubles à couvrir
	 */
	 
	public static function liste( $mission , $statut = 0 ) {
		// On met en place le lien vers la base de données
		$link = Configuration::read('db.link');
		
		// On récupère tous les immeubles
		if ($statut) {
			$query = $link->prepare('SELECT `immeuble_id`, `rue_id` FROM `boitage` WHERE `mission_id` = :id AND `boitage_statut` > 0');
		} else {
			$query = $link->prepare('SELECT `immeuble_id`, `rue_id` FROM `boitage` WHERE `mission_id` = :id AND `boitage_statut` = 0');
		}
		$query->bindParam(':id', $mission, PDO::PARAM_INT);
		$query->execute();
		$immeubles = $query->fetchAll(PDO::FETCH_NUM);
		
		// On lance un tri par rue des immeubles
		$rues = array();
		foreach ($immeubles as $immeuble) { $rues[$immeuble[1]][] = $immeuble[0]; }
		
		// On retourne le tableau trié par rues
		return $rues;
	}
	
	
	/**
	 * Cette méthode permet d'estimer le nombre d'électeur concernés par un boitage
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$mission		Identifiant de la mission concernée par l'estimation
	 * @param	int		$type			Type d'électeurs à vérifier (1 : déjà boités, 0 : à boiter, 2 : tous)
	 *
	 * @return	int					Nombre d'électeur estimé
	 */
	
	public static function estimation( $mission , $type = 0 ) {
		// On met en place le lien vers la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de recherche des immeubles concernés par le comptage
		if ($type) {
			$query = $link->prepare('SELECT `immeuble_id` FROM `boitage` WHERE `mission_id` = :id AND `boitage_statut` > 0');
		} else {
			$query = $link->prepare('SELECT `immeuble_id` FROM `boitage` WHERE `mission_id` = :id AND `boitage_statut` = 0');
		}
		$query->bindParam(':id', $mission, PDO::PARAM_INT);
		$query->execute();
		$immeubles = $query->fetchAll(PDO::FETCH_NUM);
		
		// On retraite la liste des immeubles pour l'importer dans la requête SQL
		$ids = array();
		foreach ($immeubles as $immeuble) { $ids[] = $immeuble[0]; }
		$immeubles = implode(',', $ids);
		
		// On fait la recherche du nombre d'électeurs pour tous les immeubles demandés
		$query = $link->query('SELECT COUNT(*) FROM `contacts` WHERE `immeuble_id` IN (' . $immeubles . ')');
		$data = $query->fetch(PDO::FETCH_NUM);
		
		// On retourne le nombre d'électeurs
		return $data[0];
	}
	
	
	/**
	 * Cette méthode permet de reporter un boîtage
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$mission		Identifiant de la mission concernée par le reporting (MD5)
	 * @param	int		$immeuble		Identifiant de l'immeuble concerné par le reporting (MD5)
	 * @param	int		$statut			Statut du reporting : 2 pour fait, 1 pour inaccessible
	 * @result	void
	 */
	
	public static function reporting( $mission , $immeuble , $statut ) {
		// On met en place le lien vers la base de données
		$link = Configuration::read('db.link');
		
		// On récupère les informations sur la mission
		$informations = self::informations($mission);
		
		// On prépare et exécute la requête
		$query = $link->prepare('UPDATE `boitage` SET `boitage_statut` = :statut, `boitage_date` = NOW(), `boitage_militant` = :cookie WHERE MD5(`mission_id`) = :mission AND MD5(`immeuble_id`) = :immeuble');
		$query->bindParam(':statut', $statut);
		$query->bindParam(':cookie', User::ID(), PDO::PARAM_INT);
		$query->bindParam(':mission', $mission);
		$query->bindParam(':immeuble', $immeuble);
		$query->execute();
				
		// Si l'immeuble a été fait, on reporte le boitage pour tous les les contacts
		if ($statut == 2)
		{
    		// On cherche tous les contacts qui habitent ou sont déclarés électoralement dans l'immeuble en question pour créer un élément d'historique
    		$query = $link->prepare('SELECT `contact_id` FROM `contacts` WHERE MD5(`immeuble_id`) = :immeuble OR MD5(`adresse_id`) = :immeuble');
    		$query->bindParam(':immeuble', $immeuble);
    		$query->execute();
    		$contacts = $query->fetchAll(PDO::FETCH_NUM);
    		
    		// On fait la boucle de tous ces contacts pour leur ajouter l'élément d'historique
    		foreach ($contacts as $contact) {
	    		$query = $link->prepare('INSERT INTO `historique` (`contact_id`, `compte_id`, `historique_type`, `historique_date`, `historique_objet`) VALUES (:contact, :compte, "boite", NOW(), :mission)');
	    		$query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
	    		$query->bindParam(':compte', User::ID(), PDO::PARAM_INT);
	    		$query->bindParam(':mission', $informations['mission_nom']);
	    		$query->execute();
    		}
		}
	}
}