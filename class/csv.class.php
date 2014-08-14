<?php

/*
	Classe du noyau de traitement des données CSV du site
*/


class csv extends core {
	
// Définition des propriétés
	private $db; // Lien vers la base MySQL
	private $url; // Domaine du serveur
	
	
// Définition des méthodes
	
	public	function __construct($db, $url) {
		$this->db = $db;
		$this->url = $url;
	}
		
	
// Méthodes liées à la classe sélectionnée
	
	// lectureFichier( string , string ) permet de traiter les données d'un fichier CSV dans un tableau
	public	function lectureFichier( $fichier , $separateur = ';' ) {
		
		// On défini les données de démarrage
		$row = 0;
		$line = array();
		$data = array();
		$head = array();
		
		// On ouvre le fichier, uniquement en lecture
		$file = fopen($fichier, 'r');
		
		// On calcule la taille du fichier
		$size = filesize($fichier) + 1;
		
		// On fait une boucle pour chaque ligne du fichier
		while ($line = fgetcsv($file, $size, $separateur)) :
		
			// On affecte les données de la ligne au tableau général
			$data[$row] = $line;
			$row++;
		
		endwhile;
		
		// On ferme le fichier
		fclose($file);
		
		// On retourne le tableau contenant les données du fichier
		return $data;
	}
}