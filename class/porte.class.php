<?php
/**
 * Cette classe comprend l'ensemble des méthodes de traitement des porte-à-porte sur le SaaS LeQG
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */

class Porte {
	
	/**
	 * Cette méthode permet de calculer le nombre de missions disponible actuellement
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @return	int		Nombre de missions disponibles
	 */
	 
	public static function nombre() {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de calcul du nombre de missions
		$query = $link->query('SELECT COUNT(*) AS nombre FROM `mission` WHERE `mission_statut` = 1 AND `mission_type` = "porte" AND (`mission_deadline` IS NULL OR `mission_deadline` >= NOW())');
		$data = $query->fetch(PDO::FETCH_NUM);
		
		// On retourne le nombre retrouvé
		return $data[0];
	}
	
	
	/**
	 * Vérifie si l'utilisateur est inscrit ou non dans une mission
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param   int    $mission   ID de la mission
	 *
	 * @return	bool
	 */
	 
	public static function estInscrit( $mission ) {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		$userId = User::ID();
		
		// On exécute la requête de calcul du nombre de missions
		$query = $link->prepare('SELECT * FROM `inscriptions` WHERE `mission_id` = :mission AND `user_id` = :user');
		$query->bindParam(':mission', $mission, PDO::PARAM_INT);
		$query->bindParam(':user', $userId, PDO::PARAM_INT);
		$query->execute();
		
		// On affiche un booléen
		if ($query->rowCount()) {
			return true;
		} else {
			return false;
		}
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
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de récupération des missions
		$query = $link->query('SELECT * FROM `mission` WHERE `mission_statut` = 1 AND `mission_type` = "porte" AND (`mission_deadline` IS NULL OR `mission_deadline` >= NOW()) ORDER BY `mission_deadline` ASC');
		
		// On retourne le tableau récupéré
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * Cette méthode permet de créer une nouvelle mission de porte-à-porte
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	array	$infos		Tableau contenant l'ensemble des informations postées par l'utilisateur
	 * @return	int					Identifiant SQL de la nouvelle mission créée
	 */
	 
	public static function creation( array $infos ) {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		$userId = User::ID();
		
		// On retraite la date entrée
		if (!empty($infos['date'])) {
			$date = explode('/', $infos['date']);
			ksort($date);
			$date = implode('-', $date);
		} else {
			$date = null;
		}
	
		// On exécute la requête d'insertion dans la base de données
		$query = $link->prepare('INSERT INTO `mission` (`createur_id`, `responsable_id`, `mission_deadline`, `mission_nom`, `mission_type`) VALUES (:cookie, :responsable, :deadline, :nom, "porte")');
		$query->bindParam(':cookie', $userId, PDO::PARAM_INT);
		$query->bindParam(':responsable', $infos['responsable'], PDO::PARAM_INT);
		$query->bindParam(':deadline', $date);
		$query->bindParam(':nom', $infos['nom']);
		$query->execute();
		
		// On affiche l'identifiant de la nouvelle mission
		return $link->lastInsertId();
	}
	
	
	/**
	 * Cette méthode permet de vérifier si une mission de porte-à-porte correspond bien à l'ID renvoyé
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string	$mission	Entrée dont la véracité est à contrôler
	 * @return	bool
	 */
	
	public static function verification( $mission ) {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de vérification
		$query = $link->prepare('SELECT `mission_id` FROM `mission` WHERE MD5( `mission_id` ) = :id AND `mission_type` = "porte"');
		$query->bindParam(':id', $mission);
		$query->execute();
		
		return ($query->rowCount()) ? true : false;
	}
	
	
	/**
	 * Cette méthode permet de récupérer toutes les informations concernant une mission de porte-à-porte demandée
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string	$mission	Identifiant de la mission pour laquelle la récupération des informations est demandée
	 * @return	array				Tableau contenant l'ensemble des informations concernant la mission demandée
	 */
	
	public static function informations($mission) {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de recherche des informations
		$query = $link->prepare('SELECT * FROM `mission` WHERE MD5(`mission_id`) = :id');
		$query->bindParam(':id', $mission);
		$query->execute();
		
		// On retourne les informations sous forme d'un tableau
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * Cette méthode permet de connaître le nombre d'électeurs à visiter dans une mission
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	int		$mission 	Identifiant de la mission
	 * @param	string	$type		La recherche doit-elle porter sur les électeurs fait (1), non-fait (0) ou tous (-1) 
	 * @result	int					Nombre d'immeubles correspondant à la recherche
	 */
	
	public static function nombreVisites( $mission , $type = 0 ) {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête
		if ($type) {
			$query = $link->prepare('SELECT COUNT(*) FROM `porte` WHERE `mission_id` = :mission AND `porte_statut` > 0');
		} else {
			$query = $link->prepare('SELECT COUNT(*) FROM `porte` WHERE `mission_id` = :mission AND `porte_statut` = 0');
		}
		$query->bindParam(':mission', $mission, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch(PDO::FETCH_NUM);
		
		// On retourne le nombre de visites trouvés
		return $data[0];
	}
	
	
	/**
	 * Cette méthode permet d'ajouter une rue entière dans une mission de porte-à-porte
	 *
	 * @author	Damien Senger	<mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	int		$rue		Identifiant de la rue à ajouter
	 * @param	int		$mission	Identifiant de la mission dans laquelle ajouter la rue
	 * @return	bool
	 */
	 
	public static function ajoutRue( $rue , $mission ) {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On effectue une recherche de tous les immeubles contenus dans la rue
		$query = $link->prepare('SELECT `immeuble_id` FROM `immeubles` WHERE `rue_id` = :id');
		$query->bindParam(':id', $rue, PDO::PARAM_INT);
		$query->execute();
		$immeubles = $query->fetchAll(PDO::FETCH_NUM);
		
		// Pour chaque immeuble, on cherche tous les électeurs de l'immeuble
		foreach ($immeubles as $immeuble) {
			$query = $link->prepare('SELECT `contact_id` FROM `contacts` WHERE `immeuble_id` = :immeuble OR `adresse_id` = :immeuble');
			$query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
			$query->execute();
			$contacts = $query->fetchAll(PDO::FETCH_NUM);
			
			// Pour chaque électeur, on créé une porte à frapper
			foreach ($contacts as $contact) {
				$query = $link->prepare('INSERT INTO `porte` (`mission_id`, `rue_id`, `immeuble_id`, `contact_id`) VALUES (:mission, :rue, :immeuble, :contact)');
				$query->bindParam(':mission', $mission, PDO::PARAM_INT);
				$query->bindParam(':rue', $rue, PDO::PARAM_INT);
				$query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
				$query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
				$query->execute();
			}
		}
	}
	
	
	/**
	 * Cette méthode permet d'ajouter un bureau entier dans une mission de porte-à-porte
	 *
	 * @author	Damien Senger	<mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	int		$bureau		Identifiant de la rue à ajouter
	 * @param	int		$mission	Identifiant de la mission dans laquelle ajouter la rue
	 * @return	bool
	 */
	 
	public static function ajoutBureau( $bureau , $mission ) {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On effectue une recherche de tous les immeubles contenus dans la rue
		$query = $link->prepare('SELECT `immeuble_id`, `rue_id` FROM `immeubles` WHERE `bureau_id` = :id');
		$query->bindParam(':id', $bureau, PDO::PARAM_INT);
		$query->execute();
		$immeubles = $query->fetchAll(PDO::FETCH_NUM);
		
		// Pour chaque immeuble, on cherche tous les contacts pour ajouter pour chacun une entrée dans la base porte à frapper
		foreach ($immeubles as $immeuble) {
			$query = $link->prepare('SELECT `contact_id` FROM `contacts` WHERE `immeuble_id` = :immeuble OR `adresse_id` = :immeuble');
			$query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
			$query->execute();
			$contacts = $query->fetchAll(PDO::FETCH_NUM);
			
			// Pour chaque électeur, on créé une porte à frapper
			foreach ($contacts as $contact) {
				$query = $link->prepare('INSERT INTO `porte` (`mission_id`, `rue_id`, `immeuble_id`, `contact_id`) VALUES (:mission, :rue, :immeuble, :contact)');
				$query->bindParam(':mission', $mission, PDO::PARAM_INT);
				$query->bindParam(':rue', $immeuble[1], PDO::PARAM_INT);
				$query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
				$query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
				$query->execute();
			}
		}
	}
	
	
	/**
	 * Cette méthode permet de pouvoir obtenir une liste des immeubles à visiter par rue
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	int		$mission	Identifiant de la mission
	 * @param	int		$statut		Statut des immeubles recherchés (1 fait, 0 non fait)
	 * @return	array				Tableau contenant par rue l'ensemble des immeubles à couvrir
	 */
	 
	public static function liste( $mission , $statut = 0 ) {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On récupère la liste de toutes les portes
		$query = $link->prepare('SELECT `immeuble_id`, `rue_id` FROM `porte` WHERE `mission_id` = :id');
		$query->bindParam(':id', $mission, PDO::PARAM_INT);
		$query->execute();
		$portes = $query->fetchAll(PDO::FETCH_NUM);
		
		// On lance le tri par immeuble des portes à frapper
		$immeubles = array();
		foreach ($portes as $porte) {
			if (!array_key_exists($porte[0], $immeubles)) {
				$immeubles[$porte[0]] = array(
					'immeuble_id' => $porte[0],
					'rue_id' => $porte[1]
				);
			}
		}
		
		// On lance le tri par rues des immeubles
		$rues = array();
		foreach ($immeubles as $immeuble) {
			$rues[$immeuble['rue_id']][] = $immeuble['immeuble_id'];
		}
		
		// On retourne le tableau trié
		return $rues;
	}
	
	
	/**
	 * Cette méthode permett de charger les électeurs d'une mission à visiter liés à un immeuble
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @param	int		$mission		Identifiant de la mission concernée par l'estimation (md5)
	 * @param 	int		$immeuble		Identifiant de l'immeuble dont nous souhaitons extraire les électeurs (md5)
	 * 
	 * @return	array					Tableau des électeurs de l'immeuble demandé
	 */
	
	public static function electeurs( $mission , $immeuble ) {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
	
		// On récupère la liste des portes à frapper dans l'immeuble demandé
		$query = $link->prepare('SELECT * FROM `porte` WHERE MD5(`mission_id`) = :mission AND MD5(`immeuble_id`) = :immeuble AND `porte_statut` = 0');
		$query->bindParam(':mission', $mission);
		$query->bindParam(':immeuble', $immeuble);
		$query->execute();
		$portes = $query->fetchAll(PDO::FETCH_ASSOC);
		
		// Pour chaque porte, on cherche les informations du contact
		foreach ($portes as $key => $porte) {
			$query = $link->prepare('SELECT * FROM `contacts` WHERE `contact_id` = :contact');
			$query->bindParam(':contact', $porte['contact_id']);
			$query->execute();
			$contact = $query->fetch(PDO::FETCH_ASSOC);
			
			$portes[$key] = array_merge($portes[$key], $contact);
		}
		
		// On trie le tableau des électeurs par nom
		Core::triMultidimentionnel($portes, 'contact_nom', SORT_ASC);
		
		// On retourne le tableau trié
		return $portes;
	}
	
	
	/**
	 * Cette méthode permet d'estimer le nombre d'électeur concernés par un porte à porte
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
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
	
		// On prépare la requête de recherche des immeubles concernés par le comptage
		if ($type) {
			$query = $link->prepare('SELECT COUNT(*) FROM `porte` WHERE `mission_id` = :mission AND `porte_statut` > 0');
		} else {
			$query = $link->prepare('SELECT COUNT(*) FROM `porte` WHERE `mission_id` = :mission AND `porte_statut` = 0');
		}
		$query->bindParam(':mission', $mission, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch(PDO::FETCH_NUM);
		
		// On retourne le nombre de portes cherchées
		return $data[0];
	}
	
	
	/**
	 * Cette méthode permet de reporter un porte-à-porte
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$mission		Identifiant de la mission concernée par le reporting (MD5)
	 * @param	int		$electeur		Identifiant de l'électeur concerné par le reporting (MD5)
	 * @param	int		$statut			Statut du reporting : 2 pour fait, 1 pour inaccessible
	 * @result	void
	 */
	
	public static function reporting( $mission , $electeur , $statut ) {
		// On récupère la connexion à la base de données
		$link = Configuration::read('db.link');
		$userId = User::ID();
	
		// On récupère les informations sur la mission
		$informations = self::informations($mission);
		
		// On prépare et exécute la requête
		$query = $link->prepare('UPDATE `porte` SET `porte_statut` = :statut, `porte_date` = NOW(), `porte_militant` = :militant WHERE MD5(`mission_id`) = :mission AND MD5(`contact_id`) = :contact');
		$query->bindParam(':statut', $statut);
		$query->bindParam(':militant', $userId, PDO::PARAM_INT);
		$query->bindParam(':mission', $mission);
		$query->bindParam(':contact', $electeur);
		$query->execute();
		
		// On recherche l'identifiant en clair du contact vu
		$query = $link->prepare('SELECT `contact_id` FROM `contacts` WHERE MD5(`contact_id`) = :contact');
		$query->bindParam(':contact', $electeur);
		$query->execute();
		$contact = $query->fetch(PDO::FETCH_NUM);
		
		// On rajoute une entrée d'historique pour le contact en question
		$query = $link->prepare('INSERT INTO `historique` (`contact_id`, `compte_id`, `historique_type`, `historique_date`, `historique_objet`) VALUES (:contact, :compte, "porte", NOW(), :nom)');
		$query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
		$query->bindParam(':compte', $userId, PDO::PARAM_INT);
		$query->bindParam(':nom', $informations['mission_nom']);
		$query->execute();
	}
}
?>