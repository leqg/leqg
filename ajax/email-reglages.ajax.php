<?php

	// On récupère l'information
	$information = $_POST;
	
	$query = 'UPDATE reglages SET valeur = "' . $information['expediteur'] . '" WHERE nom = "email-expediteur"';
	$sql = $db->query($query);
	
	$query = 'UPDATE reglages SET valeur = "' . $information['expediteur-email'] . '" WHERE nom = "email-expediteur-adresse"';
	$sql = $db->query($query);
	
	$core->tpl_go_to('email', array('action' => 'reglages'), true); 
?>