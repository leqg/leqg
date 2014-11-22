<?php
	// On se connecte à la base de données
	$link = Configuration::read('db.link');
	
	// On récupère les informations
	$var = $_GET;
	
	// On retraite les critères complexes
	$var['criteres'] = trim($var['criteres'], ';');
	
	// On récupère la liste des contacts concernés
	$contacts = Contact::listing($var, 0, false);
	
	// Pour chaque contact, on l'ajoute à la mission
	foreach($contacts as $contact) {
		// On exécute la requête d'ajout du rappel
		$query = $link->prepare('INSERT INTO `rappels` (`argumentaire_id`, `contact_id`) VALUES (:argumentaire, :contact)');
		$query->bindParam(':argumentaire', $var['mission'], PDO::PARAM_INT);
		$query->bindParam(':contact', $contact, PDO::PARAM_INT);
		$query->execute();
	}
?>