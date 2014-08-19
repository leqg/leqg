<?php
	
	// On traite les informations
	if (!empty($_POST['nom'])) :
	
		// On formate l'information
		$nom = $core->securisation_string($_POST['nom']);

		// On prépare la requête
		$query = 'UPDATE		contacts
				  SET		contact_nom = "' . $nom . '"
				  WHERE		contact_id = ' . $_GET['fiche'];
		
		// On exécute la requête
		$db->query($query);

	endif;
	
	if (!empty($_POST['nomUsage'])) :
	
		// On formate l'information
		$nomUsage = $core->securisation_string($_POST['nomUsage']);

		// On prépare la requête
		$query = 'UPDATE		contacts
				  SET		contact_nom_usage = "' . $nomUsage . '"
				  WHERE		contact_id = ' . $_GET['fiche'];
		
		// On exécute la requête
		$db->query($query);

	endif;
	
	if (!empty($_POST['prenoms'])) :
	
		// On formate l'information
		$prenoms = $core->securisation_string($_POST['prenoms']);

		// On prépare la requête
		$query = 'UPDATE		contacts
				  SET		contact_prenoms = "' . $prenoms . '"
				  WHERE		contact_id = ' . $_GET['fiche'];
		
		// On exécute la requête
		$db->query($query);

	endif;
	
	// On redirige
	$core->tpl_go_to('fiche', array('id' => $_GET['fiche']), true);
?>