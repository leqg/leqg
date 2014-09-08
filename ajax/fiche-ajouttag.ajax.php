<?php

	// On récupère les informations
	$infos = $_POST;
	
	// On cherche les tags déjà présent pour la fiche
	$fiche->acces($infos['fiche'], true);
	
	// On récupère un tableau des tags
	$tags = explode(',', $fiche->get_infos('tags'));
	
	// On vérifie que le tag ajouté n'existe pas déjà et que le tag n'est pas vide
	if (!in_array($infos['tag'], $tags) && !empty($infos['tag'])) :
	
		// On ajout le tag à la liste
		$tags[] = $core->securisation_string($infos['tag']);
		
		// On transforme en string le tableau $tags
		$tags = trim(implode(',', $tags), ',');
	
		// On enregistre la nouvelle liste des tags
		$db->query('UPDATE `contacts` SET `contact_tags` = "' . $tags . '" WHERE `contact_id` = ' . $infos['fiche']);
	
	endif;

?>