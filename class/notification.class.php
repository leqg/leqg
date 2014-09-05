<?php

class notification extends core {
	
	
// Définition des propriétés
	private $db; // Lien vers la base MySQL
	private $compte; // ID du compte utilisant la classe
	private $url; // Domaine du serveur
	
	
// Définition des méthodes
	
	public	function __construct($db, $compte, $url) {
		$this->db = $db;
		$this->compte = $compte;
		$this->url = $url;
	}
	
	
	// nombre( [ int ] ) renvoit le nombre de notification actuelle pour un utilisateur sélectionné
	public	function nombre( $user = null ) {
		if (is_null($user) || !is_numeric($user)) $user = $this->compte;
		
		// On recherche d'abord toutes les tâches en cours pour un utilisateur sélectionné
		$query = 'SELECT * FROM `taches` WHERE `compte_id` = ' . $user . ' AND tache_terminee = 0';
		$sql = $this->db->query($query);
		$taches = $sql->num_rows;
		
		
		// On calcule enfin le nombre de notifications à afficher et on retourne ce nombre
		$notifications = $taches;
		return $notifications;
	}
	
}	
?>