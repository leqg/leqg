<?php
	// On récupère les données envoyées par le formulaire
	if (isset($_GET)) {
		// On retraite sous forme d'un tableau les données envoyées par le formulaire
		$tri = array(
			'email' => $_GET['email'],
			'mobile' => $_GET['mobile'],
			'fixe' => $_GET['fixe'],
			'electeur' => $_GET['electeur'],
			'criteres' => trim($_GET['criteres'], ';')
		);
		
		// On charge les fiches correspondantes
		$contacts = Contact::listing($tri, $_GET['debut']);
		
		// On prépare l'array de résultat
		$fiches = array();
		
		// Pour chaque identifiant trouvé, on cherche l'ensemble des données afférentes
		foreach ($contacts as $c) {
			$contact = new Contact(md5($c));
			$fiches[$c] = $contact->donnees();
		}
		
		// On transforme le tableau final en JSON
		$json = json_encode($fiches);
		
		// On retourne le tableau
		echo $json;
		
	} else {
		// On retourne une erreur
		return false;
	}
?>