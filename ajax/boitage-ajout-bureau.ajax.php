<?php

	// On récupère les informations
	$infos = $_POST;
	
	// On récupère des informations sur la mission
	$mission = Boite::informations($infos['mission'])[0];
	
	// On effectue l'ajout de la rue à la mission
	Boite::ajoutBureau($infos['bureau'], $mission['mission_id']);
	
?>