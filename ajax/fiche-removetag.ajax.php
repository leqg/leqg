<?php

	// On récupère les informations
	$infos = $_POST;
	
	// On retraite le tag entré
	$infos['tag'] = $core->securisation_string($infos['tag']);
	
	// On cherche les tags déjà présent pour la fiche
	$fiche->acces($infos['fiche'], true);
	
	// On récupère un tableau des tags
	$tags = explode(',', $fiche->get_infos('tags'));
		
	// On vérifie que le tag ajouté n'existe pas déjà et qu'il n'est pas vide
	if (in_array($infos['tag'], $tags) && !empty($infos['tag'])) :
		
		// On essaye de récupérer la clé correspondante au tag recherché
		$key = array_search($infos['tag'], $tags);
		
		// On supprime l'entrée trouvée
		if ($key !== false) unset($tags[$key]);
		
		// On prépare le tableau pour l'enregistrement
		$tags = trim(implode(',', $tags), ',');
		
		// On enregistre le nouveau tableau des tags dans la base de données
		$db->query('UPDATE `contacts` SET `contact_tags` = "' . $tags . '" WHERE `contact_id` = ' . $infos['fiche']);
	
	endif;

?>