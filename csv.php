<?php

require_once 'includes.php';

$data = $csv->lectureFichier('insee.csv');

$row = 0;
foreach ($data as $line) {
    /*
    [0] => Commune
        [1] => Codepos
        [2] => Departement
        [3] => INSEE	
    */
    
    // On vérifie qu'il ne s'agit pas de la première ligne
    if ($row > 0) :    
        // On cherche l'ID de la commune correspondante
        $query = 'SELECT commune_id FROM communes WHERE CONCAT(departement_id, commune_numero) = ' . $line[3];
        $sql = $db->query($query);
        if ($sql->num_rows == 1) :
            $row = $sql->fetch_array();
            $id = $row[0];
            
            // On enregistre le code postal dans la base de données
            $query = 'INSERT INTO codes_postaux (code_postal, commune_id) VALUES (' . $line[1] . ', ' . $id . ')';
            $db->query($query);
     else :
            // On enregistre le code postal dans la base de données sans la ville
            $query = 'INSERT INTO codes_postaux (code_postal, commune_id) VALUES (' . $line[1] . ', 0)';
            $db->query($query);
     endif;
        
        $row++;
    
        // Sinon, on la saute
    else : $row++; 
    endif;
}

?>Fin!