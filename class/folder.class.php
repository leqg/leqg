<?php

/**
 * La classe contact permet de créer un objet folder contenant toutes les opérations liées au dossier ouvert
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */

class folder
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
}
?>