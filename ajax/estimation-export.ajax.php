<?php
	
	// On récupère les informations
	$formulaire = $_POST;
	
	// À partir de ces informations, on lance la simulation d'export
	$nombre = $fiche->export($formulaire, true);
	
	// On imprime le résultat
	echo number_format($nombre, 0, ',', ' ');
	
?>