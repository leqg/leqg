<?php
	$infos = $_POST;
	
	if ($infos['type'] == 'email')
	{
		$coordonnees = strtolower($infos['coordonnees']);
	}
	// Si c'est un numéro de téléphone, on le formate pour la base de données
	else
	{
		$coordonnees = preg_replace('`[^0-9]`', '', $infos['coordonnees']);
	}
	
	// On ouvre la fiche du contact demandé
	$contact = new contact($infos['contact']);
	
	// On ajoute les données à la base de données
	$contact->ajoutCoordonnees($infos['type'], $coordonnees);

?>