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
	public	function creation( $info ) {
		if (!is_array($info)) return false;
	
		// On prépare la requête
		$query = 'INSERT INTO taches (	createur_id,
										compte_id,
										dossier_id,
										historique_id,
										tache_description,
										tache_deadline )
				   VALUES	(	"' . $_COOKIE['leqg-user'] . '",
				   				"' . $info['destinataire'] . '",
				   				"' . $info['dossier'] . '",
				   				"' . $info['interaction'] . '",
				   				"' . $info['description'] . '",
				   				"' . $info['deadline'] . '" )';
		
		// On effectue la requête
		$sql = $this->db->query($query);
		
		// On renvoit l'ID inscrit dans la base de données
		return $this->db->insert_id;
	}
	
	
	// recherche( int , bool ) est la méthode de recherche des tâches associées à un compte membre, elle renvoit un tableau contenant les différentes tâches à réaliser
	public	function recherche( $id , $toutes = false ) {
		if ( is_numeric( $id ) ) :
			// On prépare la requête de récupération de toutes les tâches associées à un membre
			$query = 'SELECT * FROM taches WHERE compte_id = ' . $id;
			
			// Si on ne demande pas l'affichage des tâches même terminées, on adapte la requête
			if ( $toutes == false ) $query = $query . " AND tache_terminee = 0";
			
			// On prépare le tableau contenant les différentes tâches
			$taches = array();
			
			// On effectue la requête et on vérifie que les requêtes extraites correspondent bien au compte demandé
			$sql = $this->db->query( $query );
			while ( $row = $sql->fetch_assoc() ) :
				$taches[] = $this->formatage_donnees( $row );
			endwhile;
			
			return $taches;
			
		else : return false; endif;
	}
	
	
	// listeParInteraction( int ) permet d'avoir la liste de toutes les tâches pour une interaction donnée
	public	function listeParInteraction( $interaction ) {
		if (!is_numeric($interaction)) return false;
		
		// On exécute la recherche
		$query = 'SELECT * FROM taches WHERE tache_terminee = 0 AND historique_id = ' . $interaction;
		$sql = $this->db->query($query);
		
		// On initialise la liste des tâches
		$taches = array();
		
		// On affecte les résultats au tableau
		while ($row = $sql->fetch_assoc()) $taches[] = $this->formatage_donnees($row);
		
		// On retourne la liste des tâches
		return $taches;
	}
	
	
	// fermetureTache( int ) permet de fermer une tâche
	public	function fermetureTache( $task ) {
		if (!is_numeric($task)) return false;
		
		$query = 'UPDATE taches SET tache_terminee = 1 WHERE tache_id = ' . $task;
		$this->db->query($query);
		
		return true;
	}
		
}	
?>