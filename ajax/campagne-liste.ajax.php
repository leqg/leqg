<?php
	if (isset($_GET['campagne'])) {
		// On récupère les informations sur la mission
		$campagne = new Campagne($_GET['campagne']);
		
		// On récupère la liste des ids de la mission
		$contacts = $campagne->contacts();
		
		// On renvoit en JSON
		echo json_encode($contacts);
	}
?>