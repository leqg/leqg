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
}