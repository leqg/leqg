<?php

	// On récupère les informations envoyées
	$infos = $_POST;
	
	// On enregistre les informations
	if ($infos['type'] == 'boitage') {
		$boitage->reporting($infos['mission'], $infos['immeuble'], $infos['statut']);
	} else {
		//$boitage->reporting();
	}

?>