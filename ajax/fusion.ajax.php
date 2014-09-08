<?php

	// On récupère les informations
	$infos = $_POST;
	
	// On récupère les informations
	$query1 = 'SELECT * FROM `contacts` WHERE `contact_id` = ' . $infos['fiche1'];
	$query2 = 'SELECT * FROM `contacts` WHERE `contact_id` = ' . $infos['fiche2'];
	$sql1 = $db->query($query1);
	$sql2 = $db->query($query2);
	$contact1 = $sql1->fetch_assoc();
	$contact2 = $sql2->fetch_assoc();
	$contact1 = $core->formatage_donnees($contact1);
	$contact2 = $core->formatage_donnees($contact2);


	// On regarde si la première fiche ou la deuxième fiche correspond à une fiche électeur
	if ($contact1['electeur'] == 1 || $contact2['electeur'] == 1) :
	
		// On regarde quelle est la fiche à conserver, et la fiche à supprimer
		if ($contact1['electeur'] == 1 && $contact2['electeur'] == 0) :
			$on = $contact1['id'];
			$off = $contact2['id'];
		elseif ($contact1['contact_electeur'] == 0 && $contact2['contact_electeur'] == 1) :
			$on = $contact2['id'];
			$off = $contact1['id'];
		else :
			$on = $contact1['id'];
			$off = $contact2['id'];
		endif;
		
		// On récupère les tags de la fiche qui va disparaître pour les ajouter à la nouvelle fiche
		$tags1 = explode(',', $contact1['tags']);
		$tags2 = explode(',', $contact2['tags']);
		$tags = array_merge($tags1, $tags2);
		
		// On trie les tags, on vire les doublons et on enregistre le tout
		$tags = array_unique($tags);
		
		// On transforme les tags en string
		$tags = implode(',', $tags);
		
		// On update la fiche restante
		$query = 'UPDATE	`contacts`
				  SET		`adresse_id` = ' . $infos['adresse'] . ',
				  			`contact_email` = "' . $infos['email'] . '",
				  			`contact_telephone` = "' . $infos['fixe'] . '",
				  			`contact_mobile` = "' . $infos['mobile'] . '",
				  			`contact_tags` = "' . $tags . '"
				  WHERE		`contact_id` = ' . $on;
		$db->query($query);
	
		// On supprime l'autre fiche
		$query = 'DELETE FROM `contacts` WHERE `contact_id` = ' . $off;
		$db->query($query);
		
		// On se déplace vers la nouvelle fiche fusionnée
		$core->tpl_go_to('fiche', array('id' => $on), true);
	
	endif;

?>