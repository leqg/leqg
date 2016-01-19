<?php

$data = $csv->lectureFichier('data/departements.csv');

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
        
        //$core-debug($keys);
    }
    
    // Sinon, on enregistre les informations dans la base de données
    else {
        // On prépare le tableau des informations
        $information = array();
    
        // On reformate les clés
        foreach ($line as $key => $val) {
            $information[$keys[$key]] = $val;
        }
        
        //$core-debug($information);
        
        // On prépare l'enregistrement des informations dans la base de données
        $query = 'INSERT INTO `departements` (`departement_id`,
											  `region_id`,
											  `departement_nom`,
											  `departement_chef_lieu`)
				  VALUES (' . $information['DEP'] . ',
				  		  ' . $information['REGION'] . ',
				  		  "' . htmlentities($information['NCCENR']) . '",
				  		  ' . $information['CHEFLIEU'] . ')';
        
        // On enregistre l'information dans la base de données et on redirige vers les informations principales
        $db->query($query);
    }
    
    // On fini par incrémenter le numéro de la ligne
    $row++;
    
}

?>
Fini!