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
		// On récupère le lien de connexion à la base de données
		$this->link = Configuration::read('db.link');
		
		// On récupère les informations sur la campagne
		$query = $this->link->prepare('SELECT *, MD5(`campagne_id`) AS `code` FROM `campagne` WHERE MD5(`campagne_id`) = :id');
		$query->bindParam(':id', $campagne);
		$query->execute();
		
		// On récupère les informations
		$this->campagne = $query->fetch(PDO::FETCH_ASSOC);
		
		// On récupère le nombre d'envois réalisés
		$query = $this->link->prepare('SELECT COUNT(*) AS `nombre` FROM `historique` WHERE MD5(`campagne_id`) = :id');
		$query->bindParam(':id', $campagne);
		$query->execute();
		$nombre = $query->fetch(PDO::FETCH_NUM);
		$this->campagne['nombre'] = $nombre[0];
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
}
?>