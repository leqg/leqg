<?php

	// On récupère l'information
	$information = $_POST;
	
	$query = 'UPDATE reglages SET valeur = "' . $information['expediteur'] . '" WHERE nom = "sms-expediteur"';
	$sql = $db->query($query);
	
	$core->tpl_go_to('sms', array('action' => 'reglages'), true); 
?>