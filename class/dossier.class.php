<?php

class dossier extends core {
	
// Définition des propriétés
	private $db; // Lien vers la base MySQL
	private $compte; // ID du compte utilisant la classe
	private $url; // Domaine du serveur
	private $dossier;
	
	
// Définition des méthodes
	
	public	function __construct($db, $compte, $url) {
		$this->db = $db;
		$this->compte = $compte;
		$this->url = $url;
	}
	
	
	// recherche( bool ) est la méthode de recherche des dossiers ouverts actuellement
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
	
	
	// rechercheParFiche( int ) est la méthode permettant de rechercher les dossiers rattachés à une fiche
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
	
	
	// nombre( int ) est la méthode permettant de savoir combien de dossiers sont ouvert pour une fiche demandée
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
	
	
	// creation_rapide( int , string , string ) est une méthode permettant la création rapide d'un dossier
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
	
	
	// lierInteraction( int , int ) permet de lier ensemble une interaction et un dossier
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
	
	
	// lierFiche( int , int ) permet de lier ensemble une fiche et un dossier
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
			$this->db->query('UPDATE dossiers SET dossier_contacts = "' . $contacts . '" WHERE dossier_id = ' . $dossier);
		}
		
		return true;
	}
	
	
	// supprimerLiaisonInteraction( int ) permet de supprimer la liaison entre une fiche interaction et un dossier
	public	function supprimerLiaisonInteraction( $id ) {
		if (!is_numeric($id)) return false;
		
		$query = 'UPDATE historique SET dossier_id = NULL WHERE historique_id = ' . $id;
		$this->db->query($query);
		
		return true;
	}
}