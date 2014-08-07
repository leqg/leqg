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
			$query = 'SELECT * FROM historique WHERE contact_id = ' . $fiche . ' ORDER BY historique_timestamp DESC';
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
	
	
	// ajout( int , int , string , date , string , string->array , string ) permet d'ajouter une nouvelle interaction au sein de la base de données
	public	function ajout( $contact , $compte , $type , $date , $lieu , $thematiques , $notes ) {
		// on formate les thématiques et la date en tableau
		$thematiques = explode(',', $this->securisation_string($thematiques));
		$date = explode('/', $date);

		// on sécurise les strings texte
		$lieu = $this->securisation_string($lieu);
		$notes = $this->securisation_string($notes);
	
		// on vérifie le format des informations entrées
		if ( is_numeric( $contact ) && is_numeric( $compte ) && is_string($type) && checkdate( $date[1] , $date[0] , $date[2] ) && is_string( $lieu ) && is_array($thematiques) && is_string($notes) ) :

			// On prépare la requête d'ajout des informations à la base de données
			$query = 'INSERT INTO historique (	contact_id,
												compte_id,
												historique_type,
												historique_date,
												historique_lieu,
												historique_thematiques,
												historique_notes )
										VALUES (' . $contact . ',
												' . $compte . ',
												"' . $type . '",
												"' . $date[2] . '-' . $date[1] . '-' . $date[0] . '",
												"' . $lieu . '",
												"' . implode(',', $thematiques) . '",
												"' . $notes . '" )';
												
			// On effectue la requête d'ajout à la base de données
			$sql = $this->db->query($query);
			
			// On récupère le numéro ID de l'enregistrement
			$id = $this->db->insert_id;
			
			// On retourne l'ID en question
			return $id;
		
		else : return false; endif;
	}
	
	
	// recherche( int ) permet de récupérer les informations liées à une interaction recherchée
	public	function recherche( $id ) {
		// on vérifie que l'ID demandé est formaté correctement
		if ( is_numeric( $id ) ) :
		
			// on effectue la recherche des informations liées
			$query = 'SELECT * FROM historique WHERE historique_id = ' . $id;
			$sql = $this->db->query($query);
			$infos = $this->formatage_donnees($sql->fetch_assoc());
			
			// On retourne le tableau des résultats
			return $infos;
		
		else : return false; endif;
	}
	
	
	// returnType( string [, bool] ) permet de retourner un affichage correct et compréhensible de tous du type d'événement entré dans l'historique
	public	function returnType( $type , $return = true ) {
		$types = array(	'contact'	=> 'Entrevue',
						'telephone'	=> 'Entretien téléphonique',
						'email'		=> 'Échange électronique',
						'courrier'	=> 'Correspondance',
						'autre'		=> 'Autre' );
				
		$retour = $types[$type];
		
		if ($return === true) : return $retour; else : echo $retour; endif;
	}
}