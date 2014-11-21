<?php
	
	// On récupère les informations
	$info = $_POST;
	
	// On lance la création de la mission avec les informations récupérées
	$mission = Boite::creation($info);
	
	// On redirige vers la page de la mission
	Core::tpl_go_to('boite', array('mission' => md5($mission)), true);

?>