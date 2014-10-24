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

class evenement extends carto
{
	/**
	 * @var	array	$evenement	Tableau des informations connues sur l'événement
	 *							ouvert lors de la construction de la classe
	 * @var object	$link		Lien vers la base de données
	 */
	
	
	/**
	 * Constructeur de la classe Contact
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string	$evenement	Identifiant (hashage SHA256) de l'événement demandé
	 * @param	bool	$securite	Permet de savoir si on doit revenir au module contact
	 *								si aucune fiche contact n'est trouvée
	 *
	 * @result	void
	 */
	 
	public function __construct( $evenement, $securite = false )
	{
		// On commence par paramétrer les données PDO
		$dsn =  'mysql:host=' . Configuration::read('db.host') . 
				';dbname=' . Configuration::read('db.basename');
		$user = Configuration::read('db.user');
		$pass = Configuration::read('db.pass');

		$this->link = new PDO($dsn, $user, $pass);
				
		// On cherche maintenant à savoir s'il existe un contact ayant pour identifiant celui demandé
		$query = $this->link->prepare('SELECT * FROM `historique` WHERE SHA2(`historique_id`, 256) = :evenement');
		$query->bindParam(':evenement', $evenement);
		$query->execute();
		$evenements = $query->fetchAll();
		
		// Si on ne trouve pas d'utilisateur, on retourne vers la page d'accueil du module contact
		if (!count($evenements) && $securite)
		{
			Core::tpl_go_to('contacts', true);
		}
		// Sinon, on affecte les données aux propriétés de l'objet
		else
		{
			$this->evenement = $evenements[0];
		}
	}
}