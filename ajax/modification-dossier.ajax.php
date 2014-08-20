<?php

	// On récupère les données du formulaire
	$nom = $_POST['nom'];
	$description = $_POST['description'];
	$dossier = $_POST['dossier'];
	
	// On enregistre les données
	$query = 'UPDATE	dossiers
			  SET		dossier_nom = "' . $nom . '",
			  			dossier_description = "' .$description. '"
			  WHERE		dossier_id = ' . $dossier;
	
	// On lance la requête
	$db->query($query);
	
	// On redirige vers le dossier
	$core->tpl_go_to('dossier', array('id' => $dossier), true);

?>