<?php

	// On récupère les informations
	$formulaire = $_POST;
	
	// On réalise l'export et on récupère le nom du fichier
	$f = $fiche->export($formulaire);
	
	$core->debug($f);

?>