<?php

	// On récupère les informations envoyées par POST
	$ville = $_POST['ville'];
	$rue = $_POST['rue'];
	$immeuble = $_POST['immeuble'];
	$contact = $_POST['contact'];
	
	// On vérifie que les données sont toutes des données numériques
	if (is_numeric($ville) && is_numeric($rue) && is_numeric($immeuble)) :
	
		// On récupère les tableaux d'informations nécessaires
			$immeuble = $carto->immeuble($immeuble);
	
		// On lance la fonction de modification de l'adresse pour la fiche demandée
			$fiche->modificationAdresse($contact, $immeuble);
			
			echo '1';

	endif;
?>
