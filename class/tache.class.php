<?php

class tache extends core {
	
	
// Définition des propriétés
	private $db; // Lien vers la base MySQL
	private $compte; // ID du compte utilisant la classe
	private $url; // Domaine du serveur
	private $tache; // tableau des informations disponibles à propos de la tâche ouverte
	
	
// Définition des méthodes
	
	public	function __construct($db, $compte, $url) {
		$this->db = $db;
		$this->compte = $compte;
		$this->url = $url;
	}
	
	
	// creation( string , int ) est la méthode de création d'une nouvelle tâche (avec la possibilité d'y assigner de suite une fiche
	public	function creation( $contenu , $deadline , $contacts , $destinataire ) {
		// On vérifie le contenu des données
		$contenu = $this->securisation_string($contenu);
		$date = explode('/', $deadline);
		if (!checkdate($date[1], $date[0], $date[2])) { $deadline = null; } else { $deadline = mktime(0, 0, 0, $date[1], $date[0], $date[2]); }
	
		// On prépare la requête
		$query = 'INSERT INTO taches (	compte_id,
										tache_description,
										tache_deadline,
										tache_contacts, 
										tache_destinataire )
				   VALUES	(	"' . $_COOKIE['leqg-user'] . '",
				   				"' . $contenu . '",
				   				"' . date( 'Y-m-d' , $deadline ) . '",
				   				"' . $contacts . '",
				   				"' . $destinataire . '" ) ';
		
		// On effectue la requête
		$sql = $this->db->query($query);
		
		// On renvoit l'ID inscrit dans la base de données
		return $this->db->insert_id;
	}
	
	
	// recherche( int , bool ) est la méthode de recherche des tâches associées à un compte membre, elle renvoit un tableau contenant les différentes tâches à réaliser
	public	function recherche( $id , $toutes = false ) {
		if ( is_numeric( $id ) ) :
			// On prépare la requête de récupération de toutes les tâches associées à un membre
			$query = 'SELECT * FROM taches WHERE tache_destinataire LIKE "%' . $id . '%"';
			
			// Si on ne demande pas l'affichage des tâches même terminées, on adapte la requête
			if ( $toutes == false ) $query = $query . " AND tache_terminee = 0";
			
			// On prépare le tableau contenant les différentes tâches
			$taches = array();
			
			// On effectue la requête et on vérifie que les requêtes extraites correspondent bien au compte demandé
			$sql = $this->db->query( $query );
			while ( $row = $sql->fetch_assoc() ) :
				$destinataires = explode(',', $row['tache_destinataire']);
				
				if ( in_array( $id , $destinataires ) ) $taches[] = $this->formatage_donnees( $row );
			endwhile;
			
			return $taches;
			
		else : return false; endif;
	}
		
}	
?>