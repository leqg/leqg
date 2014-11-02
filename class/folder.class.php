<?php

/**
 * La classe contact permet de créer un objet folder contenant toutes les opérations liées au dossier ouvert
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */

class Folder
{
	
	/**
	 * @var	array	$contact    Propriété contenant un tableau d'informations relatives au dossier ouvert
	 * @var object	$link		Lien vers la base de données
	 */
	public $dossier;
	private $link;
	
	
	/**
	 * Constructeur de la classe Folder
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string/array	  $dossier   Identifiant (hashage MD5) du dossier 
	 *                                   demandé ou tableau des arguments de création 
	 *                                   si c'est une création
	 * @param   bool          $creation  Créer le dossier si c'est un tableau
	 *
	 * @result	void
	 */
	 
	public function __construct($dossier, $creation = false)
	{
		// On commence par paramétrer les données PDO
		$dsn =  'mysql:host=' . Configuration::read('db.host') . ';dbname=' . Configuration::read('db.basename');
		$user = Configuration::read('db.user');
		$pass = Configuration::read('db.pass');

		$this->link = new PDO($dsn, $user, $pass);
		
		if ($creation)
		{
    		    // On prépare la requête
    		    $query = $this->link->prepare('INSERT INTO `dossiers` (`dossier_nom`, `dossier_description`, `dossier_date_ouverture`) VALUES (:nom, :desc, NOW())');
    		    $query->bindParam(':nom', $dossier['nom']);
    		    $query->bindParam(':desc', $dossier['desc']);
    		    
    		    // On exécute la création du dossier
    		    $query->execute();
    		    
    		    // On récupère l'identifiant du dossier
    		    $dossier = md5($this->link->lastInsertId());
    		    
    		    // On vide les informations de BDD
    		    unset($query);
		}
		
		// On récupère les informations sur le dossier
		$query = $this->link->prepare('SELECT * FROM `dossiers` WHERE MD5(`dossier_id`) = :id');
		$query->bindParam(':id', $dossier);
		$query->execute();
		$dossier = $query->fetch(PDO::FETCH_ASSOC);
		
		// On fabrique de MD5 du dossier
		$dossier['dossier_md5'] = md5($dossier['dossier_id']);
		
		// On déplace ces informations dans la propriété $dossier
		$this->dossier = $dossier;
    }
    
    
    /**
     * Récupère les données du dossier au format json()
     *
     * Cette méthode permet d'extraire toutes les données du dossier au format json
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     *
     * @return  string
     */
    
    public function json()
    {
        return json_encode($this->dossier);
    }
    
    
    /**
     * Récupère une information sur le dossier
     *
     * Cette méthode permet de récupérer une information sur le dossier ouvert
     * actuellement
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     *
     * @param   string   $info   Information à chercher
     *
     * @result  string           Information trouvée
     */
    
    public function get( $info )
    {
        return $this->dossier[$info];
    }
    
    
    /**
	 * Modifie une information dans la base de données
	 *
	 * Cette méthode permet de modifier dans la base de données une information demandée
	 * par une autre pour le dossier ouvert
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @param   string   $info   Information à modifier
	 * @param   string   $valeur Nouvelle valeur de l'information
	 * 
	 * @return void
	 */
	
	public function modifier( $info , $valeur )
	{
		// On prépare la requête de modification
		$query = $this->link->prepare('UPDATE `dossiers` SET `' . $info . '` = :valeur WHERE `dossier_id` = :id');
		$query->bindParam(':id', $this->dossier['dossier_id']);
		$query->bindParam(':valeur', $valeur);
		
		// On exécute la modification
		$query->execute();
	}
	
	
	/**
	 * Liste les événements liés au dossier
	 *
	 * Cette méthode permet de lister tous les événements liés à un contact
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @result  array  Liste des ID des événements
	 */
	
	public function evenements( )
	{
		// On prépare la requête
		$query = $this->link->prepare('SELECT `historique_id` FROM `historique` WHERE `dossier_id` = :id ORDER BY `historique_date` DESC');
		$query->bindParam(':id', $this->dossier['dossier_id']);
		
		// On exécute la recherche
		$query->execute();
		
		// On affecte la requête au tableau $evenements
		$evenements = $query->fetchAll(PDO::FETCH_ASSOC);
		
		// On retourne le tableau
		return $evenements;
	}
    
    
    /**
	 * Retour une liste des dossiers
	 *
	 * Cette méthode statique permet de retourner une liste complète des dossiers
	 * selon leur paramètre d'ouverture
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @param   bool   $statut   Statut des dossiers à récupérer
	 *
	 * @result  array            Tableau des dossiers trouvés
	 */
	 
	public static function liste( $statut = 1)
	{
		// On prépare le lien vers la BDD
		$dsn =  'mysql:host=' . Configuration::read('db.host') . ';dbname=' . Configuration::read('db.basename');
		$user = Configuration::read('db.user');
		$pass = Configuration::read('db.pass');
		$link = new PDO($dsn, $user, $pass);
		
		// On prépare la requête
		$query = $link->prepare('SELECT `dossier_id` FROM `dossiers` WHERE `dossier_statut` = :statut');
		$query->bindParam(':statut', $statut, PDO::PARAM_INT);
		
		// On récupère les données
		$query->execute();
		$dossiers = $query->fetchAll(PDO::FETCH_ASSOC);
		
		// On retourne le tableau
		return $dossiers;
	}
}
?>