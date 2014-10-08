<?php

	$infos = $_GET;
	
	// On récupère des informations sur la mission
	$mission = $porte->informations($infos['mission']);
	
	// On récupère les rues de la mission avec leurs immeubles
	$rues = $porte->liste($mission['mission_id'], 0);
	
	// On récupère les immeubles à faire de notre rue
	$immeubles = $rues[$infos['rue']];
	
	// Pour chaque immeuble, on modifie l'ID en son numéro
	foreach ($immeubles as $key => $immeuble) {
		$i = $carto->immeuble($immeuble);
		
		$immeubles[$key] = $i['numero'];
	}
	
	// On tri les résultats
	natsort($immeubles);
	$liste = array();
	
	foreach ($immeubles as $i) {
		$liste[] = $i;
	}
	
	// On exporte le tout en JSON
	echo json_encode($liste);
?>