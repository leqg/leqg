<?php

class mission extends core {
	
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
	
	
	// chargement( int ) permet de charger les informations dans une variable concernant une mission
	public	function chargement( $parcours ) {
		$query = 'SELECT * FROM missions WHERE mission_id = ' . $parcours;
		$sql = $this->db->query($query);
		$info = $sql->fetch_assoc();
		
		return $this->formatage_donnees($info);
	}
	
	
	// creation( string , int , int , array ) permet de créer une mission
	public	function creation( $type , $ville , $rue , $immeubles ) {
	
		// Si c'est pour du porte à porte, à faire parle d'électeurs alors, on fait la liste des électeurs par immeuble
		if ($type == 'porte') {
		
			$afaire = array();
			foreach ($immeubles as $immeuble) {
				$query = 'SELECT	*
						  FROM		contacts
						  WHERE		immeuble_id = ' . $immeuble . '
						  AND		contact_electeur  = 1
						  ORDER BY	contact_nom, contact_nom_usage, contact_prenoms ASC';
			
				// On effectue la requête BDD et on affiche les résultats dans un tableau $electeurs
				$electeurs = array();
				$sql = $this->db->query($query);
				while ($row = $sql->fetch_assoc()) $electeurs[] = $this->formatage_donnees($row);
	
				foreach ($electeurs as $electeur) $afaire[] = $electeur['id'];
			}
			
		} elseif ($type == 'boite') {
			
			$afaire = array();
			foreach($immeubles as $immeuble) {
				$afaire[] = $immeuble;
			}
			
		}
		
		// On prépare le tableau
		$buildings = implode(',', $immeubles);
		$afaire = implode(',', $afaire);
		
		// On enregistre dans la base de données les informations
		$query = 'INSERT INTO	missions (`ville_id`,
										  `rue_id`,
										  `mission_type`,
										  `mission_creation`,
										  `mission_immeubles`,
										  `mission_a_faire`)
				  VALUES (' . $ville . ',
				  		  ' . $rue . ',
				  		  "' . $type . '",
				  		  ' . date('Y-m-d') . ',
				  		  "' . $buildings . '",
				  		  "' . $afaire . '")';
		
		$this->db->query($query);
		
		return $this->db->insert_id;
	}
	
}