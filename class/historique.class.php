<?php

class historique extends core {
	
	// Définition des propriétés
	private $db; // Lien vers la base MySQL
	private $compte; // ID du compte utilisant la classe
	
	
	// Définition des méthodes
	
	public	function __construct($db, $compte) {
		$this->db = $db;
		$this->compte = $compte;
	}
	
	
	// rechercheParFiche( int ) permet d'extraire de la BDD les entrées de l'historique pour une fiche demandée
	public	function rechercheParFiche( $fiche ) {
		// On vérifie que la fiche est bien un nombre (id = numeric)
		if ( is_numeric( $fiche ) ) :
		
			// On effectue la recherche dans la BDD des entrées dans l'historique rattachées à la fiche contact demandée
			$query = 'SELECT * FROM historique WHERE contact_id = ' . $fiche;
			$sql = $this->db->query($query);
			
			// On fait la liste de toutes les entrées pour les affecter dans un tableau
			$entrees = array();
			
			while ( $row = $sql->fetch_assoc() ) $entrees[] = $row;
			
			return $entrees;
		
		else : return false; endif;
	}
	
	
	// nombre( int ) permet d'extraire le nombres d'entrées dans l'historique du compte rattachés à la fiche contact demandée
	public	function nombre( $fiche ) {
		// On vérifie que la fiche est bien un nombre
		if ( is_numeric( $fiche ) ) :
		
			// On charge les entrées relatives à la fiche contact
			$entrees = $this->rechercheParFiche( $fiche );
			
			// On compte le nombre d'entrées
			$nombre = count($entrees);
			
			// On retourne le nombre trouvé
			return $nombre;
		
		else : return false; endif;
	}
}