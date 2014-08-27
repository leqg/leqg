<?php
	
	// On récupère les informations
	$info = $_POST;
	
	// On créé une mission contenant les immeubles
	$id = $mission->creation('boite', $info['ville'], $info['rue'], $info['immeubles']);
	
	// On redirige vers la page de la mission
	$core->tpl_go_to('boite', array('action' => 'mission', 'mission' => $id), true);

?>