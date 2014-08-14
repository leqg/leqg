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
			
			while ( $row = $sql->fetch_assoc() ) $entrees[] = $this->formatage_donnees($row);
			
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
	
	
	// ajout( int , int , string , date , string , string , string ) permet d'ajouter une nouvelle interaction au sein de la base de données
	public	function ajout( $contact , $compte , $type , $date , $lieu , $objet , $notes ) {
		// on formate la date en tableau
		$date = explode('/', $date); 

		// on sécurise les strings texte
		$lieu = $this->securisation_string($lieu);
		$notes = $this->securisation_string($notes);
		$objet = $this->securisation_string($objet);
	
		// on vérifie le format des informations entrées
		if ( is_numeric( $contact ) && is_numeric( $compte ) && is_string($type) && checkdate( $date[1] , $date[0] , $date[2] ) && is_string( $lieu ) && is_string( $objet ) && is_string($notes) ) :

			// On prépare la requête d'ajout des informations à la base de données
			$query = 'INSERT INTO historique (	contact_id,
												compte_id,
												historique_type,
												historique_date,
												historique_lieu,
												historique_objet,
												historique_notes )
										VALUES (' . $contact . ',
												' . $compte . ',
												"' . $type . '",
												"' . $date[2] . '-' . $date[1] . '-' . $date[0] . '",
												"' . $lieu . '",
												"' . $objet . '",
												"' . $notes . '" )';
												
			// On effectue la requête d'ajout à la base de données
			$sql = $this->db->query($query);
			
			// On récupère le numéro ID de l'enregistrement
			$id = $this->db->insert_id;
			
			// On retourne l'ID en question
			return $id;
		
		else : return false; endif;
	}
	
	
	// modification( array ) permet de modifier une interaction selon les paramètres entrés 
	public	function modification( $infos ) {
		// On vérifie que le tableau information en est bien un
		if (!is_array($infos)) return false;
		
		// On reformate les données nécessaires
			// comme la date
			$infos['date'] = explode('/', $infos['date']);
			krsort($infos['date']);
			$infos['date'] = implode('-', $infos['date']);
			
			// comme les strings
			$infos['lieu'] = $this->securisation_string($infos['lieu']);
			$infos['objet'] = $this->securisation_string($infos['objet']);
			$infos['notes'] = $this->securisation_string($infos['notes']);
			
		// On prépare la requête SQL
		$query = 'UPDATE		historique
				  SET		historique_type = "' . $infos['type'] . '",
				  			historique_date = "' . $infos['date'] . '",
				  			historique_lieu = "' . $infos['date'] . '",
				  			historique_objet = "' . $infos['objet'] . '",
				  			historique_notes = "' . $infos['notes'] . '",
				  			compte_id = ' . $this->compte . ',
				  			historique_timestamp = NOW()
				  WHERE		historique_id = ' . $infos['interaction'] . '
				  AND		contact_id = ' . $infos['fiche'];
		
		// On effectue la requête SQL
		if ($this->db->query($query)) return true;
		
		return false;
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
	public	function returnType( $type , $return = false ) {
		$types = array(	'contact'	=> 'Entrevue',
						'telephone'	=> 'Entretien téléphonique',
						'email'		=> 'Échange électronique',
						'courrier'	=> 'Correspondance',
						'autre'		=> 'Autre' );
				
		$retour = $types[$type];
		
		if ($return === true) : return $retour; else : echo $retour; endif;
	}
	
	
	// elementActuel( bool ) recherche l'élément d'historique ouvert actuellement, en récupérant par exemple l'information à travers la variable GET
	public	function elementActuel( $return = false ) {
		// On récupère l'information de la variable GET
		$element = $_GET['interaction'];
		
		if (is_numeric($element)) :
			if ($return === true) : return $element; else : echo $element; endif;
		else : return false; endif;
	}
	
	
	// affichageThematiques( string->array , bool , bool , bool ) permet de retourner avec un système de tags les différentes thématiques liées à un élement de l'historique
	public	function affichageThematiques( $themas , $return = false , $lienVersTag = false ,  $parentDejaPresent = true ) {
		// On vérifie que l'entrée est bien un tableau une fois restructurée
		$tags = explode(',', $themas);
		
		if ( is_array( $tags ) ) :
			
			// On prépare l'affichage
			$affichage = '';
			
			// On affiche le parent s'il n'est pas déjà présent
			if ($parentDejaPresent === false) $affichage .= '<div id="liste-tags">';
			
			// On fait la liste des différentes thématiques pour les afficher en forme de tags
			foreach ($tags as $tag) :
			
				// On regarde si on a demandé un lien
				if ($lienVersTag === true) $affichage .= '<a href="' . $this->tpl_get_url('thematiques', $tag) . '">';
				
				// On affiche le tag
				$affichage .= '<span class="tag">' . $tag . '</span>';
				
				// On regarde si on doit fermer le lien demandé
				if ($lienVersTag === true) $affichage .= '</a>';
			
			endforeach;
			
			// On ferme le parent s'il n'est pas déjà présent
			if ($parentDejaPresent === false) $affichage .= '</div>';
			
			// Si on demande un return, on le lance, sinon un echo
			if ($return) : return $affichage; else : echo $affichage; endif;
			
		else : return false; endif;
	} 
}