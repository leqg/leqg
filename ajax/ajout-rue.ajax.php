<?php

	// On récupère la rue entrée
	$rue = $_POST['rue'];
	$ville = $_POST['ville'];
	
	// On entre la rue dans la base de données
	$identifiant = $carto->ajoutRue($ville, $rue);
	
	echo $identifiant;

?>