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
	 * @return	void
	 */
	 
	public	function __construct($db) {
		$this->db = $db;
	}

	
	/**
	 * Cette méthode permet de calculer le nombre de missions disponible actuellement
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	0.1
	 *
	 * @return	int		Nombre de missions disponibles
	 */
	 
	public	function nombre() {
		// On prépare la requête
		$query = 'SELECT	*
				  FROM		`boitage`
				  WHERE		`boitage_statut` = 1
				  AND		( `boitage_deadline` IS NULL OR `boitage_deadline` >= NOW() )';
				  
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
				  FROM		`boitage`
				  WHERE		`boitage_statut` = 1
				  AND		( `boitage_deadline` IS NULL OR `boitage_deadline` >= NOW() )';
				  
		// On effectue la requête et on retourne le nombre de lignes trouvées
		$sql = $this->db->query($query);
		
		return $sql->fetchAll();
	}
}