<?php
	
	// On récupère les informations
	$info = $_POST;
	
	// On lance la création de la mission avec les informations récupérées
	$mission = Mission::creation('porte', $info);
	
	// On redirige vers la page de la mission
	Core::tpl_go_to('mission', array('code' => md5($mission)), true);

?>