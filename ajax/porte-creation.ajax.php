<?php
	
	// On récupère les informations
	$info = $_POST;
	
	// On lance la création de la mission avec les informations récupérées
	$mission = $porte->creation($info);
	
	// On redirige vers la page de la mission
	$core->tpl_go_to('porte', array('mission' => md5($mission)), true);

?>