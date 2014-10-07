<?php

	$infos = $_GET;
	
	// On récupère les rues de la mission avec leurs immeubles
	$rues = $boitage->liste($infos['mission'], 0);
	
	// On récupère les immeubles à faire de notre rue
	$immeubles = $rues[$infos['rue']];
	
	// Pour chaque immeuble, on modifie l'ID en son numéro
	foreach ($immeubles as $key => $immeuble) {
		$i = $carto->immeuble($immeuble);
		
		$immeubles[$key] = $i['numero'];
	}
	
	$core->debug($immeubles, false);
	
	// On tri les résultats
	natsort($immeubles);
	
	$core->debug($immeubles, false);
	
	// On exporte le tout en JSON
	echo json_encode($immeubles);
?>