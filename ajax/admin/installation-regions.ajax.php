<?php

$data = $csv->lectureFichier('data/regions.csv');

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
    }
    
    // Sinon, on enregistre les informations dans la base de données
    else {
        // On prépare le tableau des informations
        $region = array();
    
        // On reformate les clés
        foreach ($line as $key => $val) {
            $region[$keys[$key]] = $val;
        }
        
        // On prépare l'enregistrement des informations dans la base de données
        $query = 'INSERT INTO `regions` (`region_id`,
										 `region_nom`,
										 `region_chef_lieu`)
				  VALUES (' . $region['REGION'] . ',
				  		  "' . htmlentities($region['NCCENR']) . '",
				  		  ' . $region['CHEFLIEU'] . ')';
        
        // On enregistre l'information dans la base de données et on redirige vers les informations principales
        $db->query($query);
    }
    
    // On fini par incrémenter le numéro de la ligne
    $row++;
    
}

?>
Fini!