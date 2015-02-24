<?php
/**
 * Cette classe comprend l'ensemble des méthodes de traitement des des campagnes du SaaS LeQG
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */

class Campagne {
	
	/**
	 * @var	object  $link       Propriété contenant le lien vers la base de données de l'utilisateur
	 * @var	object  $core       Propriété contenant le lien vers la base de données générale
	 * @var	array   $campagne   Tableau contenant les informations sur la campagne
	 */
	private $link, $core, $campagne;
	

	/**
	 * Cette méthode permet la construction de la classe boîtage
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param   string   $campagne   Identifiant MD5 de la campagne
	 *
	 * @return	void
	 */
	 
	public function __construct( $campagne ) {
		// On récupère les informations sur la campagne demandée
		$query = Core::query('campagne-par-id');
		$query->bindParam(':campagne', $campagne, PDO::PARAM_INT);
		$query->execute();
		
		// On récupère les informations
        self::$campagne = $query->fetch(PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * Récupère une information
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param   string   $info   Information demandée
	 *
	 * @return	string           Information trouvée
	 */
	
	public function get( $info ) {
		return $this->campagne[ $info ];
	}
	
	
	/**
	 * Récupère des informations sur les contacts concernés
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @return	array           Contacts concernés
	 */
	 
	public function contacts() {
		// On liste les éléments d'historique concernés
		$query = $this->link->prepare('SELECT * FROM `historique` WHERE `campagne_id` = :id');
		$campagne = $this->get('campagne_id');
		$query->bindParam(':id', $campagne);
		$query->execute();
		$historique = $query->fetchAll(PDO::FETCH_ASSOC);
		
		// On fait le tableau des différents contacts concernés
		$contacts = array();
		foreach ($historique as $h) {
			$contacts[] = $h['contact_id'];
		}
		
		// On retraite le tableau des ids
		$ids = implode(',', $contacts);
		
		// On cherche tous les contacts concernés par l'envoi
		$query = $this->link->query('SELECT *, MD5(`contact_id`) AS `contact_md5` FROM `contacts` WHERE `contact_id` IN (' . $ids . ')');
		
		if ($query->rowCount()) {
			$contacts = $query->fetchAll(PDO::FETCH_ASSOC);
			
			// Pour chaque contact, on rajoute le nom de la ville et on traite le nom d'affichage
			foreach ($contacts as $key => $contact) {
				if ($contact['adresse_id']) {
					$ville = Carto::villeParImmeuble($contact['adresse_id']);
				}
				elseif ($contact['immeuble_id']) {
					$ville = Carto::villeParImmeuble($contact['immeuble_id']);
				}
				else {
					$ville = 'Ville inconnue';
				}
				
				$contacts[$key]['ville'] = $ville;
				
				$contacts[$key]['nom_affichage'] = mb_convert_case($contact['contact_nom'], MB_CASE_UPPER) . ' ' . mb_convert_case($contact['contact_nom_usage'], MB_CASE_UPPER) . ' ' . mb_convert_case($contact['contact_prenoms'], MB_CASE_TITLE);
			}
		} else {
			$contacts = array();
		}
		
		return $contacts;
	}
	
	
	/**
    	 * Création d'une nouvelle campagne vierge
    	 * 
    	 * @param   string  $type       Type de campagne
    	 * 
    	 * @return  int                 ID de la campagne créée
    	 * */
    
    public static function nouvelle($type)
    {
        // On récupère le lien vers la base de données
        $link = Configuration::read('db.link');
        
        // On récupère l'identifiant de l'utilisateur connecté
        $user = User::ID();
        
        // On effectue la création de la campagne
        $query = Core::query('campagne-creation');
        $query->bindParam(':type', $type);
        $query->bindParam(':user', $user, PDO::PARAM_INT);
        $query->execute();
        
        // On retourne l'identifiant de la campagne
        return $link->lastInsertId();
    }
	
	
	/**
	 * Créé une nouvelle campagne
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param   string   $type   Type de campagne à créer
	 * @param   array    $infos  Informations de création
	 *
	 * @return	void
	 */
	
	public static function creation($type, array $infos) {
		// On récupère le lien vers la base de données
		$link = Configuration::read('db.link');
		
		// On récupère certaines informations
		$createur = User::ID();
		
		// On effectue la création des informations
		$query = $link->prepare('INSERT INTO `campagne` (`campagne_type`, `campagne_titre`, `campagne_message`, `campagne_date`, `campagne_createur`) VALUES (:type, :titre, :message, NOW(), :createur)');
		$query->bindParam(':type', $type);
		$query->bindParam(':titre', $infos['titre']);
		$query->bindParam(':message', $infos['message']);
		$query->bindParam(':createur', $createur, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne l'identifiant de la campagne
		return $link->lastInsertId();
	}
	
	
	/**
	 * Liste les campagnes existantes
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param   string   $type   Type de campagne à créer
	 *
	 * @return	void
	 */
	
	public static function liste($type) {
		// On récupère le lien vers la base de données
		$link = Configuration::read('db.link');
		
		// On cherche la liste des campagnes du type demandé
		$query = $link->prepare('SELECT MD5(`campagne_id`) AS `code` FROM `campagne` WHERE `campagne_type` = :type ORDER BY `campagne_date` DESC');
		$query->bindParam(':type', $type);
		$query->execute();
		
		// On retourne la liste
		if ($query->rowCount()) {
			return $query->fetchAll(PDO::FETCH_ASSOC);
		} else {
			return array();
		}
	}
	
	
	/**
     * Crée un nouvel emvoi
     * 
     * @param   string  $objet      Objet de l'envoi
     * @param   string  $message    Message de l'envoi
     * @param   string  $type       Type de l'envoi
     * @return  int                 ID de l'envoi
     * */
    
    public static function envoi($objet, $message, $type)
    {
        // On récupère les données
        $user = User::ID();
        
        // On lance la création de l'envoi
        $link = Configuration::read('db.link');
        $query = $link->prepare('INSERT INTO `envois` (`compte_id`, `envoi_type`, `envoi_time`, `envoi_titre`, `envoi_texte`) VALUES (:compte, :type, NOW(), :titre, :texte)');
        $query->bindValue(':compte', $user, PDO::PARAM_INT);
        $query->bindValue(':type', $type);
        $query->bindValue(':titre', $objet);
        $query->bindValue(':texte', $message);
        $query->execute();
        
        return $link->lastInsertId();
    }
}
