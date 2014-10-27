<?php

	// On récupère les informations
	$infos = $_POST;
	
	// On récupère des informations sur la mission
	$mission = $porte->informations($infos['mission']);
	
	// On effectue l'ajout de la rue à la mission
	$porte->ajoutBureau($infos['bureau'], $mission['mission_id']);
	
?>