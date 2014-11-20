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
		$estimation = Contact::listing($tri, true);
		
		echo $estimation;
		
	} else {
		// On retourne une erreur
		return false;
	}
?>