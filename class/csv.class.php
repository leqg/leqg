<?php

/*
	Classe du noyau de traitement des données CSV du site
*/


class carto extends core {
	
// Définition des propriétés
	private $db; // Lien vers la base MySQL
	private $compte; // ID du compte qui utilise à l'instant donné la plateforme
	
	
// Définition des méthodes
	
	public	function __construct($db, $compte) {
		$this->db = $db;
		$this->compte = $compte;
	}
		
	
// Méthodes liées à la classe sélectionnée
	
}