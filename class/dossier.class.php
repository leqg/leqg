<?php
/**
 * Cette classe comprend l'ensemble des méthodes de traitement des dossiers sur le SaaS LeQG
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */


class dossier extends core {
	
	/**
	 * @var	object	$db			Propriété concenant le lien vers la base de données de l'utilisateur
	 * @var	string	$url		Propriété contenant l'URL du serveur
	 * @var	string	$compte		Propriété contenant les informations sur le compte connecté
	 */
	private $db, $url, $compte;
	

	/**
	 * Cette méthode permet la construction de la classe dossier
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	object	$db			Lien vers la base de données de l'utilisateur
	 * @param	string	$compte		Informations concernant l'utilisateur connecté
	 * @param	string	$url		URL du serveur
	 * @return	void
	 */
	 
	public	function __construct($db, $compte, $url) {
		$this->db = $db;
		$this->compte = $compte;
		$this->url = $url;
	}
	

	/**
	 * Cette méthode permet de récupérer la liste des dossiers
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	bool	$tous		Si true, la liste contient également les dossiers fermés
	 * @return	array				Tableau des dossiers enregistrés dans la base de données
	 */
	 
	public	function recherche( $tous = false ) {
		// On prépare la requête de recherche
		$query = 'SELECT * FROM dossiers WHERE dossier_statut = 1';
		
		// On regarde si on extrait tous les dossiers où juste ceux ouverts
		if ( $tous == false ) $query = $query . ' AND dossier_date_fermeture IS NULL';
		
		// On effectue la requête
		$sql = $this->db->query($query);
		
		// On prépare le tableau de rendu
		$rendu = array();
		
		// On affecte les résultats au tableau de rendu
		while( $row = $sql->fetch_assoc() ) $rendu[] = $this->formatage_donnees( $row );
		
		// On retourne le tableau de rendu
		return $rendu;
	}
	

	/**
	 * Cette méthode permet de récupérer la liste des dossiers rattachés à une fiche demandée
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$fiche		ID de la fiche dont on souhaite récupérer les dossiers
	 * @return	array				Tableau des dossiers enregistrés pour la fiche demandée
	 */
	 
	public	function rechercheParFiche( $fiche ) {
		// On vérifie que l'entrée est bien un nombre (ID = numeric only)
		if (is_numeric( $fiche ) ) {
			// On prépare la requête et on l'exécute
			$query = 'SELECT * FROM dossiers WHERE dossier_contacts LIKE "%' .$fiche. '%"';
			$sql = $this->db->query( $query );
			
			// On vérifie que les données sorties de la BDD contiennent bien la fiche
			$dossiers = array();
			
			while ( $row = $sql->fetch_assoc() ) :
			
				// On formate les données issues de row pour faciliter leur compréhension
				$row = $this->formatage_donnees( $row );
				$fiches = explode( ',' , $row['contacts'] );
				
				foreach ( $fiches as $f ) :
				
					if ( $f == $fiche ) $dossiers[] = $row;
				
				endforeach;
			
			endwhile;
			
			// On renvoit les dossiers contenant la fiche
			return $dossiers;
			
		} else {
			return false;
		}
	}
	

	/**
	 * Cette méthode permet de connaître le nombre des dossiers rattachés à une fiche demandée
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$fiche		ID de la fiche dont on souhaite connaître le nombre de dossiers rattachés
	 * @return	array				Tableau des dossiers enregistrés pour la fiche demandée
	 */
	 
	public	function nombre( $fiche ) {
		// On vérifie que l'entrée est bien un nombre (id = int)
		if (is_numeric( $fiche )) :
			// On fait la recherche des dossiers pour la fiche en question
			$dossiers = $this->rechercheParFiche( $fiche );
			
			// On compte le nombre de dossiers renvoyés
			$nombre = count($dossiers);
			
			// On renvoit le nombre de dossiers trouvés
			return $nombre;
		else :
			return false;
		endif;
	}
	

	/**
	 * Cette méthode permet de procéder à la création rapide d'un dossier
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string	$contact		ID de la fiche contact concernée par le dossier
	 * @param	string	$nom			Nom du dossier à créer
	 * @param	string	$description 	Description du dossier à créer
	 * @return	int						ID du dossier créé
	 */
	 
	public	function creation_rapide( $contact , $nom , $description ) {
		// On sécurise les insertions
		if (!is_numeric($contact) && is_string($nom) && is_string($description)) return false;
		$nom = $this->securisation_string($nom);
		$description = $this->securisation_string($description);
		
		// On prépare la requête d'insertion dans la base de données
		$query = 'INSERT INTO dossiers (`dossier_nom`, `dossier_description`, `dossier_contacts`)
				  VALUES ("' . $nom . '",
				  		  "' . $description . '",
				  		  "' . $contact . '")';
				  		  
		// On exécute la requête et on retourne l'ID des données insérées
		$this->db->query($query);
		return $this->db->insert_id;
	}
	

	/**
	 * Cette méthode permet de lier ensemble une interaction et un dossier
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$interaction 	ID de l'interaction concernée
	 * @param	int		$dossier	 	ID du dossier concerné
	 * @return	bool					Réussite ou non de l'opération
	 */
	 
	public	function lierInteraction( $interaction , $dossier ) {
		// On vérifie que tout est bien numérique
		if (!is_numeric($interaction) || !is_numeric($dossier)) return false;
		
		// On prépare la requête SQL
		$query = 'UPDATE historique SET dossier_id = ' . $dossier . ' WHERE historique_id = ' . $interaction;
		
		// On exécute la requête
		if ($this->db->query($query)) {
			return true;
		} else {
			return false;
		}
	}
	

	/**
	 * Cette méthode permet de lier ensemble une fiche et un dossier
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$interaction 	ID de la fiche concernée
	 * @param	int		$dossier	 	ID du dossier concerné
	 * @return	bool					Réussite ou non de l'opération
	 */
	 
	public	function lierFiche( $fiche , $dossier ) {
		// On vérifie que tout est bien numérique
		if (!is_numeric($fiche) || !is_numeric($dossier)) return false;
		
		// On récupère les informations sur le dossier
		$query = 'SELECT * FROM dossiers WHERE dossier_id = ' . $dossier;
		$sql = $this->db->query($query);
		$d = $this->formatage_donnees($sql->fetch_assoc());
		$contacts = explode(',', $d['contacts']);
		if (!in_array($fiche, $contacts)) {
			$contacts[] = $fiche;
			$contacts = implode(',', $contacts);
			$query = 'UPDATE dossiers SET dossier_contacts = "' . $contacts . '" WHERE dossier_id = ' . $dossier;
		}
		
		if ($this->db->query($query)) {
			return true;
		} else {
			return false;
		}
	}
	

	/**
	 * Cette méthode permet de supprimer une liaison entre une interaction et un dossier
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$interaction 	ID de l'interaction concernée
	 * @param	int		$dossier	 	ID du dossier concerné
	 * @return	bool					Réussite ou non de l'opération
	 */
	 
	public	function supprimerLiaisonInteraction( $id ) {
		if (!is_numeric($id)) return false;
		
		$query = 'UPDATE historique SET dossier_id = NULL WHERE historique_id = ' . $id;
		
		if ($this->db->query($query)) {
			return true;
		} else {
			return false;
		}
	}
}