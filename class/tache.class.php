<?php

/*
	Classe du noyau central du système LeQG
*/


class tache extends core {
	
	// Définition des propriétés
	private $db; // Lien vers la base MySQL
	private $compte; // ID du compte utilisant la classe
	private $tache; // tableau des informations disponibles à propos de la tâche ouverte
	
	
	// Définition des méthodes
	
	public function __construct($db, $compte) {
		$this->db = $db;
		$this->compte = $compte;
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
		
}	
?>