<?php

	// On récupère les informations
	$infos = $_POST;
	
	// On récupère des informations sur la mission
	$mission = $boitage->informations($infos['mission']);
	
	// On effectue l'ajout de la rue à la mission
	$boitage->ajoutBureau($infos['bureau'], $mission['mission_id']);
	
?>