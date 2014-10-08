<?php
/**
 * Cette classe comprend l'ensemble des méthodes de traitement des boîtages sur le SaaS LeQG
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */

class boitage extends core {
	
	/**
	 * @var	object	$db			Propriété concenant le lien vers la base de données de l'utilisateur
	 */
	private $db;
	

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
	 
	public	function __construct($db) {
		$this->db = $db;
	}

	
	/**
	 * Cette méthode permet de calculer le nombre de missions disponible actuellement
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @return	int		Nombre de missions disponibles
	 */
	 
	public	function nombre() {
		// On prépare la requête
		$query = 'SELECT	*
				  FROM		`mission`
				  WHERE		`mission_statut` = 1
				  AND		( `mission_deadline` IS NULL OR `mission_deadline` >= NOW() )';
				  
		// On effectue la requête et on retourne le nombre de lignes trouvées
		$sql = $this->db->query($query);
		
		return $sql->num_rows;
	}
	
	
	/**
	 * Cette méthode permet d'obtenir un tableau des missions disponibles actuellement
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	0.1
	 *
	 * @return	int		Tableau des missions disponibles
	 */
	 
	public	function missions() {
		// On prépare la requête
		$query = 'SELECT	*
				  FROM		`mission`
				  WHERE		`mission_statut` = 1
				  AND		( `mission_deadline` IS NULL OR `mission_deadline` >= NOW() )';
				  
		// On effectue la requête et on retourne le nombre de lignes trouvées
		$sql = $this->db->query($query);
		
		$missions = array();
		while($row = $sql->fetch_assoc()) $missions[] = $row;
		
		return $missions;
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
	 
	public	function creation( array $infos ) {
		// On retraite la date entrée
		$date = explode('/', $infos['date']);
		ksort($date);
		$date = implode('-', $date);
	
		// On prépare la requête d'insertion dans la base de données
		$query = 'INSERT INTO `mission` ( `createur_id`, `responsable_id`, `mission_deadline`, `mission_nom`, `mission_type` )
				  VALUES ( ' . $_COOKIE['leqg-user'] . ', ' . $infos['responsable'] . ', "' . date('Y-m-d', strtotime($date)) . '", "' . $this->securisation_string($infos['nom']) . '", "boitage" )';
		
		// On lance la requête et on retourne l'identifiant de la nouvelle mission
		$this->db->query($query);
		
		return $this->db->insert_id;
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
	
	public	function verification( $mission ) {
		// On exécute la requête de vérification
		$sql = $this->db->query('SELECT * FROM `mission` WHERE MD5( `mission_id` ) = "' . $mission . '" AND `mission_type` = "boitage"');
		
		// S'il existe un résultat, on valide la vérification, sinon non
		if ($sql->num_rows == 1) {
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
	
	public	function informations($mission) {
		// On exécute la requête de recherche des informations
		$sql = $this->db->query('SELECT * FROM `mission` WHERE MD5( `mission_id` ) = "' . $mission . '"');

		// On retourne les informations sous forme d'un tableau
		return $sql->fetch_assoc();
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
	
	public	function nombreImmeubles( $mission , $type = 0 ) {
		// On prépare la requête
		$query = 'SELECT * FROM `boitage` WHERE `mission_id` = ' . $mission;
		if ($type == 1) { $query .= ' AND `boitage_statut` > 0'; }
		if ($type == 0) { $query .= ' AND `boitage_statut` = 0'; }

		// On exécute la requête
		$sql = $this->db->query($query);
		
		// On retourne le nombre d'immeubles trouvés
		return $sql->num_rows;
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
	 
	public	function ajoutRue( $rue , $mission ) {
		// On effectue une recherche de tous les immeubles contenus dans la rue
		$immeubles = array();
		$query = 'SELECT * FROM `immeubles` WHERE `rue_id` = ' . $rue;
		$sql = $this->db->query($query);
		while ($row = $sql->fetch_assoc()) $immeubles[] = $row;
		
		// Pour chaque immeuble, on créé une insertion dans la base de données
		foreach ($immeubles as $immeuble) {
			$query = 'INSERT INTO `boitage` (`mission_id`, `rue_id`, `immeuble_id`) VALUES (' . $mission . ', ' . $rue . ', ' . $immeuble['immeuble_id'] . ')';
			$this->db->query($query);
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
	 
	public	function ajoutBureau( $bureau , $mission ) {
		// On effectue une recherche de tous les immeubles contenus dans la rue
		$immeubles = array();
		$query = 'SELECT * FROM `immeubles` WHERE `bureau_id` = ' . $bureau;
		$sql = $this->db->query($query);
		while ($row = $sql->fetch_assoc()) $immeubles[] = $row;
		
		// Pour chaque immeuble, on créé une insertion dans la base de données
		foreach ($immeubles as $immeuble) {
			$query = 'INSERT INTO `boitage` (`mission_id`, `rue_id`, `immeuble_id`) VALUES (' . $mission . ', ' . $immeuble['rue_id'] . ', ' . $immeuble['immeuble_id'] . ')';
			$this->db->query($query);
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
	 
	public	function liste( $mission , $statut = 0 ) {
		$rues = array();
		$immeubles = array();
		
		// On récupère tous les immeubles 
		$query = 'SELECT * FROM `boitage` WHERE `mission_id` = ' . $mission;
		if ($statut == 0) $query .= ' AND `boitage_statut` = 0';
		if ($statut == 1) $query .= ' AND `boitage_statut > 0';
		$sql = $this->db->query($query);
		while ($row = $sql->fetch_assoc()) { $immeubles[] = $row; }
		
		// On lance le tri par rues des immeubles
		foreach ($immeubles as $immeuble) {
			$rues[$immeuble['rue_id']][] = $immeuble['immeuble_id'];
		}
		
		// On retourne le tableau trié
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
	
	public	function estimation( $mission , $type = 0 ) {
		// On prépare la requête de recherche des immeubles concernés par le comptage
		$query = 'SELECT 	*
				  FROM		`boitage`
				  WHERE		`mission_id` = ' . $mission;
		
		if ($type == 1) { $query .= ' AND `boitage_statut` > 0'; }
		if ($type == 0) { $query .= ' AND `boitage_statut` = 0'; }
		
		$sql = $this->db->query($query);
		$immeubles = array();
		while ($row = $sql->fetch_assoc()) $immeubles[] = $row['immeuble_id'];
		
		// On fait la recherche du nombre d'électeurs pour tous les immeubles demandés
		$query = 'SELECT 	COUNT(*) AS `nombre`
				  FROM		`contacts`
				  WHERE		`immeuble_id` = ' . implode(' OR `immeuble_id` = ', $immeubles); $core->debug($query);
		$sql = $this->db->query($query);
		
		if ($sql) {
			$sql = $sql->fetch_assoc();
			
			// On retourne l'estimation
			return number_format($sql['nombre'], 0, ',', ' ');
		} else {
			// On retourne zéro
			return 0;
		}
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
	
	public	function reporting( $mission , $immeuble , $statut ) {
		// On récupère les informations sur la mission
		$informations = $this->informations($mission);
		
		// On prépare et exécute la requête
		$query = 'UPDATE `boitage` SET `boitage_statut` = ' . $statut . ', `boitage_date` = NOW(), `boitage_militant` = "' . $_COOKIE['leqg-user'] . '" WHERE MD5(`mission_id`) = "' . $mission . '" AND MD5(`immeuble_id`) = "' . $immeuble . '"';
		$this->db->query($query);
		
		// On cherche tous les contacts qui habitent ou sont déclarés électoralement dans l'immeuble en question pour créer un élément d'historique
		$query = 'SELECT * FROM `contacts` WHERE MD5(`immeuble_id`) = "' . $immeuble . '" OR MD5(`adresse_id`) = "' . $immeuble . '"';
		$sql = $this->db->query($query);
		$contacts = array();
		while ($row = $sql->fetch_assoc()) $contacts[] = $row;
		
		// On fait la boucle de tous ces contacts pour ajouter l'élément d'histoire
		foreach ($contacts as $contact) {
			$query = 'INSERT INTO	`historique` (`contact_id`, 
												  `compte_id`, 
												  `historique_type`, 
												  `historique_date`, 
												  `historique_objet`)
					  VALUES					 (' . $contact['contact_id'] . ',
					  							  ' . $_COOKIE['leqg-user'] . ',
					  							  "boite",
					  							  NOW(),
					  							  "' . $informations['mission_nom'] . '")';
			$this->db->query($query);
		}
	}
}