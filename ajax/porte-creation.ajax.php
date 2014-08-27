<?php
	
	// On récupère les informations
	$info = $_POST;
	
	// On créé une mission contenant les immeubles
	$id = $mission->creation('porte', $info['ville'], $info['rue'], $info['immeubles']);
	
	// On redirige vers la page de la mission
	$core->tpl_go_to('porte', array('action' => 'mission', 'mission' => $id), true);

?>