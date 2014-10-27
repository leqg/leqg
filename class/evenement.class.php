<?php

/**
 * La classe événement permet de gérer l'ensemble des informations liées
 * à des événements d'histoire liés à des contacts donnés.
 *
 * Cette classe comprend l'ensemble des propriétés et méthodes disponibles pour
 * un événement demandé, ainsi que des méthodes statiques liées à la recherche
 * ou au listage des événements liés à une fiche, un utilisateur ou un compte.
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 * @version		0.1
 */

class evenement
{
	/**
	 * @var	array	$evenement	Tableau des informations connues sur l'événement
	 *							ouvert lors de la construction de la classe
	 * @var object	$link		Lien vers la base de données
	 */
	private $evenement;
	public $link;
	
	
	/**
	 * Constructeur de la classe Contact
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string	$evenement	Identifiant de l'événement demandé (en cas de
	 *								création, il s'agit de l'ID du contact lié)
	 * @param	bool	$securite	Permet de savoir si l'idenfiant entré est
	 * 								hashé par MD5 ou non
	 * @param	bool	$creation	La méthode doit-elle créer un nouvel événement
	 *								où est-ce un événement existant (true = création)
	 *
	 * @result	void
	 */
	 
	public function __construct( $evenement, $securite = true, $creation = false )
	{
		// On commence par paramétrer les données PDO
		$dsn =  'mysql:host=' . Configuration::read('db.host') . 
				';dbname=' . Configuration::read('db.basename');
		$user = Configuration::read('db.user');
		$pass = Configuration::read('db.pass');

		$this->link = new PDO($dsn, $user, $pass);
		
		// On regarde si on doit créer un nouvel événement, ou s'il s'agit d'un événement à ouvrir
		if ($creation === true)
		{
			// On prépare les variables
			if (isset($_COOKIE['leqg-user'])) { $user = $_COOKIE['leqg-user']; } else { $user = 0; }
			
			// On prépare la requête
			$query = $this->link->prepare('INSERT INTO `historique` (`contact_id`, `compte_id`, `historique_type`, `historique_date`) VALUES (' . $evenement . ', ' . $user . ', "autre", NOW())');
			
			// On exécute la requête
			$query->execute();
			
			// On récupère l'identifiant de l'événement créé
			$identifiant = $this->link->lastInsertId();
			
			// On effectue une recherche des informations liées à cet enregistrement
			$query = $this->link->prepare('SELECT * FROM `historique` WHERE `historique_id` = :evenement');
			$query->bindParam(':evenement', $identifiant);
			$query->execute();
			$evenements = $query->fetchAll();
			$evenement = $evenements[0];
			
			// On retourne le tout dans la propriété privée evenement
			$this->evenement = $evenement;
			
		}
		// On ouvre, dans ce cas, l'événement demandé
		else
		{
			// On cherche maintenant à savoir s'il existe un contact ayant pour identifiant celui demandé
			if ($securite === true)
			{
				$query = $this->link->prepare('SELECT * FROM `historique` WHERE MD5(`historique_id`) = :evenement');
			}
			else
			{
				$query = $this->link->prepare('SELECT * FROM `historique` WHERE `historique_id` = :evenement');
			}
			$query->bindParam(':evenement', $evenement);
			$query->execute();
			$evenements = $query->fetchAll();
			
			// Si on ne trouve pas d'utilisateur, on retourne vers la page d'accueil du module contact
			if (!count($evenements))
			{
				Core::tpl_go_to('contacts', true);
			}
			// Sinon, on affecte les données aux propriétés de l'objet
			else
			{
				// On commence par retraiter la date de l'événement pour l'avoir en format compréhensible
				$evenement = $evenements[0];
				$evenement['historique_date_fr'] = date('d/m/Y', strtotime($evenement['historique_date']));
				
				// On retourne le tout dans la propriété evenement
				$this->evenement = $evenement;
			}
		}
	}
	
	
	/**
	 * Retourne en JSON les informations liées au tableau
	 *
	 * Cette méthode retourne toutes les informations connues sur l'événement
	 * ouvert actuellement dans un format JSON, retraitées pour éviter les 
	 * entitées HTML (&...)
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @return  string
	 */
	
	public function json_infos()
	{
		// On prépare la fonction de suppression des entités HTML
		function rendu($string)
		{
			return html_entity_decode($string, ENT_QUOTES);
		}
		
		// On applique la fonction au tableau des informations liées à l'événement
		$event = array_map("rendu", $this->evenement);
		
		// On retourne le tableau retraité des informations en JSON
		return json_encode($event);
	}
	
	
	/**
	 * Détermine si un événement donné fait l'objet d'une fiche ou non
	 *
	 * Cette méthode retourne un booléen pour déterminer si un événement peut 
	 * faire l'objet d'un lien vers sa fiche détaillée ou s'il s'agit d'un
	 * élément d'historique ne pouvant faire l'objet d'un affichage détaillé
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @return bool
	 */
	
	public function lien(  )
	{
		// On détermine la liste des types possédant une fiche détaillée
		$type = array('contact', 'telephone', 'email', 'courrier', 'autre');
		
		// On renvoit un booléen selon la présence ou non dans le tableau des types ouvrables
		if (in_array($this->get_infos('type'), $type))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * Retourne une information connue
	 *
	 * Cette méthode permet de récupérer les informations connues sur l'événement
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	array	$infos	Information demandée
	 * @return	mixed
	 */
	
	public function get_infos( $infos )
	{
		return $this->evenement['historique_' . $infos];
	}
	
	
	/**
	 * Modifie les données dans la base de données
	 *
	 * Cette méthode permet de mettre à jour les informations de la base de données
	 * concernant un champ de l'événément
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	string	$info	Information à modifier
	 * @param	string	$value	Valeur à enregistrer
	 * @return	bool			Réussite ou non de l'opération
	 */
	 
	public function modification( $info , $value )
	{
		// On retraite la valeur s'il s'agit d'une demande de modification de la date
		if ($info == 'historique_date')
		{
			$value = explode('/', $value);
			$value = $value[2] . '-' . $value[1] . '-' . $value[0];
			
		}
		// Sinon on retraite les caractères spéciaux
		else
		{
			$value = Core::securisation_string($value);
		}
		
		// On prépare la requête
		$query = $this->link->prepare('UPDATE `historique` SET `'. $info . '` = "' . $value . '" WHERE `historique_id` = ' . $this->evenement['historique_id']);
		
		// On exécute la modification
		if ($query->execute())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}