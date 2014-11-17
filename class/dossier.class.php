<?php

/**
 * La classe Dossier permet de créer un objet Dossier contenant toutes les opérations liées au dossier ouvert
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */

class Dossier
{
	
	/**
	 * @var	array	$contact    Propriété contenant un tableau d'informations relatives au dossier ouvert
	 * @var object	$link		Lien vers la base de données
	 */
	public $dossier;
	private $link;
	
	
	/**
	 * Constructeur de la classe Dossier
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
		$this->link = Configuration::read('db.link');
		
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
	 * Liste les tâches liées au dossier
	 *
	 * Cette méthode permet d'obtenir un tableau de toutes les tâches liées
	 * à un événement lui-même lié au dossier ouvert actuellement
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param   bool    $statut   Tâches terminées (1) ou non (0)
	 *
	 * @result  array   Liste des tâches avec leurs caractéristiques
	 */
	
	public function taches( $statut = 0 )
	{
		// On prépare la liste des tâches
		$taches = array();
		
		// On fait la liste des événements pour récupérer la liste des tâches
		// correspondantes à chaque événement
		$evenements = $this->evenements();
		
		// Pour chaque événement, on cherche les tâches
		foreach ($evenements as $evenement)
		{
			// On prépare la requête
			$query = $this->link->prepare('SELECT * FROM `taches` WHERE `historique_id` = :historique AND `tache_terminee` = :statut');
			$query->bindParam(':historique', $evenement['historique_id']);
			$query->bindParam(':statut', $statut);
			
			// On exécute la requête
			$query->execute();
			
			// On ajoute les informations à la table
			if ($query->rowCount())
			{
				$taches = array_merge($taches, $query->fetchAll(PDO::FETCH_ASSOC));
			}
		}
		
		// On retourne la liste des tâches
		return $taches;
	}
	
	
	/**
	 * Liste les fichiers liés au dossier
	 *
	 * Cette méthode permet d'obtenir un tableau de tous les fichiers liées
	 * à un événement lui-même lié au dossier ouvert actuellement
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param   bool    $statut   Tâches terminées (1) ou non (0)
	 *
	 * @result  array   Liste des tâches avec leurs caractéristiques
	 */
	
	public function fichiers(  )
	{
		// On prépare la liste des tâches
		$fichiers = array();
		
		// On fait la liste des événements pour récupérer la liste des tâches
		// correspondantes à chaque événement
		$evenements = $this->evenements();
		
		// Pour chaque événement, on cherche les tâches
		foreach ($evenements as $evenement)
		{
			// On prépare la requête
			$query = $this->link->prepare('SELECT * FROM `fichiers` WHERE `interaction_id` = :historique');
			$query->bindParam(':historique', $evenement['historique_id']);
			
			// On exécute la requête
			$query->execute();
			
			// On ajoute les informations à la table
			if ($query->rowCount())
			{
				$fichiers = array_merge($fichiers, $query->fetchAll(PDO::FETCH_ASSOC));
			}
		}
		
		// On retourne la liste des tâches
		return $fichiers;
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
		$link = Configuration::read('db.link');
		
		// On prépare la requête
		$query = $link->prepare('SELECT `dossier_id` FROM `dossiers` WHERE `dossier_statut` = :statut ORDER BY `dossier_nom` ASC');
		$query->bindParam(':statut', $statut, PDO::PARAM_INT);
		
		// On récupère les données
		$query->execute();
		$dossiers = $query->fetchAll(PDO::FETCH_ASSOC);
		
		// On retourne le tableau
		return $dossiers;
	}
	
	
	/**
	 * Liste l'intégralité des dossiers
	 *
	 * Cette méthode permet de récupérer un tableau des dossiers ouverts ou non (au choix)
	 * avec l'ensemble des informations associées
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @param   bool   $tous   True pour tous les dossiers, false pour une uniquement les dossiers ouverts
	 * 
	 * @result  array          Tableau de tous les dossiers
	 */
	
	public static function liste_complete($tous = false) {
		// On prépare le lien vers la BDD
		$link = Configuration::read('db.link');
		
		// On lance la recherche des dossiers selon le critère choisi
		if ($tous) {
			$query = $link->query('SELECT * FROM `dossiers` ORDER BY `dossier_nom` ASC');
		} else {
			$query = $link->query('SELECT * FROM `dossiers` WHERE `dossier_date_fermeture` IS NULL ORDER BY `dossier_nom` ASC');
		}
		
		// On retourne les informations
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
}
?>