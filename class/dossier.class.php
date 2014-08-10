<?php

class dossier extends core {
	
	// Définition des propriétés
	private $db; // Lien vers la base MySQL
	private $compte; // ID du compte utilisant la classe
	private $dossier; // tableau des informations disponibles à propos de la tâche ouverte
	
	
	// Définition des méthodes
	
	public	function __construct($db, $compte) {
		$this->db = $db;
		$this->compte = $compte;
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
}