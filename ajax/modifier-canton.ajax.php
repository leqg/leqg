<?php

	// On récupère les informations
	$bureau = $_GET['bureau'];
	$canton = $_GET['canton'];
	
	// On modifie l'information dans la base de données
	$db->query('UPDATE bureaux SET canton_id = ' . $canton . ' WHERE bureau_id = ' . $bureau);
	
	// On redirige vers la fiche du bureau
	$core->tpl_go_to('carto', array('module' => 'bureaux', 'bureau' => $bureau), true);

?>