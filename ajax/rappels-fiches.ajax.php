<?php
	// On fait le lien à la BDD
	$link = Configuration::read('db.link');
	
	// On récupère les informations sur la mission
	if (isset($_GET['mission'])) {
		
		// On récupère la liste des rappels fait ou à effectuer
		$query = $link->prepare('SELECT `contact_id` FROM `rappels` WHERE `argumentaire_id` = :mission');
		$query->bindParam(':mission', $_GET['mission']);
		$query->execute();
		$contacts = $query->fetchAll(PDO::FETCH_ASSOC);
		
		// On récupère les informations sur les fiches
		$fiches = array();
		foreach ($contacts as $contact) {
			$fiche = new Contact(md5($contact['contact_id']));
			$fiches[$fiche->get('contact_id')] = $fiche->donnees();
		}
		
		// On retourne toutes les informations sur les fiches trouvées
		echo json_encode($fiches);
	}
?>