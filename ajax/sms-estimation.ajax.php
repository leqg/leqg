<?php
	
	// On récupère les informations
	$formulaire = $_POST;
	
	// À partir de ces informations, on lance la simulation d'export
	$fiches = $fiche->export($formulaire, true, true);
	
	// On calcule le nombre de fiches
	$nombre = count($fiches);
	
	// S'il existe des fiches, on ajoute l'information à la base de données
	if ($nombre > 0) {
		$liste = implode(',', $fiches);
		
		$query = 'UPDATE	envois
				  SET		envoi_destinataire = "' . $liste . '"
				  WHERE		envoi_id = ' . $formulaire['campagne'];
				  
		$db->query($query);
	}
	
	// On imprime le résultat
	echo number_format($nombre, 0, ',', ' ');
	
?>