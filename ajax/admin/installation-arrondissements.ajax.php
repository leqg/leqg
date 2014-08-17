<?php

$data = $csv->lectureFichier('data/arrondissements.csv');

// On initialise le tableau des clés
$keys = array();

$row = 0;
foreach ($data as $line) {

	// S'il s'agit de la première ligne, on récupère les informations
	if ($row == 0) {
		
		// On fait la boucle des entrées pour récupérer les clés
		foreach ($line as $key) {
			$keys[] = $key;
		}
		
		//$core->debug($keys);
	}
	
	// Sinon, on enregistre les informations dans la base de données
	else {
		// On prépare le tableau des informations
		$information = array();
	
		// On reformate les clés
		foreach ($line as $key => $val) {
			$information[$keys[$key]] = $val;
		}
		
		//$core->debug($information);
		
		// On calcule le numéro identifiant de l'arrondissement
		$id = $information['DEP'].$information['AR'];
		
		// On détermine le nom de l'arrondissement
		if (!empty($information['ARTMIN'])) {
			$nom = str_replace('(', '', $information['ARTMIN']);
			$nom = str_replace(')', '', $nom);
			if ($information['TNCC'] != 5) { $nom .= ' '; }
			$nom .= $information['NCCENR'];
		} else {
			$nom = $information['NCCENR'];
		}
		
		// On prépare l'enregistrement des informations dans la base de données
		$query = 'INSERT INTO `arrondissements` (`arrondissement_id`,
												 `arrondissement_numero`,	
											 	 `departement_id`,
											 	 `arrondissement_nom`,
											 	 `arrondissement_chef_lieu`)
				  VALUES (' . $id . ',
				  		  ' . $information['AR'] . ',
				  		  ' . $information['DEP'] . ',
				  		  "' . htmlentities($nom) . '",
				  		  ' . $information['CHEFLIEU'] . ')';
		
		// On enregistre l'information dans la base de données et on redirige vers les informations principales
		$db->query($query);
	}
	
	// On fini par incrémenter le numéro de la ligne
	$row++;
	
}

?>
Fini!