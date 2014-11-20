<?php
	if (isset($_GET['campagne'])) {
		// On récupère les informations sur la mission
		$campagne = new Campagne($_GET['campagne']);
		
		// On récupère la liste des informations sur les contacts de la campagne
		$contacts = $campagne->contacts();
		
		// On renvoit en JSON
		echo json_encode($contacts);
	}
?>