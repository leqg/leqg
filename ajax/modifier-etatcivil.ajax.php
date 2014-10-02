<?php
	
	// On récupère les informations entrées
	if ($_POST) {
		$nom = $core->securisation_string($_POST['nom']);
		$nomUsage = $core->securisation_string($_POST['nomUsage']);
		$prenoms = $core->securisation_string($_POST['prenoms']);
		
		// On prépare la requête
		$query = 'UPDATE	contacts
				  SET		contact_nom = "' . $nom . '",
				  			contact_nom_usage = "' . $nomUsage . '",
				  			contact_prenoms = "' . $prenoms . '"
				  WHERE		contact_id = ' . $_GET['fiche'];
		// On exécute la requête
		$db->query($query);
	}

	$core->tpl_go_to('fiche', array('id' => $_GET['fiche']), true);
?>